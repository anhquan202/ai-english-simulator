<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\PracticeSession;
use App\Models\Scenario;
use App\Models\StickyNote;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MultiLanguagePracticeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_practice_session_can_be_stored_with_target_language(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::first() ?? Scenario::create([
            'title' => 'Quán cà phê đặc sản',
            'slug' => 'test-coffee-shop',
            'category' => 'dining',
            'location_name' => 'Tokyo, Japan',
            'context_note' => 'Gọi cà phê bằng tiếng Nhật',
            'estimated_minutes' => 5,
        ]);

        $character = Character::first() ?? Character::create([
            'scenario_id' => $scenario->id,
            'name' => 'Kenji',
            'role' => 'Barista',
            'role_vi' => 'Nhân viên pha chế',
            'system_prompt' => 'You are Kenji, a barista in Tokyo.',
            'first_message' => 'Irasshaimase! Gochuumon wa ikaga deshō ka?',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('practice.store'), [
            'scenario_id' => $scenario->id,
            'character_id' => $character->id,
            'level' => 'A2',
            'practice_language' => 'ja',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('practice_sessions', [
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'character_id' => $character->id,
            'practice_language' => 'ja',
            'level' => 'A2',
        ]);
    }

    public function test_practice_session_defaults_to_english(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::first();
        $character = $scenario->characters->first();

        $response = $this->actingAs($user)->postJson(route('practice.store'), [
            'scenario_id' => $scenario->id,
            'character_id' => $character->id,
            'level' => 'B1',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('practice_sessions', [
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'practice_language' => 'en',
            'level' => 'B1',
        ]);
    }

    public function test_practice_room_loads_with_language_metadata(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::first();
        $character = $scenario->characters->first();

        $session = PracticeSession::create([
            'session_code' => 'test-room-session-ja',
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'character_id' => $character->id,
            'level' => 'A2',
            'practice_language' => 'ja',
            'status' => 'in_progress',
            'current_stage' => 'AI_SPEAKING',
        ]);

        $response = $this->actingAs($user)->get(route('practice.room', $session->session_code));

        $response->assertOk();
        $response->assertViewIs('practice.room');
        $response->assertSee('Tiếng Nhật');
        $response->assertSee('🇯🇵');
    }

    public function test_practice_summary_loads_with_language_metadata_and_pinned_notes(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::first();
        $character = $scenario->characters->first();

        $session = PracticeSession::create([
            'session_code' => 'test-summary-session-zh',
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'character_id' => $character->id,
            'level' => 'A1',
            'practice_language' => 'zh',
            'status' => 'completed',
            'csi_score' => 92,
        ]);

        StickyNote::create([
            'user_id' => $user->id,
            'practice_session_id' => $session->id,
            'language' => 'zh',
            'phrase' => '麻烦少冰',
            'meaning_vi' => 'Làm ơn cho ít đá',
            'review_stage' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('practice.summary', $session->session_code));

        $response->assertOk();
        $response->assertViewIs('practice.summary');
        $response->assertSee('Tiếng Trung');
        $response->assertSee('🇨🇳');
        $response->assertSee('麻烦少冰');
        $response->assertSee('Làm ơn cho ít đá');
    }

    public function test_scenario_lobby_accepts_language_query_param(): void
    {
        $scenario = Scenario::first();
        $response = $this->get(route('scenarios.show', ['scenario' => $scenario->slug, 'lang' => 'ko']));

        $response->assertOk();
        $response->assertViewIs('scenarios.show');
        $response->assertSee('Tiếng Hàn');
        $response->assertSee('🇰🇷');
    }
}
