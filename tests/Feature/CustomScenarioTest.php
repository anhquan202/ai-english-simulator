<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\PracticeSession;
use App\Models\Scenario;
use App\Models\ScenarioHint;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomScenarioTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_guest_redirected_to_login_when_accessing_create_page(): void
    {
        $response = $this->get(route('custom-stories.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_create_page(): void
    {
        $user = User::factory()->create([
            'tier' => 'unlimited',
        ]);

        $response = $this->actingAs($user)->get(route('custom-stories.create'));

        $response->assertOk();
        $response->assertViewIs('scenarios.create');
        $response->assertSee('TỰ TẠO STORY THỰC CHIẾN');
    }

    public function test_ai_generate_endpoint_returns_valid_json(): void
    {
        $user = User::factory()->create([
            'tier' => 'unlimited',
        ]);

        $response = $this->actingAs($user)->postJson(route('custom-stories.generate-ai'), [
            'prompt' => 'Phỏng vấn xin việc Tech Lead tại Google Singapore',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'data' => [
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
            ],
        ]);
    }

    public function test_user_can_store_custom_scenario_and_redirect_to_catalog(): void
    {
        $user = User::factory()->create([
            'tier' => 'unlimited',
        ]);

        $payload = [
            'title' => 'Đàm phán tăng lương và thăng chức',
            'category' => 'workplace',
            'location_name' => 'Phòng họp Giám đốc Nhân sự',
            'detailed_description' => 'Bạn trao đổi trực tiếp với HR Director về kết quả công việc năm qua và đề xuất nâng bậc lương.',
            'user_role' => 'Nhân viên xuất sắc',
            'ai_name' => 'Rachel',
            'ai_role' => 'HR Director',
            'twist_prompt' => 'Rachel thông báo quỹ ngân sách công ty năm nay bị siết 15% và đề nghị tăng ngày nghỉ thay vì lương.',
            'goals' => "Trình bày các đóng góp trọng yếu\nĐàm phán phương án kết hợp giữa lương và cổ phần ưu đãi",
            'first_message' => 'Good morning. Thanks for setting up this meeting today.',
            'level' => 'B2',
            'ai_tone' => 'professional',
            'action' => 'save',
        ];

        $response = $this->actingAs($user)->post(route('custom-stories.store'), $payload);

        $response->assertRedirect(route('scenarios.index'));
        $response->assertSessionHas('success');

        $scenario = Scenario::where('title', 'Đàm phán tăng lương và thăng chức')->first();
        $this->assertNotNull($scenario);
        $this->assertEquals($user->id, $scenario->user_id);
        $this->assertTrue($scenario->is_custom);
        $this->assertEquals('workplace', $scenario->category);

        $character = Character::where('scenario_id', $scenario->id)->first();
        $this->assertNotNull($character);
        $this->assertEquals('Rachel', $character->name);
        $this->assertEquals('HR Director', $character->role);
        $this->assertTrue($character->is_default);

        $hints = ScenarioHint::where('scenario_id', $scenario->id)->get();
        $this->assertGreaterThanOrEqual(1, $hints->count());
    }

    public function test_user_can_start_practice_directly_from_creation(): void
    {
        $user = User::factory()->create([
            'tier' => 'unlimited',
        ]);

        $payload = [
            'title' => 'Kiểm tra hải quan sân bay London Heathrow',
            'category' => 'travel',
            'location_name' => 'Heathrow Airport Terminal 2',
            'detailed_description' => 'Bạn trả lời các câu hỏi kiểm tra nhập cảnh của sĩ quan hải quan.',
            'user_role' => 'Khách du lịch',
            'ai_name' => 'Officer Miller',
            'ai_role' => 'Sĩ quan Nhập cảnh',
            'twist_prompt' => 'Sĩ quan yêu cầu xuất trình thư xác nhận đặt phòng khách sạn và vé máy bay khứ hồi.',
            'goals' => 'Trả lời dứt khoát mục đích chuyến đi',
            'first_message' => 'Passport and landing card, please.',
            'level' => 'A2',
            'ai_tone' => 'challenging',
            'action' => 'start_practice',
        ];

        $response = $this->actingAs($user)->post(route('custom-stories.store'), $payload);

        $scenario = Scenario::where('title', 'Kiểm tra hải quan sân bay London Heathrow')->first();
        $this->assertNotNull($scenario);

        $session = PracticeSession::where('scenario_id', $scenario->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals($user->id, $session->user_id);
        $this->assertEquals('A2', $session->level);

        $response->assertRedirect(route('practice.room', ['sessionCode' => $session->session_code]));
    }

    public function test_custom_scenario_visible_in_author_catalog_and_hidden_from_others(): void
    {
        $author = User::factory()->create(['tier' => 'unlimited']);
        $otherUser = User::factory()->create(['tier' => 'unlimited']);

        $scenario = Scenario::factory()->create([
            'user_id' => $author->id,
            'title' => 'Story Độc Quyền Của Author',
            'is_custom' => true,
            'is_public' => false,
            'is_active' => true,
        ]);

        Character::factory()->create([
            'scenario_id' => $scenario->id,
            'is_default' => true,
        ]);

        // Author visits catalog
        $authorResponse = $this->actingAs($author)->get(route('scenarios.index'));
        $authorResponse->assertOk();
        $authorResponse->assertSee('Story Độc Quyền Của Author');
        $authorResponse->assertSee('Tự tạo');

        // Other user visits catalog
        $otherResponse = $this->actingAs($otherUser)->get(route('scenarios.index'));
        $otherResponse->assertOk();
        $otherResponse->assertDontSee('Story Độc Quyền Của Author');
    }
}
