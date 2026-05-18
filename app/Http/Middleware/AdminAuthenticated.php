<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated in session
        if (!$this->authService->isAuthenticated()) {
            return redirect(route('login'))
                ->with('error', 'Please login to continue');
        }

        // Validate token with API (check if session is still valid on server)
        if (!$this->authService->validateToken()) {
            return redirect(route('login'))
                ->with('error', 'Session expired or invalid. Please login again');
        }

        // Get current user
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return redirect(route('login'))
                ->with('error', 'Session expired, please login again');
        }

        // Check if user is admin
        if (($user['role'] ?? null) !== 'admin') {
            return redirect(route('login'))
                ->with('error', 'Only admin users can access this application');
        }

        // Share user data with all views
        view()->share('authUser', $user);

        return $next($request);
    }
}
