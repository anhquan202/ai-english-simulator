<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StickyNote;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StickyNoteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_guest_cannot_store_sticky_note(): void
    {
        $response = $this->postJson(route('notes.store'), [
            'phrase' => 'お会計を別々で',
            'meaning_vi' => 'Tính tiền riêng từng người',
            'language' => 'ja',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_store_sticky_note(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('notes.store'), [
            'phrase' => '一口吃不成胖子',
            'phonetic' => 'Yī kǒu chī bù chéng pàngzi',
            'meaning_vi' => 'Dục tốc bất đạt',
            'user_note' => 'Học mỗi ngày một chút',
            'language' => 'zh',
            'color' => 'yellow',
            'pin_style' => 'washi_kraft',
            'stamp_emoji' => '🍲',
            'stamp_name' => 'Lẩu Tứ Xuyên',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('note.phrase', '一口吃不成胖子');
        $response->assertJsonPath('note.language', 'zh');
        $response->assertJsonPath('note.review_stage', 1);

        $this->assertDatabaseHas('sticky_notes', [
            'user_id' => $user->id,
            'phrase' => '一口吃不成胖子',
            'language' => 'zh',
            'meaning_vi' => 'Dục tốc bất đạt',
        ]);
    }

    public function test_user_can_view_corkboard(): void
    {
        $user = User::factory()->create();

        StickyNote::create([
            'user_id' => $user->id,
            'phrase' => '눈치가 빠르다',
            'meaning_vi' => 'Tinh ý, nhạy bén',
            'language' => 'ko',
            'review_stage' => 1,
            'next_review_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertOk();
        $response->assertViewIs('notes.index');
        $response->assertSee('눈치가 빠르다');
        $response->assertSee('Tinh ý, nhạy bén');
    }

    public function test_user_can_review_note_success_advances_stage(): void
    {
        $user = User::factory()->create();

        $note = StickyNote::create([
            'user_id' => $user->id,
            'phrase' => '一期一会',
            'meaning_vi' => 'Đời người chỉ gặp một lần',
            'language' => 'ja',
            'review_stage' => 1,
            'consecutive_correct' => 0,
            'next_review_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->postJson(route('notes.review', $note->id), [
            'remembered' => true,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('note.review_stage', 2);
        $response->assertJsonPath('note.is_mastered', false);

        $note->refresh();
        $this->assertSame(2, $note->review_stage);
        $this->assertSame(1, $note->consecutive_correct);
    }

    public function test_user_can_review_note_failure_resets_stage(): void
    {
        $user = User::factory()->create();

        $note = StickyNote::create([
            'user_id' => $user->id,
            'phrase' => '武士道とは死ぬことと見つけたり',
            'meaning_vi' => 'Tinh thần samurai',
            'language' => 'ja',
            'review_stage' => 4,
            'consecutive_correct' => 3,
            'next_review_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->postJson(route('notes.review', $note->id), [
            'remembered' => false,
        ]);

        $response->assertOk();
        $response->assertJsonPath('note.review_stage', 1);

        $note->refresh();
        $this->assertSame(1, $note->review_stage);
        $this->assertSame(0, $note->consecutive_correct);
    }

    public function test_user_cannot_review_another_users_note(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $note = StickyNote::create([
            'user_id' => $owner->id,
            'phrase' => 'Secret note',
            'meaning_vi' => 'Ghi chú bí mật',
            'language' => 'en',
            'review_stage' => 1,
        ]);

        $response = $this->actingAs($attacker)->postJson(route('notes.review', $note->id), [
            'remembered' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_note(): void
    {
        $user = User::factory()->create();

        $note = StickyNote::create([
            'user_id' => $user->id,
            'phrase' => 'Note to be deleted',
            'meaning_vi' => 'Xóa note này',
            'language' => 'en',
            'review_stage' => 1,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('notes.destroy', $note->id));

        $response->assertOk();
        $this->assertDatabaseMissing('sticky_notes', [
            'id' => $note->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_note(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $note = StickyNote::create([
            'user_id' => $owner->id,
            'phrase' => 'Owner Note',
            'meaning_vi' => 'Không thể xóa',
            'language' => 'en',
            'review_stage' => 1,
        ]);

        $response = $this->actingAs($attacker)->deleteJson(route('notes.destroy', $note->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('sticky_notes', [
            'id' => $note->id,
        ]);
    }

    public function test_user_can_update_reminder_settings(): void
    {
        $user = User::factory()->create([
            'reminder_enabled' => true,
            'reminder_time' => '20:00',
        ]);

        $response = $this->actingAs($user)->postJson(route('notes.reminder'), [
            'reminder_enabled' => true,
            'reminder_time' => '07:30',
            'reminder_days' => ['T2', 'T4', 'T6'],
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertTrue($user->reminder_enabled);
        $this->assertSame('07:30', $user->reminder_time);
        $this->assertSame(['T2', 'T4', 'T6'], $user->reminder_days);
    }
}
