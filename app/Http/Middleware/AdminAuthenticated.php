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
            if ($request->is('api-proxy/*') || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect(route('login'))
                ->with('error', 'Please login to continue');
        }

        // Skip validateToken for api-proxy requests to prevent concurrent request bottlenecks/timeouts.
        // The backend API will validate the token when the request is proxied anyway.
        if (!$request->is('api-proxy/*')) {
            // Validate token with API (check if session is still valid on server)
            if (!$this->authService->validateToken()) {
                return redirect(route('login'))
                    ->with('error', 'Session expired or invalid. Please login again');
            }
        }

        // Get current user
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            if ($request->is('api-proxy/*') || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Session expired'], 401);
            }
            return redirect(route('login'))
                ->with('error', 'Session expired, please login again');
        }

        // Check if user is admin
        if (($user['role'] ?? null) !== 'admin') {
            if ($request->is('api-proxy/*') || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return redirect(route('login'))
                ->with('error', 'Only admin users can access this application');
        }

        // Share user data with all views
        view()->share('authUser', $user);

        return $next($request);
    }
}
