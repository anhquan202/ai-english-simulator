<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display login form view.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Display registration form view.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user login attempt.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = [
            'username' => (string) $request->input('username'),
            'password' => (string) $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công.',
                    'user' => Auth::user(),
                    'redirect_url' => url('/'),
                ]);
            }

            return redirect()->intended('/');
        }

        $errorMessage = 'Mật khẩu chưa đúng. Bạn thử nhập lại nhé.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $errorMessage,
                'errors' => [
                    'password' => [$errorMessage],
                ],
            ], 422);
        }

        return back()
            ->withErrors(['password' => $errorMessage])
            ->withInput($request->except('password'));
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'username.unique' => 'Tên đăng nhập này đã được sử dụng. Hãy chọn tên khác.',
            'password.min' => 'Mật khẩu cần có ít nhất 8 ký tự.',
            'first_name.required' => 'Vui lòng nhập tên của bạn.',
            'last_name.required' => 'Vui lòng nhập họ của bạn.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $firstName = trim((string) $validated['first_name']);
        $lastName = trim((string) $validated['last_name']);
        $username = trim((string) $validated['username']);

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'name' => trim($firstName.' '.$lastName),
            'email' => $username.'@learner.molo.local',
            'password' => Hash::make((string) $validated['password']),
            'native_language' => 'vi',
            'target_language' => 'en',
            'target_level' => 'A2',
            'default_level' => 'A2',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công.',
                'user' => $user,
                'redirect_url' => url('/'),
            ], 201);
        }

        return redirect()->route('scenarios.index');
    }

    /**
     * Handle user logout and invalidate session.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã đăng xuất thành công.',
                'redirect_url' => route('scenarios.index'),
            ]);
        }

        return redirect()->route('scenarios.index');
    }
}
