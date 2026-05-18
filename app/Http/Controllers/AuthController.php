<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email format is invalid',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if ($result['success']) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', $result['message']);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', $result['message']);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        $this->authService->logout();
        return redirect(route('login'))
            ->with('success', 'Logged out successfully');
    }

    /**
     * Get current user (API endpoint for frontend)
     */
    public function getCurrentUser()
    {
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        return response()->json(['data' => $user], 200);
    }
}
