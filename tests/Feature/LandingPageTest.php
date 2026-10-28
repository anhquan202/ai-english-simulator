<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test public landing page loads successfully with Warm Humanist elements.
     */
    public function test_public_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('mở lời.');
        $response->assertSee('AI REAL-WORLD ENGLISH SIMULATOR');
        $response->assertSee('Vượt qua nỗi sợ mở lời.');
        $response->assertSee('VÀO PHÒNG LUYỆN NÓI NGAY');
    }

    /**
     * Test practice catalog page loads with to-C navigation (layouts.app).
     */
    public function test_practice_catalog_renders_inside_toc_layout(): void
    {
        $response = $this->get('/practice');

        $response->assertStatus(200);
        $response->assertSee('Luyện nói');
        $response->assertSee('Lịch sử');
        $response->assertSee('Tiến bộ');
    }

    /**
     * Test authenticated user sees personalized workspace button on landing page.
     */
    public function test_authenticated_user_sees_enter_workspace_cta_on_landing_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Minh Anh',
            'username' => 'minhanh_test',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Vào phòng học');
    }

    /**
     * Test toC header logo links to landing page and user dropdown only contains logout.
     */
    public function test_toc_header_logo_links_to_landing_page_and_user_dropdown_only_contains_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Minh Anh',
            'username' => 'minhanh_user',
        ]);

        $response = $this->actingAs($user)->get('/practice');

        $response->assertStatus(200);

        // Logo links to landing page
        $response->assertSee(route('landing'));

        // Header tabs remain intact
        $response->assertSee(route('scenarios.index'));
        $response->assertSee(route('history.index'));

        // Dropdown contains user info and logout, but no other menu options
        $response->assertSee('Đăng xuất');
        $response->assertDontSee('Hồ sơ của tôi');
        $response->assertDontSee('Cài đặt tài khoản');
    }
}

