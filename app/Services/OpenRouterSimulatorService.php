<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\AiSimulatorServiceInterface as AltAiSimulatorServiceInterface;
use App\Models\DialogueTurn;
use App\Models\PracticeSession;
use App\Services\Contracts\AiSimulatorServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterSimulatorService implements AiSimulatorServiceInterface, AltAiSimulatorServiceInterface
{
    protected ?string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->model = (string) config('services.openrouter.model', 'google/gemma-4-26b-a4b-it:free');
        $this->baseUrl = (string) config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
    }

    /**
     * Process an incoming dialogue turn via OpenRouter API (Dual-Channel JSON mode)
     * with graceful degradation to an intelligent local mock if the API key is absent, rate-limited, or unreachable.
     */
    public function processTurn(
        PracticeSession $session,
        string $userTranscript,
        int $thinkingSeconds = 0,
        int $redoCount = 0,
        bool $hintsUsed = false
    ): array {
        // Load scenario and character relationships if not already loaded
        if (! $session->relationLoaded('scenario')) {
            $session->load('scenario');
        }
        if (! $session->relationLoaded('character')) {
            $session->load('character');
        }

        $systemPrompt = $this->buildSystemPrompt($session);
        $conversationHistory = $this->buildConversationHistory($session, $userTranscript);

        // Check if an OpenRouter API Key is configured
        if ($this->hasValidApiKey()) {
            try {
                $response = $this->callOpenRouterApi($conversationHistory, $systemPrompt);
                if ($response !== null) {
                    return $this->sanitizeDualChannelResponse($response);
                }
            } catch (Exception $e) {
                Log::warning('OpenRouter API call failed, falling back to local simulation: '.$e->getMessage());
            }
        }

        // Graceful fallback to intelligent simulated response
        return $this->generateMockDualChannelResponse(
            $session,
            $userTranscript,
            $thinkingSeconds,
            $redoCount,
            $hintsUsed
        );
    }

    /**
     * Process a focused retry of the weakest slice dialogue turn.
     */
    public function processSliceRetry(
        DialogueTurn $turn,
        string $userTranscript
    ): array {
        $thresholds = config('simulator.word_score_thresholds', ['green' => 85, 'orange' => 60]);
        $words = $this->extractWords($userTranscript);
        $evaluations = [];
        $totalScore = 0;

        foreach ($words as $word) {
            $isFunctionWord = $this->isFunctionWord($word);
            $ipa = $this->lookupIpa($word);

            // On slice retry, simulate high accuracy with intentional targeted feedback
            $score = $isFunctionWord ? 95 : rand(75, 96);
            $totalScore += $score;

            if ($score >= $thresholds['green']) {
                $status = 'GREEN';
                $note = 'Phát âm chuẩn xác, trọng âm rõ ràng.';
            } elseif ($score >= $thresholds['orange']) {
                $status = 'ORANGE';
                $note = 'Khá tốt, chú ý nối âm tự nhiên hơn.';
            } else {
                $status = 'RED';
                $note = 'Cần chú ý phát âm rõ âm cuối.';
            }

            $evaluations[] = [
                'word' => $word,
                'type' => $isFunctionWord ? 'function' : 'content',
                'status' => $status,
                'ipa' => $ipa,
                'note' => $note,
            ];
        }

        $wordCount = max(1, count($evaluations));
        $newAccuracy = round($totalScore / $wordCount, 1);
        $oldScore = 65.0;
        $deltaImprovement = round(max(0.0, $newAccuracy - $oldScore), 1);

        return [
            'word_evaluations' => $evaluations,
            'accuracy_score' => $newAccuracy,
            'delta_improvement' => $deltaImprovement,
            'encouragement_note' => $deltaImprovement > 5.0
                ? 'Tuyệt vời! Bạn đã sửa được các lỗi phát âm trọng tâm và âm đuôi rất rõ ràng.'
                : 'Tiến bộ rõ rệt! Hãy tiếp tục duy trì nhịp điệu tự nhiên này.',
        ];
    }

    /**
     * Verify whether an API key is configured.
     */
    protected function hasValidApiKey(): bool
    {
        return ! empty($this->apiKey) && ! str_contains($this->apiKey, 'your_');
    }

    /**
     * Make HTTP request to OpenRouter Chat Completions API with automated free model fallback.
     */
    protected function callOpenRouterApi(array $conversationHistory, string $systemPrompt): ?array
    {
        $endpoint = rtrim($this->baseUrl, '/').'/chat/completions';

        // Prepare model list with automatic free fallback routing in case primary model is rate-limited
        $models = array_values(array_unique(array_filter([
            $this->model,
            'openrouter/free',
            'liquid/lfm-2.5-2.6b:free',
        ])));

        $payload = [
            'model' => $this->model,
            'models' => $models,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $conversationHistory
            ),
            'temperature' => 0.7,
            'max_tokens' => 1200,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'HTTP-Referer' => 'http://localhost:8002',
            'X-Title' => 'AI English Simulator',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(20)->post($endpoint, $payload);

        if ($response->successful()) {
            $rawText = $response->json('choices.0.message.content');
            if (is_string($rawText)) {
                $decoded = $this->cleanAndDecodeJson($rawText);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        Log::warning('OpenRouter API Non-200 or unparseable response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }

    /**
     * Clean markdown code fences and parse JSON string robustly.
     */
    protected function cleanAndDecodeJson(string $rawText): ?array
    {
        $clean = trim($rawText);

        // Strip markdown ```json ... ``` fences if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $clean, $matches)) {
            $clean = trim($matches[1]);
        } else {
            // Extract the outermost JSON object if there's conversational prefix/suffix
            $firstBrace = strpos($clean, '{');
            $lastBrace = strrpos($clean, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $clean = substr($clean, $firstBrace, $lastBrace - $firstBrace + 1);
            }
        }

        $decoded = json_decode($clean, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Build system prompt dynamically from scenario, character persona, and twist settings.
     */
    protected function buildSystemPrompt(PracticeSession $session): string
    {
        $character = $session->character;
        $scenario = $session->scenario;
        $level = $session->level ?? 'A2';

        $characterName = $character?->name ?? 'Assistant';
        $characterRole = $character?->role ?? $character?->role_title ?? 'Conversation Partner';
        $personality = $character?->personality ?? 'Friendly, patient, and spoken clearly.';
        $location = $scenario?->location_name ?? 'Everyday venue';
        $twistPrompt = $scenario?->twist_prompt ?? 'Provide a slight real-world variation naturally.';

        $twistGuidance = match (strtoupper($level)) {
            'A1' => 'Keep the twist ultra-simple with 3-5 word phrasing and 2 straightforward choices (no overwhelming data).',
            'A2' => 'Everyday vocabulary and accessible phrasing.',
            'B1' => 'Natural pace and standard conversational options.',
            'B2' => 'Full realistic details, terms, or policy nuances to challenge negotiation.',
            default => 'Everyday vocabulary and accessible phrasing.',
        };

        return <<<PROMPT
You are an advanced AI roleplay partner and English communication coach in an immersive simulator.

### 1. YOUR ROLE & PERSONA
- Name: {$characterName}
- Role: {$characterRole}
- Personality & Tone: {$personality}
- Location: {$location}
- Difficulty Level: {$level}
  * A1: Use ultra-simple, high-frequency vocabulary, very short sentences (3-5 words), speak slowly and clearly, avoid complex idioms or clauses.
  * A2: Use clear, simple vocabulary, natural but accessible phrasing.
  * B1: Use moderate speed, everyday idioms, and conversational phrasal verbs.
  * B2: Speak at natural native speed, use casual slang, and negotiate naturally.

### 2. CONVERSATIONAL RULES (ABSOLUTE)
- Stay in character 100% of the time. Speak natural spoken English.
- Keep your verbal reply CONCISE (1 to 2 sentences max). NEVER lecture or explain grammar in your spoken reply.
- CODE-SWITCHING (Anti-Nagging Rule - BR-EXC-03): If the user speaks Vietnamese or mixes languages, DO NOT interrupt or nag them with "Please speak English". Naturally acknowledge any English words you understood and continue the conversation smoothly in English.
- REALISTIC TWIST: At turns 2-3, if not triggered yet, introduce this twist naturally: "{$twistPrompt}". Adapt the twist's complexity dynamically based on difficulty level:
  * If A1: Keep the twist ultra-simple with 3-5 word phrasing and 2 straightforward choices (no overwhelming data).
  * If A2: Everyday vocabulary and accessible phrasing.
  * If B1: Natural pace and standard conversational options.
  * If B2: Full realistic details, terms, or policy nuances to challenge negotiation.
  * Current Level Directive ({$level}): {$twistGuidance}
- TERMINATION (BR-MIS-02): When the primary exchange goal is completed naturally (payment done / item received / goodbye exchanged), say a warm, polite farewell and set "ready_to_end": true.

### 3. EVALUATION RULES (OUTPUT IN STRICT JSON)
- Content vs Function Words (BR-EVA-01):
  * Mark function words (to, a, the, with, and, for, i, could, can, would, in, of, on, at, please) as GREEN.
  * Strictly evaluate content words (nouns, main verbs, adjectives). Mark RED if critical vowels/consonants are distorted or ending sounds are dropped.
- Context Alternative (REQ-EVA-02): Provide ONE natural native expression for the user's sentence that fits the exact current social context, with a brief 1-line Vietnamese explanation.

OUTPUT SCHEMA (STRICT JSON ONLY, DO NOT ADD ANY MARKDOWN OR TEXT OUTSIDE JSON):
{
  "ai_reply": {
    "text": "1-2 spoken sentences from the persona",
    "translation_vi": "Bản dịch nghĩa tiếng Việt tự nhiên và gần gũi của câu thoại trên",
    "emotion": "friendly | curious | polite | cheerful | surprised"
  },
  "session_progress": {
    "is_goal_achieved": boolean,
    "contextual_relevance_score": number (0-100),
    "ready_to_end": boolean
  },
  "pedagogical_feedback": {
    "context_alternative": "Natural native expression phrasing",
    "context_explanation": "Brief 1-line Vietnamese explanation"
  },
  "word_evaluations": [
    {
      "word": "string",
      "type": "function | content",
      "status": "GREEN | ORANGE | RED",
      "ipa": "/.../",
      "note": "optional tip"
    }
  ]
}
PROMPT;
    }

    /**
     * Build OpenRouter messages history array from prior dialogue turns.
     */
    protected function buildConversationHistory(PracticeSession $session, string $newUserTranscript): array
    {
        $history = [];

        $turns = $session->dialogueTurns()
            ->orderBy('turn_number')
            ->get();

        foreach ($turns as $turn) {
            $transcript = $turn->raw_transcript ?? $turn->user_transcript ?? $turn->ai_reply_text;
            if (! empty($transcript)) {
                $history[] = [
                    'role' => $turn->speaker === 'user' ? 'user' : 'assistant',
                    'content' => $transcript,
                ];
            }
        }

        // Append current incoming turn
        $history[] = [
            'role' => 'user',
            'content' => $newUserTranscript,
        ];

        return $history;
    }

    /**
     * Generate an intelligent mock dual-channel response simulating OpenRouter/Gemini output.
     */
    protected function generateMockDualChannelResponse(
        PracticeSession $session,
        string $userTranscript,
        int $thinkingSeconds,
        int $redoCount,
        bool $hintsUsed
    ): array {
        $thresholds = config('simulator.word_score_thresholds', ['green' => 85, 'orange' => 60]);
        $scenarioSlug = $session->scenario?->slug ?? '';
        $character = $session->character;
        $turnCount = $session->dialogueTurns()->count() + 1;

        $words = $this->extractWords($userTranscript);
        $wordEvaluations = [];

        foreach ($words as $word) {
            $isFunc = $this->isFunctionWord($word);
            $ipa = $this->lookupIpa($word);

            // Simulate scoring
            if ($isFunc) {
                $status = 'GREEN';
                $note = null;
            } else {
                $randomScore = rand(55, 95);
                if ($randomScore >= $thresholds['green']) {
                    $status = 'GREEN';
                    $note = 'Phát âm chuẩn xác.';
                } elseif ($randomScore >= $thresholds['orange']) {
                    $status = 'ORANGE';
                    $note = 'Phát âm tương đối rõ, chú ý nguyên âm đôi.';
                } else {
                    $status = 'RED';
                    $note = "Chú ý phát âm rõ âm cuối và trọng âm của từ '{$word}'.";
                }
            }

            $wordEvaluations[] = [
                'word' => $word,
                'type' => $isFunc ? 'function' : 'content',
                'status' => $status,
                'ipa' => $ipa,
                'note' => $note,
            ];
        }

        // Contextual AI replies and alternatives based on scenario & turn index
        [$aiReply, $aiReplyTranslation, $contextAlt, $contextExpl, $isGoalAchieved, $readyToEnd] = $this->resolveScenarioTurnContent(
            $scenarioSlug,
            $character?->name ?? 'Emma',
            $turnCount,
            $userTranscript,
            $session->level ?? 'A2'
        );

        $relevanceScore = rand(85, 95);
        if ($hintsUsed) {
            $relevanceScore = max(70, $relevanceScore - 5);
        }

        return [
            'ai_reply' => [
                'text' => $aiReply,
                'translation_vi' => $aiReplyTranslation,
                'emotion' => 'friendly',
            ],
            'session_progress' => [
                'is_goal_achieved' => $isGoalAchieved,
                'contextual_relevance_score' => $relevanceScore,
                'ready_to_end' => $readyToEnd,
            ],
            'pedagogical_feedback' => [
                'context_alternative' => $contextAlt,
                'context_explanation' => $contextExpl,
            ],
            'word_evaluations' => $wordEvaluations,
        ];
    }

    /**
     * Resolve contextual mock replies and pedagogical tips tailored to all 5 scenarios.
     */
    protected function resolveScenarioTurnContent(
        string $scenarioSlug,
        string $characterName,
        int $turnIndex,
        string $userText,
        string $level = 'A2'
    ): array {
        $level = strtoupper($level);

        switch ($scenarioSlug) {
            case 'supermarket-refund':
                if ($level === 'A1') {
                    if ($turnIndex <= 2) {
                        return [
                            'Hello! I can help with your return. May I see your receipt, please?',
                            'Xin chào! Tôi có thể hỗ trợ bạn trả hàng. Tôi có thể xem hóa đơn của bạn được không?',
                            'Hello, I want to return this item. Here is my receipt.',
                            'Chào nhân viên và đưa hóa đơn mua hàng rất ngắn gọn, rõ ràng.',
                            false,
                            false,
                        ];
                    }

                    return [
                        'All done! Your refund is complete. Have a wonderful day!',
                        'Đã xong! Thủ tục hoàn tiền của bạn đã hoàn tất. Chúc bạn một ngày tốt lành!',
                        'Thank you so much! Have a good day!',
                        'Cảm ơn và chúc lại nhân viên thân thiện.',
                        true,
                        true,
                    ];
                }

                if ($turnIndex <= 2) {
                    return [
                        'I see your receipt here. Card refunds take 3 to 5 business days, but I can issue an immediate store credit voucher with a 5% bonus today. Which would you prefer?',
                        'Tôi đã thấy hóa đơn của bạn ở đây. Hoàn tiền vào thẻ mất 3 đến 5 ngày làm việc, nhưng hôm nay tôi có thể cấp ngay một phiếu mua hàng có thêm 5% ưu đãi. Bạn thích phương án nào hơn?',
                        'I bought the wrong item by mistake. Could I get a refund, please?',
                        'Giải thích mua nhầm món hàng một cách lịch thiệp.',
                        false,
                        false,
                    ];
                }

                return [
                    'Perfect, here is your store voucher with the extra 5% credit. You can use it anytime. Have a great day shopping with us!',
                    'Tuyệt vời, đây là phiếu mua hàng có thêm 5% ưu đãi của bạn. Bạn có thể sử dụng bất cứ lúc nào. Chúc bạn một ngày mua sắm vui vẻ!',
                    'The store voucher with 5% bonus sounds great, I will take that option!',
                    'Đồng ý nhận voucher ưu đãi nhanh chóng và lịch sự.',
                    true,
                    true,
                ];

            case 'project-progress-meeting':
                if ($level === 'A1') {
                    if ($turnIndex <= 2) {
                        return [
                            'Good morning! How is your milestone task going this week?',
                            'Chào buổi sáng! Tiến độ nhiệm vụ tuần này của bạn thế nào rồi?',
                            'Good morning! Everything is on track and going well.',
                            'Báo cáo công việc ngắn gọn, tự tin cho trưởng nhóm.',
                            false,
                            false,
                        ];
                    }

                    return [
                        'Great job! Keep up the good work and see you at the team demo!',
                        'Làm tốt lắm! Tiếp tục duy trì phong độ và hẹn gặp bạn ở buổi demo nhóm nhé!',
                        'Thank you, Michael! I will keep you posted.',
                        'Cảm ơn và xác nhận sẽ tiếp tục cập nhật tiến độ.',
                        true,
                        true,
                    ];
                }

                if ($turnIndex <= 2) {
                    return [
                        'Thanks for the update. The client just asked for a minor change in the authentication flow. Can we finish it by Friday, or should we push it to next sprint?',
                        'Cảm ơn bạn đã cập nhật. Khách hàng vừa yêu cầu một thay đổi nhỏ ở luồng xác thực. Chúng ta có thể làm xong trước thứ Sáu không, hay nên dời sang sprint sau?',
                        'I am currently working on the API integration and should finish it by tomorrow.',
                        'Báo cáo chi tiết đầu việc đang làm và thời hạn dự kiến hoàn thành.',
                        false,
                        false,
                    ];
                }

                return [
                    'That makes total sense. Let\'s protect our Friday release and queue the auth change for next sprint. Thanks for the quick sync!',
                    'Hoàn toàn hợp lý. Hãy bảo vệ đợt phát hành thứ Sáu và đưa thay đổi xác thực vào sprint tiếp theo. Cảm ơn bạn vì cuộc trao đổi nhanh!',
                    'To protect Friday\'s release, I recommend pushing the auth change to next sprint.',
                    'Đề xuất giải pháp bảo vệ deadline rất chuyên nghiệp trong công việc.',
                    true,
                    true,
                ];

            case 'hotel-check-in':
                if ($level === 'A1') {
                    if ($turnIndex <= 2) {
                        return [
                            'Welcome to the hotel! May I have your name and passport, please?',
                            'Chào mừng quý khách đến với khách sạn! Tôi có thể xin tên và hộ chiếu của quý khách được không ạ?',
                            'Hello, I have a reservation.',
                            'Chào lễ tân và thông báo có đặt phòng trước rất đơn giản, dễ nhớ.',
                            false,
                            false,
                        ];
                    }

                    return [
                        'Here is your key for room 302. Enjoy your stay!',
                        'Đây là chìa khóa phòng 302 của quý khách. Chúc quý khách nghỉ ngơi thoải mái!',
                        'Thank you very much!',
                        'Nói lời cảm ơn ngắn gọn, lịch sự khi nhận chìa khóa phòng.',
                        true,
                        true,
                    ];
                }

                if ($turnIndex <= 2) {
                    return [
                        'Thank you! I found your reservation under that name. Our sea-view room is currently having quick AC maintenance—would you like a free upgrade to our Executive Garden Suite instead?',
                        'Cảm ơn quý khách! Tôi đã tìm thấy thông tin đặt phòng dưới tên của quý khách. Phòng hướng biển của chúng tôi hiện đang được bảo trì điều hòa nhanh—quý khách có muốn được nâng hạng miễn phí lên Phòng Suite Executive hướng vườn không ạ?',
                        'Hi, I have a reservation under my name. Here is my passport.',
                        'Trong ngữ cảnh check-in khách sạn, mở đầu bằng câu xác nhận đặt phòng và gửi passport rất tự nhiên và ngắn gọn.',
                        false,
                        false,
                    ];
                }

                return [
                    'Wonderful choice! Here are your keycards for Suite 402 on the Executive floor. Breakfast is served from 6:30 AM in the lounge. Have a wonderful stay!',
                    'Một sự lựa chọn tuyệt vời! Đây là thẻ khóa phòng Suite 402 trên tầng Executive của quý khách. Bữa sáng được phục vụ từ 6:30 sáng tại sảnh chờ. Chúc quý khách có một kỳ nghỉ tuyệt vời!',
                    'The garden suite upgrade sounds great, thank you so much!',
                    "Dùng 'sounds great, thank you so much' thể hiện sự lịch thiệp và đồng thuận nhanh chóng trong giao tiếp khách sạn.",
                    true,
                    true,
                ];

            case 'subway-ticket-purchase':
                if ($level === 'A1') {
                    if ($turnIndex <= 2) {
                        return [
                            'Sure! One subway ticket is three dollars. Cash or card?',
                            'Được chứ! Một vé tàu điện ngầm là 3 đô la. Bạn trả tiền mặt hay quẹt thẻ?',
                            'One ticket, please. How much is it?',
                            'Mẫu câu hỏi mua vé và giá tiền cơ bản cho người mới bắt đầu.',
                            false,
                            false,
                        ];
                    }

                    return [
                        'Here is your ticket. Go to Platform 1. Have a nice trip!',
                        'Vé của bạn đây. Bạn đi ra Sân ga 1 nhé. Chúc bạn có một chuyến đi vui vẻ!',
                        'Thank you! Bye!',
                        'Cảm ơn và chào tạm biệt ngắn gọn.',
                        true,
                        true,
                    ];
                }

                if ($turnIndex <= 2) {
                    return [
                        'Line 1 is temporarily halted for rail repairs today. You can take Line 3 to 14th Street and transfer to the shuttle bus. Would you like a day pass or single-ride ticket?',
                        'Tuyến số 1 hôm nay đang tạm dừng hoạt động để sửa chữa đường ray. Bạn có thể đi Tuyến số 3 đến Phố 14 và chuyển sang xe buýt đưa đón. Bạn muốn mua thẻ đi lại trong ngày hay vé một lượt?',
                        "I'd like a round-trip ticket to Downtown, please. Which line should I take?",
                        "Mẫu câu 'I'd like a [ticket_type] to [destination], please' là chuẩn mực tự nhiên tại quầy vé tàu điện ngầm.",
                        false,
                        false,
                    ];
                }

                return [
                    'Here is your metro card and connection map. Just tap your card at the turnstile right ahead. Have a safe ride!',
                    'Đây là thẻ tàu điện ngầm và bản đồ chuyển tuyến của bạn. Bạn chỉ cần quẹt thẻ tại cổng xoay ngay phía trước. Chúc bạn có chuyến đi an toàn!',
                    'Got it, thank you for the directions!',
                    "Cụm 'Got it, thank you for the directions' giúp kết thúc trao đổi gọn gàng và tự nhiên.",
                    true,
                    true,
                ];

            case 'local-roastery-coffee':
            default:
                if ($level === 'A1') {
                    if ($turnIndex <= 2) {
                        return [
                            'Sure! Small latte with oat milk is four dollars. Would you like it hot or iced?',
                            'Được chứ! Cà phê latte nhỏ với sữa yến mạch là 4 đô la. Bạn muốn uống nóng hay đá?',
                            'Hello, one small latte, please.',
                            'Chào và gọi một cốc cà phê cỡ nhỏ rất đơn giản và tự nhiên.',
                            false,
                            false,
                        ];
                    }

                    return [
                        'Here is your warm latte! Enjoy your drink and have a great day!',
                        'Cà phê latte ấm của bạn đây! Chúc bạn thưởng thức ngon miệng và có một ngày tuyệt vời!',
                        'Thank you! Have a good day!',
                        'Cảm ơn và chúc lại nhân viên quán bằng câu chào thân thiện.',
                        true,
                        true,
                    ];
                }

                if ($turnIndex <= 2) {
                    return [
                        'Sure thing! By the way, our main espresso grinder is resetting for five minutes. Would you mind waiting five minutes or would you prefer a smooth cold brew right now?',
                        'Chắc chắn rồi! Nhân tiện thì cối xay espresso chính của quán đang được reset trong 5 phút. Bạn có phiền đợi 5 phút không, hay bạn muốn đổi sang một ly cold brew mát lạnh có sẵn ngay bây giờ nhỉ?',
                        'Could I please get a medium latte with less sugar and oat milk?',
                        "Dùng 'Could I please get...' vừa lịch sự vừa rất tự nhiên trong văn hóa gọi món tại các quán cà phê.",
                        false,
                        false,
                    ];
                }

                return [
                    'Perfect, your cold brew with a splash of oat milk is ready right here! That comes to four dollars and fifty cents. Enjoy your coffee!',
                    'Tuyệt vời, ly cold brew thêm một chút sữa yến mạch của bạn đã sẵn sàng ngay đây! Của bạn hết 4 đô la 50 xu. Chúc bạn thưởng thức cà phê ngon miệng!',
                    "No problem at all, I'll switch to the cold brew then, thank you!",
                    "Cụm 'No problem at all' và 'I'll switch to...' thể hiện phản xạ linh hoạt tự nhiên khi gặp sự cố tại quán.",
                    true,
                    true,
                ];
        }
    }

    /**
     * Sanitize and guarantee all dual-channel JSON keys exist.
     */
    protected function sanitizeDualChannelResponse(array $data): array
    {
        return [
            'ai_reply' => [
                'text' => (string) ($data['ai_reply']['text'] ?? "I understand! Let's continue."),
                'translation_vi' => (string) ($data['ai_reply']['translation_vi'] ?? ''),
                'emotion' => (string) ($data['ai_reply']['emotion'] ?? 'friendly'),
            ],
            'session_progress' => [
                'is_goal_achieved' => (bool) ($data['session_progress']['is_goal_achieved'] ?? false),
                'contextual_relevance_score' => (float) ($data['session_progress']['contextual_relevance_score'] ?? 85.0),
                'ready_to_end' => (bool) ($data['session_progress']['ready_to_end'] ?? false),
            ],
            'pedagogical_feedback' => [
                'context_alternative' => $data['pedagogical_feedback']['context_alternative'] ?? null,
                'context_explanation' => $data['pedagogical_feedback']['context_explanation'] ?? null,
            ],
            'word_evaluations' => is_array($data['word_evaluations'] ?? null) ? $data['word_evaluations'] : [],
        ];
    }

    /**
     * Helper to split transcript into clean words.
     */
    protected function extractWords(string $text): array
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s\'-]/u', ' ', $text) ?? '';
        $tokens = preg_split('/\s+/', trim($clean));

        return array_values(array_filter($tokens ?? [], fn ($w) => $w !== ''));
    }

    /**
     * Identify common grammatical function words.
     */
    protected function isFunctionWord(string $word): bool
    {
        $functionWords = [
            'a', 'an', 'the', 'to', 'in', 'at', 'on', 'of', 'for', 'with', 'by',
            'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them',
            'my', 'your', 'his', 'its', 'our', 'their', 'and', 'but', 'or', 'so',
            'can', 'could', 'would', 'will', 'is', 'am', 'are', 'was', 'were', 'do', 'does', 'did',
            'have', 'has', 'had', 'please', 'just', 'that', 'this', 'here', 'there',
        ];

        return in_array(strtolower(trim($word)), $functionWords, true);
    }

    /**
     * Common IPA dictionary lookup for realistic feedback.
     */
    protected function lookupIpa(string $word): string
    {
        $dict = [
            'latte' => '/ˈlɑː.teɪ/',
            'coffee' => '/ˈkɔː.fi/',
            'sugar' => '/ˈʃʊɡ.ər/',
            'milk' => '/mɪlk/',
            'oat' => '/oʊt/',
            'cold' => '/koʊld/',
            'brew' => '/bruː/',
            'size' => '/saɪz/',
            'medium' => '/ˈmiː.di.əm/',
            'waiting' => '/ˈweɪ.tɪŋ/',
            'reservation' => '/ˌrɛz.ərˈveɪ.ʃən/',
            'passport' => '/ˈpæs.pɔːrt/',
            'check' => '/tʃɛk/',
            'in' => '/ɪn/',
            'hotel' => '/hoʊˈtɛl/',
            'suite' => '/swiːt/',
            'garden' => '/ˈɡɑːr.dən/',
            'breakfast' => '/ˈbrɛk.fəst/',
            'upgrade' => '/ʌpˈɡreɪd/',
            'wifi' => '/ˈwaɪ.faɪ/',
            'ticket' => '/ˈtɪk.ɪt/',
            'subway' => '/ˈsʌb.weɪ/',
            'metro' => '/ˈmɛt.roʊ/',
            'card' => '/kɑːrd/',
            'turnstile' => '/ˈtɜːrn.staɪl/',
            'station' => '/ˈsteɪ.ʃən/',
            'downtown' => '/ˌdaʊnˈtaʊn/',
            'shuttle' => '/ˈʃʌt.əl/',
            'line' => '/laɪn/',
            'bus' => '/bʌs/',
            'problem' => '/ˈprɑː.bləm/',
            'thank' => '/θæŋk/',
            'hello' => '/hɛˈloʊ/',
            'small' => '/smɔːl/',
            'much' => '/mʌtʃ/',
            'one' => '/wʌn/',
            'return' => '/rɪˈtɜːrn/',
            'receipt' => '/rɪˈsiːt/',
            'refund' => '/ˈriː.fʌnd/',
            'wrong' => '/rɔːŋ/',
            'mistake' => '/mɪˈsteɪk/',
            'policy' => '/ˈpɑː.lə.si/',
            'voucher' => '/ˈvaʊ.tʃər/',
            'meeting' => '/ˈmiː.tɪŋ/',
            'project' => '/ˈprɑː.dʒɛkt/',
            'progress' => '/ˈprɑː.ɡrɛs/',
            'milestone' => '/ˈmaɪl.stoʊn/',
            'deadline' => '/ˈdɛd.laɪn/',
            'sprint' => '/sprɪnt/',
            'specs' => '/spɛks/',
            'recommend' => '/ˌrɛk.əˈmɛnd/',
        ];

        $lower = strtolower(trim($word));

        return $dict[$lower] ?? "/{$lower}/";
    }
}
