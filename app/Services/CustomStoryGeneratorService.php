<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomStoryGeneratorService
{
    protected ?string $apiKey;

    protected string $model;

    protected CostTrackingService $costTracker;

    public function __construct(?CostTrackingService $costTracker = null)
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->costTracker = $costTracker ?? app(CostTrackingService::class);
    }

    /**
     * Generate full scenario details from a learner prompt.
     *
     * @return array{
     *     title: string,
     *     category: string,
     *     location_name: string,
     *     detailed_description: string,
     *     user_role: string,
     *     ai_name: string,
     *     ai_role: string,
     *     twist_prompt: string,
     *     goals: string,
     *     first_message: string,
     *     level: string,
     *     ai_tone: string
     * }
     */
    public function generate(string $prompt): array
    {
        $trimmedPrompt = trim($prompt);

        if ($this->hasValidApiKey()) {
            try {
                $result = $this->callGeminiApi($trimmedPrompt);
                if ($result !== null) {
                    return $this->sanitizeGeneratedResponse($result, $trimmedPrompt);
                }
            } catch (Exception $e) {
                Log::warning('Gemini Custom Story Generation failed, falling back to mock: '.$e->getMessage());
            }
        }

        return $this->generateMockResponse($trimmedPrompt);
    }

    /**
     * Check if a valid Gemini API key is configured.
     */
    protected function hasValidApiKey(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        $placeholders = [
            'your_gemini_api_key_here',
            'dummy_key',
            'test_key',
            'placeholder',
        ];

        return ! in_array(strtolower(trim($this->apiKey)), $placeholders, true);
    }

    /**
     * Call Google Gemini API to structure the custom story into JSON.
     */
    protected function callGeminiApi(string $prompt): ?array
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $systemInstruction = <<<INSTRUCTION
You are an expert English communication curriculum designer for "mở lời." - an AI-powered conversational simulator.
Your job is to transform a short learner prompt into a rich, realistic conversational practice scenario.

The scenario MUST follow these principles:
1. Realistic, high-stakes everyday or workplace English communication.
2. A critical "Twist" (biến số bất ngờ) that challenges the learner to react, negotiate, or solve an unexpected problem mid-conversation.
3. Spoken English first_message: natural, engaging, concise (1-2 sentences).
4. Category must be one of: "workplace", "shopping", "travel", "dining".
5. Level must be one of: "A1", "A2", "B1", "B2".
6. AI tone must be one of: "friendly", "professional", "challenging".

Output STRICT JSON only matching this schema:
{
  "title": "Tên kịch bản tiếng Việt ngắn gọn, hấp dẫn",
  "category": "workplace | shopping | travel | dining",
  "location_name": "Tên địa điểm thực tế hoặc giả định kèm thành phố/quốc gia",
  "detailed_description": "2-3 câu tiếng Việt mô tả chi tiết bối cảnh, động cơ và vai trò",
  "user_role": "Vai trò người học đảm nhận (tiếng Việt)",
  "ai_name": "Tên nhân vật AI (tiếng Anh phổ biến, e.g. David, Emma, Jack)",
  "ai_role": "Vai trò nhân vật AI (tiếng Việt, e.g. Giám đốc Kỹ thuật, Lễ tân)",
  "twist_prompt": "Biến số bất ngờ ở giữa cuộc đối thoại buộc người học phản xạ (tiếng Việt)",
  "goals": "2-3 mục tiêu giao tiếp cụ thể, phân cách bằng dấu xuống dòng",
  "first_message": "1 câu chào mở đầu tự nhiên bằng tiếng Anh từ nhân vật AI",
  "level": "A1 | A2 | B1 | B2",
  "ai_tone": "friendly | professional | challenging"
}
INSTRUCTION;

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => "Hãy thiết kế kịch bản luyện nói tiếng Anh từ ý tưởng sau:\n\"{$prompt}\""],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => 0.7,
                'maxOutputTokens' => 1200,
            ],
        ];

        $startTime = microtime(true);
        $response = Http::timeout(12)->post($endpoint, $payload);
        $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

        if ($response->successful()) {
            $rawText = $response->json('candidates.0.content.parts.0.text');
            if (is_string($rawText)) {
                $decoded = json_decode($rawText, true);
                if (is_array($decoded)) {
                    $promptTokens = (int) ($response->json('usageMetadata.promptTokenCount') ?? 250);
                    $candidateTokens = (int) ($response->json('usageMetadata.candidatesTokenCount') ?? 350);

                    $this->costTracker->logApiCall([
                        'user_id' => auth()->id(),
                        'endpoint' => 'models/'.$this->model.':generateContent',
                        'model_name' => $this->model,
                        'prompt_tokens' => $promptTokens,
                        'candidate_tokens' => $candidateTokens,
                        'total_tokens' => $promptTokens + $candidateTokens,
                        'latency_ms' => $latencyMs,
                        'status_code' => $response->status(),
                    ]);

                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Fallback mock response generator using semantic keywords.
     */
    protected function generateMockResponse(string $prompt): array
    {
        $lower = mb_strtolower($prompt, 'UTF-8');

        if (str_contains($lower, 'siêu thị') || str_contains($lower, 'trả hàng') || str_contains($lower, 'hoàn tiền') || str_contains($lower, 'shopping') || str_contains($lower, 'mua')) {
            return [
                'title' => 'Trả hàng tại siêu thị & Nhận voucher bù giá',
                'category' => 'shopping',
                'location_name' => 'FreshMart Supermarket, Chicago',
                'detailed_description' => 'Bạn mua chiếc ấm điện tại FreshMart hôm qua nhưng cắm điện không hoạt động. Bạn mang nguyên tem mác cùng biên lai đến quầy CSKH để đề nghị đổi hoặc hoàn tiền.',
                'user_role' => 'Khách hàng lịch sự nhưng kiên quyết',
                'ai_name' => 'Emma',
                'ai_role' => 'Nhân viên Dịch vụ Khách hàng',
                'twist_prompt' => 'Emma kiểm tra và thông báo chính sách thẻ mất 5 ngày mới hoàn tiền, HOẶC cô ấy có thể phát ngay voucher siêu thị tặng thêm 5% tiền mặt hôm nay.',
                'goals' => "Giải thích lỗi sản phẩm ngắn gọn, mạch lạc\nĐưa ra quyết định chọn nhận voucher hay đợi hoàn tiền thẻ",
                'first_message' => 'Hello! Welcome to FreshMart Customer Service. How may I assist you with your purchase today?',
                'level' => 'A2',
                'ai_tone' => 'friendly',
            ];
        }

        if (str_contains($lower, 'khách sạn') || str_contains($lower, 'hotel') || str_contains($lower, 'view biển') || str_contains($lower, 'du lịch') || str_contains($lower, 'check-in')) {
            return [
                'title' => 'Xử lý sự cố nhận phòng khách sạn tại Bangkok',
                'category' => 'travel',
                'location_name' => 'Grand Riverview Hotel, Bangkok',
                'detailed_description' => 'Sau chuyến bay dài, bạn đến quầy lễ tân nhận phòng Deluxe view biển đã thanh toán trước. Tuy nhiên, khách sạn gặp sự cố kỹ thuật đột xuất.',
                'user_role' => 'Khách du lịch muốn nhận phòng nghỉ ngơi sớm',
                'ai_name' => 'Jack',
                'ai_role' => 'Quản lý Lễ tân Khách sạn',
                'twist_prompt' => 'Jack thông báo phòng của bạn đang bảo trì điều hòa trong 2 tiếng, và đề xuất nâng hạng miễn phí lên Executive Suite kèm bữa tối miễn phí tại nhà hàng.',
                'goals' => "Diễn đạt sự mệt mỏi nhưng giữ phong thái văn minh\nĐàm phán để nhận phòng thay thế nhanh nhất có thể",
                'first_message' => 'Good afternoon, welcome to Grand Riverview Hotel! May I have your passport and reservation details, please?',
                'level' => 'A2',
                'ai_tone' => 'friendly',
            ];
        }

        if (str_contains($lower, 'cafe') || str_contains($lower, 'cà phê') || str_contains($lower, 'ăn uống') || str_contains($lower, 'nhà hàng') || str_contains($lower, 'dining')) {
            return [
                'title' => 'Gọi món và xử lý thay đổi tại quán cà phê địa phương',
                'category' => 'dining',
                'location_name' => 'Blue Bottle Cafe, San Francisco',
                'detailed_description' => 'Bạn ghé vào một quán cà phê đông đúc vào giờ cao điểm sáng để gọi một ly cà phê đặc biệt theo khẩu vị cá nhân.',
                'user_role' => 'Khách hàng quen',
                'ai_name' => 'Sophie',
                'ai_role' => 'Barista Trưởng',
                'twist_prompt' => 'Sophie thông báo sữa hạt bạn yêu cầu tạm hết, và gợi ý dùng thử sữa yến mạch nướng đặc biệt của quán không tính thêm phí.',
                'goals' => "Mô tả khẩu vị và yêu cầu đồ uống tự nhiên\nPhản hồi nhanh với gợi ý thay thế của barista",
                'first_message' => 'Hi there! Welcome in. What can I get started for you today?',
                'level' => 'A2',
                'ai_tone' => 'friendly',
            ];
        }

        // Default: Workplace & Tech Lead Interview (matches demo preset)
        return [
            'title' => ! empty($prompt) ? 'Phỏng vấn Tech Lead: Xử lý sự cố & Thống nhất ý kiến team' : 'Phỏng vấn Tech Lead: Dự án thất bại & Xung đột team',
            'category' => 'workplace',
            'location_name' => 'Google Singapore (Online Call)',
            'detailed_description' => ! empty($prompt)
                ? 'Bạn tham gia vòng phỏng vấn hành vi (Behavioral Round) với Engineering Director. Bạn cần chia sẻ về một quyết định kiến trúc khó khăn và cách bạn thống nhất ý kiến trong team dựa trên: '.$prompt
                : 'Bạn tham gia vòng phỏng vấn hành vi với Engineering Director. Bạn cần chia sẻ về một thử thách kỹ thuật lớn và cách bạn thống nhất ý kiến trong team.',
            'user_role' => 'Ứng viên Senior Software Engineer',
            'ai_name' => 'David',
            'ai_role' => 'Engineering Director',
            'twist_prompt' => 'David bất ngờ hỏi: "Nếu giám đốc kinh doanh yêu cầu ra mắt trước 2 tuần mà hệ thống chưa hoàn tất kiểm thử tải, bạn sẽ phản hồi như thế nào ngay lúc này?"',
            'goals' => "Trình bày rõ ràng cấu trúc tình huống (STAR framework)\nThể hiện khả năng bảo vệ chất lượng kỹ thuật mà vẫn thấu cảm với mục tiêu kinh doanh",
            'first_message' => "Good morning! Thanks for joining us today. Let's jump right in: Could you walk me through a major architecture decision that didn't go as planned?",
            'level' => 'B1',
            'ai_tone' => 'professional',
        ];
    }

    /**
     * Sanitize output from Gemini API to ensure complete valid keys.
     */
    protected function sanitizeGeneratedResponse(array $data, string $fallbackPrompt): array
    {
        $validCategories = ['workplace', 'shopping', 'travel', 'dining'];
        $validLevels = ['A1', 'A2', 'B1', 'B2'];
        $validTones = ['friendly', 'professional', 'challenging'];

        $category = (string) ($data['category'] ?? 'workplace');
        if (! in_array($category, $validCategories, true)) {
            $category = 'workplace';
        }

        $level = strtoupper((string) ($data['level'] ?? 'B1'));
        if (! in_array($level, $validLevels, true)) {
            $level = 'B1';
        }

        $tone = (string) ($data['ai_tone'] ?? 'professional');
        if (! in_array($tone, $validTones, true)) {
            $tone = 'professional';
        }

        $goals = $data['goals'] ?? '';
        if (is_array($goals)) {
            $goals = implode("\n", $goals);
        }

        return [
            'title' => (string) ($data['title'] ?? 'Kịch bản: '.Str::limit($fallbackPrompt, 40)),
            'category' => $category,
            'location_name' => (string) ($data['location_name'] ?? 'Everyday venue'),
            'detailed_description' => (string) ($data['detailed_description'] ?? $fallbackPrompt),
            'user_role' => (string) ($data['user_role'] ?? 'Người học tiếng Anh'),
            'ai_name' => (string) ($data['ai_name'] ?? 'David'),
            'ai_role' => (string) ($data['ai_role'] ?? 'Đối tác đối thoại'),
            'twist_prompt' => (string) ($data['twist_prompt'] ?? 'Một biến số bất ngờ xảy ra đòi hỏi bạn phải giải quyết ngay.'),
            'goals' => (string) $goals,
            'first_message' => (string) ($data['first_message'] ?? "Hello! It's great to speak with you today."),
            'level' => $level,
            'ai_tone' => $tone,
        ];
    }
}
