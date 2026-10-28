<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    /**
     * 1. GET /login and GET /register return 200 OK.
     */
    public function test_login_and_register_pages_return_successful_response(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
    }

    /**
     * 2. POST /register creates new user, hashes password, auto-logins and redirects to /.
     */
    public function test_user_can_register_via_standard_form(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'password' => 'secret1234',
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('scenarios.index'));

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@learner.molo.local',
        ]);

        $user = User::where('username', 'johndoe')->firstOrFail();
        $this->assertTrue(Hash::check('secret1234', $user->password));
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    /**
     * 3. POST /register reports validation errors when username duplicate or password < 8 chars.
     */
    public function test_register_fails_with_duplicate_username_or_short_password(): void
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'username' => 'existing_user',
            'email' => 'existing_user@learner.molo.local',
            'password' => Hash::make('password123'),
        ]);

        // Duplicate username
        $duplicateResponse = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'existing_user',
            'password' => 'validpassword123',
        ]);

        $duplicateResponse->assertSessionHasErrors([
            'username' => 'Tên đăng nhập này đã được sử dụng. Hãy chọn tên khác.',
        ]);

        // Short password (< 8 chars)
        $shortPassResponse = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'jane_unique',
            'password' => 'short',
        ]);

        $shortPassResponse->assertSessionHasErrors([
            'password' => 'Mật khẩu cần có ít nhất 8 ký tự.',
        ]);
    }

    /**
     * 4. POST /login logs in successfully with correct username + password.
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'email' => 'testuser@learner.molo.local',
            'password' => Hash::make('mypassword123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'mypassword123',
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    /**
     * 5. POST /login reports exact Figma message: "Mật khẩu chưa đúng. Bạn thử nhập lại nhé." on wrong password.
     */
    public function test_login_fails_with_exact_figma_error_message_on_wrong_password(): void
    {
        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser_wrong',
            'email' => 'testuser_wrong@learner.molo.local',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser_wrong',
            'password' => 'incorrect_password',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Mật khẩu chưa đúng. Bạn thử nhập lại nhé.',
        ]);
        $this->assertFalse(Auth::check());
    }

    /**
     * 6. POST /login and POST /register via JSON/AJAX return HTTP 200/201 and 422 JSON with correct structure.
     */
    public function test_ajax_register_and_login_return_proper_json_responses(): void
    {
        // AJAX Register Success
        $registerResponse = $this->postJson('/register', [
            'first_name' => 'Ajax',
            'last_name' => 'Tester',
            'username' => 'ajax_user',
            'password' => 'password123',
        ]);

        $registerResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công.',
                'user' => [
                    'username' => 'ajax_user',
                    'first_name' => 'Ajax',
                    'last_name' => 'Tester',
                ],
            ]);

        // AJAX Register Validation Error
        $registerErrorResponse = $this->postJson('/register', [
            'first_name' => 'Ajax',
            'last_name' => 'Tester',
            'username' => 'ajax_user', // duplicate
            'password' => '123', // short
        ]);

        $registerErrorResponse->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password'])
            ->assertJsonFragment([
                'username' => ['Tên đăng nhập này đã được sử dụng. Hãy chọn tên khác.'],
            ]);

        // AJAX Logout
        $this->postJson('/logout')->assertStatus(200);

        // AJAX Login Success
        $loginResponse = $this->postJson('/login', [
            'username' => 'ajax_user',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công.',
                'user' => [
                    'username' => 'ajax_user',
                ],
            ]);

        // AJAX Login Wrong Password Error (Figma message)
        $loginErrorResponse = $this->postJson('/login', [
            'username' => 'ajax_user',
            'password' => 'wrong_password',
        ]);

        $loginErrorResponse->assertStatus(422)
            ->assertJson([
                'message' => 'Mật khẩu chưa đúng. Bạn thử nhập lại nhé.',
                'errors' => [
                    'password' => ['Mật khẩu chưa đúng. Bạn thử nhập lại nhé.'],
                ],
            ]);
    }

    /**
     * 7. POST /logout logs out successfully and redirects to /.
     */
    public function test_user_can_logout_and_is_redirected_to_home(): void
    {
        $user = User::create([
            'first_name' => 'Logout',
            'last_name' => 'Test',
            'username' => 'logout_user',
            'email' => 'logout_user@learner.molo.local',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);
        $this->assertTrue(Auth::check());

        $response = $this->post('/logout');

        $response->assertRedirect(route('scenarios.index'));
        $this->assertFalse(Auth::check());
    }
}
