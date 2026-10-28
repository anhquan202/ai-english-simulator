<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomScenarioRequest;
use App\Models\Character;
use App\Models\Scenario;
use App\Models\ScenarioHint;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use App\Services\CustomStoryGeneratorService;
use App\Services\StoryQuotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomScenarioController extends Controller
{
    public function __construct(
        protected StoryQuotaService $quotaService,
        protected CustomStoryGeneratorService $generatorService,
        protected PracticeSessionRepositoryInterface $sessionRepository
    ) {}

    /**
     * Display the custom story creation form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = auth()->user();
        $canCreate = $this->quotaService->canCreateStory($user);
        $remainingQuota = $this->quotaService->getRemainingQuota($user);

        return view('scenarios.create', [
            'canCreate' => $canCreate,
            'remainingQuota' => $remainingQuota,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created custom scenario and optionally launch practice session.
     */
    public function store(StoreCustomScenarioRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->quotaService->canCreateStory($user)) {
            return back()->withErrors([
                'quota' => 'Bạn đã sử dụng hết hạn ngạch tạo kịch bản cho tháng này.',
            ])->withInput();
        }

        $validated = $request->validated();
        $level = strtoupper((string) $validated['level']);
        $title = (string) $validated['title'];
        $slug = Str::slug($title).'-'.Str::lower(Str::random(6));

        // Create Scenario
        $scenario = Scenario::create([
            'user_id' => $user?->id,
            'slug' => $slug,
            'title' => $title,
            'category' => (string) $validated['category'],
            'location_name' => $validated['location_name'] ?? null,
            'context_note' => Str::limit((string) $validated['detailed_description'], 180),
            'detailed_description' => (string) $validated['detailed_description'],
            'twist_prompt' => (string) ($validated['twist_prompt'] ?? ''),
            'supported_levels' => [$level],
            'estimated_minutes' => 8,
            'is_custom' => true,
            'is_public' => false,
            'is_active' => true,
        ]);

        // Create default Character persona
        $aiTone = $validated['ai_tone'] ?? 'professional';
        $personality = match ($aiTone) {
            'friendly' => 'Thân thiện, kiên nhẫn, hỗ trợ và cởi mở',
            'challenging' => 'Thử thách, phản biện sắc bén và đào sâu tình huống',
            default => 'Chuyên nghiệp, rõ ràng, chuẩn mực và lịch thiệp',
        };

        $character = Character::create([
            'scenario_id' => $scenario->id,
            'name' => (string) $validated['ai_name'],
            'role' => (string) $validated['ai_role'],
            'role_title' => (string) $validated['ai_role'],
            'personality' => $personality,
            'tone_of_voice' => $aiTone,
            'first_message' => (string) ($validated['first_message'] ?: "Hello! Let's get started with our conversation."),
            'system_prompt' => "You are {$validated['ai_name']}, acting as {$validated['ai_role']}. Maintain a {$aiTone} tone and converse naturally in English at level {$level}.",
            'is_default' => true,
        ]);

        // Attach initial ScenarioHints from goals
        $goalsText = (string) ($validated['goals'] ?? '');
        $goals = preg_split('/\r\n|\r|\n/', $goalsText);
        $order = 1;

        if (is_array($goals)) {
            foreach ($goals as $goal) {
                $trimmed = trim((string) $goal);
                if ($trimmed !== '') {
                    ScenarioHint::create([
                        'scenario_id' => $scenario->id,
                        'level' => $level,
                        'intent_label' => $trimmed,
                        'vietnamese_intent' => $trimmed,
                        'sentence_frame' => "I would like to discuss: {$trimmed}",
                        'sort_order' => $order++,
                        'step_order' => $order,
                    ]);
                }
            }
        }

        if ($order === 1) {
            ScenarioHint::create([
                'scenario_id' => $scenario->id,
                'level' => $level,
                'intent_label' => 'Mở đầu cuộc trò chuyện tự nhiên',
                'vietnamese_intent' => 'Mở đầu cuộc trò chuyện tự nhiên',
                'sentence_frame' => "Hello, it's nice to meet you. I'm here to talk about our situation.",
                'sort_order' => 1,
                'step_order' => 1,
            ]);
        }

        // Action routing: Start practice immediately or return to catalog
        $action = $request->input('action');
        if ($action === 'start_practice' || $request->has('start_practice')) {
            $session = $this->sessionRepository->createSession([
                'session_code' => (string) Str::uuid(),
                'user_id' => $user?->id,
                'guest_token' => null,
                'scenario_id' => $scenario->id,
                'character_id' => $character->id,
                'level' => $level,
                'status' => 'in_progress',
                'current_stage' => 'AI_SPEAKING',
            ]);

            return redirect()->route('practice.room', ['sessionCode' => $session->session_code]);
        }

        return redirect()->route('scenarios.index')->with('success', 'Kịch bản tự tạo đã được lưu thành công!');
    }

    /**
     * AJAX endpoint to generate scenario fields from prompt idea.
     */
    public function generateAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['nullable', 'string', 'max:1000'],
        ]);

        $prompt = (string) ($validated['prompt'] ?? '');
        $data = $this->generatorService->generate($prompt);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
