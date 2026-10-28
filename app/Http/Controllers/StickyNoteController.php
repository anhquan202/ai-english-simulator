<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\StickyNote;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StickyNoteController extends Controller
{
    public function __construct(
        protected SpacedRepetitionService $spacedRepetitionService
    ) {}

    /**
     * Display the corkboard view with all sticky notes and review status.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $notes = $user ? $user->stickyNotes()
            ->orderBy('is_mastered')
            ->orderBy('next_review_at')
            ->get() : collect();

        $dueCount = $user ? $user->getDueNotesCount() : 0;
        $masteredCount = $user ? $user->stickyNotes()->where('is_mastered', true)->count() : 0;

        $reminderConfig = [
            'enabled' => (bool) ($user?->reminder_enabled ?? true),
            'selectedTime' => (string) ($user?->reminder_time ?? '20:00'),
            'days' => (array) ($user?->reminder_days ?? ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']),
        ];

        return view('notes.index', [
            'notes' => $notes,
            'dueCount' => $dueCount,
            'masteredCount' => $masteredCount,
            'reminderConfig' => $reminderConfig,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created sticky note.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để lưu giấy note.',
                ], 401);
            }

            return redirect()->route('login');
        }

        $validated = $request->validate([
            'phrase' => ['required', 'string', 'max:1000'],
            'meaning_vi' => ['required', 'string', 'max:1000'],
            'phonetic' => ['nullable', 'string', 'max:255'],
            'user_note' => ['nullable', 'string', 'max:1000'],
            'cultural_tip' => ['nullable', 'string', 'max:1000'],
            'language' => ['nullable', 'string', 'in:en,ja,zh,ko,es'],
            'color' => ['nullable', 'string', 'in:yellow,mint,peach,pink,lavender'],
            'pin_style' => ['nullable', 'string', 'in:thumbtack_brass,thumbtack_silver,thumbtack_red,washi_kraft,washi_caro,paperclip'],
            'stamp_emoji' => ['nullable', 'string', 'max:20'],
            'stamp_name' => ['nullable', 'string', 'max:50'],
            'practice_session_id' => ['nullable', 'integer', 'exists:practice_sessions,id'],
            'dialogue_turn_id' => ['nullable', 'integer', 'exists:dialogue_turns,id'],
        ]);

        $note = StickyNote::create([
            'user_id' => auth()->id(),
            'practice_session_id' => $validated['practice_session_id'] ?? null,
            'dialogue_turn_id' => $validated['dialogue_turn_id'] ?? null,
            'language' => $validated['language'] ?? 'en',
            'phrase' => $validated['phrase'],
            'phonetic' => $validated['phonetic'] ?? null,
            'meaning_vi' => $validated['meaning_vi'],
            'user_note' => $validated['user_note'] ?? null,
            'cultural_tip' => $validated['cultural_tip'] ?? null,
            'color' => $validated['color'] ?? 'yellow',
            'pin_style' => $validated['pin_style'] ?? 'thumbtack_brass',
            'stamp_emoji' => $validated['stamp_emoji'] ?? '🌸',
            'stamp_name' => $validated['stamp_name'] ?? 'Hoa anh đào',
            'review_stage' => 1,
            'next_review_at' => $this->spacedRepetitionService->getInitialReviewDate(),
            'consecutive_correct' => 0,
            'is_mastered' => false,
        ]);

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu giấy note thành công!',
                'note' => $note,
                'due_count' => auth()->user()->getDueNotesCount(),
            ], 201);
        }

        return back()->with('success', 'Đã lưu giấy note thành công!');
    }

    /**
     * Mark a note as reviewed (advancing or resetting Spaced Repetition interval).
     */
    public function review(StickyNote $note, Request $request): JsonResponse|RedirectResponse
    {
        if ($note->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền cập nhật giấy note này.');
        }

        $validated = $request->validate([
            'remembered' => ['required', 'boolean'],
        ]);

        $note->markReviewed((bool) $validated['remembered']);

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'note' => $note,
                'due_count' => auth()->user()->getDueNotesCount(),
            ]);
        }

        return back()->with('success', 'Cập nhật tiến trình ôn tập thành công!');
    }

    /**
     * Remove / unpin a sticky note.
     */
    public function destroy(StickyNote $note, Request $request): JsonResponse|RedirectResponse
    {
        if ($note->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa giấy note này.');
        }

        $note->delete();

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa giấy note.',
                'due_count' => auth()->user()->getDueNotesCount(),
            ]);
        }

        return back()->with('success', 'Đã gỡ giấy note khỏi bảng.');
    }

    /**
     * Update user reminder notification settings.
     */
    public function updateReminder(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $validated = $request->validate([
            'reminder_enabled' => ['nullable', 'boolean'],
            'reminder_time' => ['nullable', 'string', 'max:10'],
            'reminder_days' => ['nullable', 'array'],
        ]);

        $user->reminder_enabled = (bool) ($validated['reminder_enabled'] ?? true);
        if (! empty($validated['reminder_time'])) {
            $user->reminder_time = $validated['reminder_time'];
        }
        if (isset($validated['reminder_days'])) {
            $user->reminder_days = $validated['reminder_days'];
        }
        $user->save();

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu cài đặt nhắc lịch ôn tập!',
            ]);
        }

        return back()->with('success', 'Đã lưu cài đặt nhắc lịch ôn tập!');
    }
}
