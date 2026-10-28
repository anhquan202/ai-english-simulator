# ĐẶC TẢ KỸ THUẬT PROMPT AI & TÍCH HỢP GEMINI
## (AI PROMPT ENGINEERING & DUAL-CHANNEL SPECIFICATION - APS)

* **Dự án:** AI-Powered Real-World English Communication Simulator
* **Mã tài liệu:** APS-v1.0 (Gemini 2.5 Flash & Laravel Service Integration)
* **Tài liệu tham chiếu:** [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md) | [SYSTEM_LOGIC_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/SYSTEM_LOGIC_SPECIFICATION.md) | [DATABASE_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/DATABASE_SPECIFICATION.md)
* **Trạng thái:** Active / Baseline

---

## 1. NGUYÊN LÝ THIẾT KẾ KÊNH KÉP (DUAL-CHANNEL OUTPUT)

Thay vì gọi nhiều API độc lập (vừa tốn chi phí vừa tăng độ trễ mạng), hệ thống sử dụng **Google Gemini 2.5 Flash** với tính năng `response_mime_type: 'application/json'` để thực thi đồng thời 2 nhiệm vụ trong **1 request duy nhất (dưới 1 giây)**:

1. **Kênh Thoại (User-Facing Dialogue):** Lời đối đáp tự nhiên, ngắn gọn (1–2 câu) của nhân vật AI theo Persona và bối cảnh.
2. **Kênh Đánh Giá Ngầm (System-Facing Analytics):** Bóc tách phân tích ngữ âm từng từ (Content vs Function), đề xuất câu nói tự nhiên theo ngữ cảnh và theo dõi mức độ giải quyết giao dịch.

```
[Người dùng nói] ──> [Laravel gửi History + Transcript tới Gemini 2.5 Flash]
                                      │
               ┌──────────────────────┴──────────────────────┐
               ▼                                             ▼
     [KÊNH 1: HIỂN THỊ NGAY]                       [KÊNH 2: XỬ LÝ NGẦM]
   - Câu thoại của AI (ai_reply)               - Đánh giá hoàn thành (is_goal_achieved)
   - Cảm xúc hiển thị (emotion)                - Điểm thích ứng ngữ cảnh (adaptability)
                                               - Bóc tách từ nội dung (word_analysis)
                                               - Gợi ý câu tự nhiên (context_alternative)
```

---

## 2. ĐẶC TẢ JSON SCHEMA BẮT BUỘC (STRICT JSON OUTPUT)

Gemini được cấu hình trả về định dạng JSON nghiêm ngặt tuân thủ cấu trúc sau:

```json
{
  "ai_reply": {
    "text": "Sure thing! What size would you like for that iced latte? We have Tall, Grande, and Venti.",
    "emotion": "friendly"
  },
  "session_progress": {
    "is_goal_achieved": false,
    "contextual_relevance_score": 90,
    "ready_to_end": false
  },
  "pedagogical_feedback": {
    "context_alternative": "Could I get a medium iced latte with less sugar, please?",
    "context_explanation": "Trong văn hóa gọi cà phê tại Mỹ, dùng 'Could I get...' lịch sự hơn nhiều so với 'I want...'"
  },
  "word_evaluations": [
    { "word": "could", "type": "function", "status": "GREEN", "ipa": "/kʊd/" },
    { "word": "i", "type": "function", "status": "GREEN", "ipa": "/aɪ/" },
    { "word": "have", "type": "function", "status": "GREEN", "ipa": "/hæv/" },
    { "word": "iced", "type": "content", "status": "GREEN", "ipa": "/aɪst/" },
    { "word": "latte", "type": "content", "status": "GREEN", "ipa": "/ˈlɑː.teɪ/" },
    { "word": "sugar", "type": "content", "status": "RED", "ipa": "/ˈʃʊɡ.ər/", "note": "Chú ý phát âm rõ âm /ʃ/, tránh đọc thành /s/" }
  ]
}
```

---

## 3. THIẾT KẾ SYSTEM PROMPT CHUẨN MỰC (TEMPLATE INJECTION)

Template này được Controller nạp động từ các bảng `scenarios`, `characters` và `practice_sessions`:

```markdown
You are an advanced AI roleplay partner and English communication coach in an immersive simulator.

### 1. YOUR ROLE & PERSONA
- Name: {character_name}
- Role: {character_role}
- Personality & Tone: {character_personality}
- Location: {scenario_location}
- Difficulty Level: {level} 
  * A2: Use clear, simple vocabulary, natural but accessible phrasing.
  * B1: Use moderate speed, everyday idioms, and conversational phrasal verbs.
  * B2: Speak at natural native speed, use casual slang, and negotiate naturally.

### 2. CONVERSATIONAL RULES (ABSOLUTE)
- Stay in character 100% of the time. Speak natural spoken English.
- Keep your verbal reply CONCISE (1 to 2 sentences max). NEVER lecture or explain grammar in your spoken reply.
- CODE-SWITCHING (Anti-Nagging Rule - BR-EXC-03): If the user speaks Vietnamese or mixes languages, DO NOT interrupt or nag them with "Please speak English". Naturally acknowledge any English words you understood and continue the conversation smoothly in English.
- HYBRID TWIST RULE (BR-MAS-01): If current turn is turn {twist_turn_number} and twist has not triggered, introduce this realistic twist naturally: "{twist_prompt}".
- TERMINATION (BR-MIS-02): When the primary exchange goal is completed naturally (payment done / item received / goodbye exchanged), say a warm, polite farewell and set "ready_to_end": true.

### 3. EVALUATION RULES (OUTPUT IN JSON)
- Content vs Function Words (BR-EVA-01): 
  * Mark function words (to, a, the, with, and, for) as GREEN (allow weak forms and linking).
  * Strictly evaluate content words (nouns, main verbs, adjectives). Mark RED if critical vowels/consonants are distorted or ending sounds (/s/, /z/, /ed/) are dropped.
- Context Alternative (REQ-EVA-02): Provide ONE natural native expression for the user's sentence that fits the exact current social context, with a brief 1-line Vietnamese explanation.
```

---

## 4. MÃ NGUỒN SERVICE LAYER TRONG LARAVEL (`GeminiSimulatorService.php`)

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiSimulatorService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Gửi toàn bộ lịch sử thoại và câu mới của user tới Gemini API
     */
    public function generateTurnResponse(array $conversationHistory, string $systemPrompt): array
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $conversationHistory,
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => 0.7,
                'maxOutputTokens' => 1200,
            ]
        ];

        try {
            $response = Http::timeout(10)->post($endpoint, $payload);

            if ($response->successful()) {
                $rawJson = $response->json('candidates.0.content.parts.0.text');
                return json_decode($rawJson, true) ?? $this->fallbackResponse();
            }

            Log::error('Gemini API Error Response', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return $this->fallbackResponse();

        } catch (Exception $e) {
            Log::error('Gemini API Connection Exception: ' . $e->getMessage());
            return $this->fallbackResponse();
        }
    }

    /**
     * Phản hồi dự phòng khi mất mạng hoặc nghẽn API để bảo vệ trải nghiệm người dùng
     */
    protected function fallbackResponse(): array
    {
        return [
            'ai_reply' => [
                'text' => "I see! Could you please repeat that? I want to make sure I got it right.",
                'emotion' => 'friendly'
            ],
            'session_progress' => [
                'is_goal_achieved' => false,
                'contextual_relevance_score' => 80,
                'ready_to_end' => false
            ],
            'pedagogical_feedback' => [
                'context_alternative' => null,
                'context_explanation' => null
            ],
            'word_evaluations' => []
        ];
    }
}
```

---

## 5. MẪU XỬ LÝ TRONG `TurnController.php`

```php
namespace App\Http\Controllers;

use App\Models\PracticeSession;
use App\Services\GeminiSimulatorService;
use Illuminate\Http\Request;

class TurnController extends Controller
{
    public function store(Request $request, PracticeSession $session, GeminiSimulatorService $gemini)
    {
        $request->validate([
            'transcript'       => 'required|string',
            'thinking_seconds' => 'nullable|numeric',
            'redo_count'       => 'integer|min:0|max:2',
            'hints_used'       => 'boolean'
        ]);

        // 1. Lưu lượt nói của User
        $userTurn = $session->turns()->create([
            'turn_number'      => $session->turns()->count() + 1,
            'speaker'          => 'user',
            'raw_transcript'   => $request->transcript,
            'thinking_seconds' => $request->thinking_seconds,
            'redo_count'       => $request->redo_count,
            'hints_used'       => $request->hints_used,
        ]);

        // 2. Chuẩn bị lịch sử hội thoại chuẩn Gemini
        $history = $session->turns()->orderBy('turn_number')->get()->map(function ($turn) {
            return [
                'role'  => $turn->speaker === 'user' ? 'user' : 'model',
                'parts' => [['text' => $turn->raw_transcript]]
            ];
        })->toArray();

        // 3. Nạp Prompt và gọi Gemini
        $systemPrompt = $this->buildSystemPrompt($session);
        $aiAnalysis = $gemini->generateTurnResponse($history, $systemPrompt);

        // 4. Lưu kết quả phân tích ngữ âm vào Turn của User
        $userTurn->update([
            'context_alternative' => $aiAnalysis['pedagogical_feedback']['context_alternative'] ?? null,
            'word_analysis'       => $aiAnalysis['word_evaluations'] ?? [],
            'turn_penalty_score'  => $this->calculatePenalty($aiAnalysis['word_evaluations'] ?? [], $request->thinking_seconds)
        ]);

        // 5. Lưu câu thoại của AI
        $aiTurn = $session->turns()->create([
            'turn_number'    => $session->turns()->count() + 1,
            'speaker'        => 'ai',
            'raw_transcript' => $aiAnalysis['ai_reply']['text'],
        ]);

        // 6. Kiểm tra kết thúc phiên tự nhiên
        if ($aiAnalysis['session_progress']['ready_to_end'] ?? false) {
            $this->finalizeSession($session);
        }

        // 7. Trả dữ liệu về cho Alpine.js
        return response()->json([
            'ai_reply'        => $aiAnalysis['ai_reply'],
            'user_evaluation' => $userTurn->fresh(),
            'is_completed'    => $session->fresh()->status === 'completed'
        ]);
    }
}
```
