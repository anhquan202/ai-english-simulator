<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CustomStoryGeneratorService;
use Tests\TestCase;

class CustomStoryGeneratorServiceTest extends TestCase
{
    protected CustomStoryGeneratorService $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new CustomStoryGeneratorService();
    }

    public function test_generator_returns_complete_scenario_structure(): void
    {
        $prompt = 'Tôi muốn luyện phỏng vấn tiếng Anh Tech Lead ở Singapore';
        $result = $this->generator->generate($prompt);

        $expectedKeys = [
            'title',
            'category',
            'location_name',
            'detailed_description',
            'user_role',
            'ai_name',
            'ai_role',
            'twist_prompt',
            'goals',
            'first_message',
            'level',
            'ai_tone',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Missing key: {$key}");
            $this->assertNotEmpty($result[$key], "Value for {$key} should not be empty");
        }

        $this->assertContains($result['category'], ['workplace', 'shopping', 'travel', 'dining']);
        $this->assertContains($result['level'], ['A1', 'A2', 'B1', 'B2']);
        $this->assertContains($result['ai_tone'], ['friendly', 'professional', 'challenging']);
    }

    public function test_generator_recognizes_shopping_prompt(): void
    {
        $prompt = 'Tôi muốn trả hàng tại siêu thị vì ấm đun nước bị hỏng';
        $result = $this->generator->generate($prompt);

        $this->assertEquals('shopping', $result['category']);
        $this->assertStringContainsString('siêu thị', mb_strtolower($result['title']));
    }

    public function test_generator_recognizes_travel_prompt(): void
    {
        $prompt = 'Check-in khách sạn ở Bangkok phòng view biển bị trục trặc';
        $result = $this->generator->generate($prompt);

        $this->assertEquals('travel', $result['category']);
        $this->assertStringContainsString('khách sạn', mb_strtolower($result['title']));
    }
}
