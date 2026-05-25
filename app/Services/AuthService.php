<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthService
{
    /**
     * Login user with email and password
     * Call API endpoint /auth/login
     * Also revokes all previous sessions (single device login)
     */
    public function login(string $email, string $password): array
    {
        try {
            $response = ApiClient::post('/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => $response->json('message', 'Login failed'),
                ];
            }

            $data = $response->json('data') ?? $response->json();
            $token = $data['token'] ?? null;
            $userData = $data['user'] ?? null;

            if (!$token || !$userData) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from server',
                ];
            }

            // Check if user is admin
            if (($userData['role'] ?? null) !== User::ROLE_ADMIN) {
                return [
                    'success' => false,
                    'message' => 'Only admin users can access this application',
                ];
            }

            // Clear any existing session data (single device)
            Session::forget('api_token');
            Session::forget('admin_user');
            Session::flush();

            // Store new token and user data in session
            Session::put('api_token', $token);
            Session::put('admin_user', $userData);
            Session::put('login_time', now()->timestamp);
            Session::put('device_id', $this->generateDeviceId());

            return [
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $userData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $token = Session::get('api_token');
        
        try {
            // Call logout endpoint to invalidate token on API side
            if ($token) {
                ApiClient::post('/auth/logout', []);
            }
        } catch (\Exception $e) {
            // Even if API call fails, clear session
        }

        // Clear all session data
        Session::forget('api_token');
        Session::forget('admin_user');
        Session::forget('login_time');
        Session::forget('device_id');
        Session::flush();
    }

    /**
     * Get current authenticated user from session
     */
    public function getCurrentUser(): ?array
    {
        return Session::get('admin_user');
    }

    /**
     * Get current API token from session
     */
    public function getToken(): ?string
    {
        return Session::get('api_token');
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return Session::has('api_token') && Session::has('admin_user');
    }

    /**
     * Refresh user data from API
     */
    public function refreshUser(): ?array
    {
        try {
            $response = ApiClient::get('/auth/me');

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json('data') ?? $response->json('user') ?? $response->json();

            if ($data) {
                Session::put('admin_user', $data);
                return $data;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate current token with API
     * Returns true if token is valid, false otherwise
     */
    public function validateToken(): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $response = ApiClient::get('/auth/me');
            
            // If 401 Unauthorized, token is invalid
            if ($response->status() === 401) {
                $this->logout();
                return false;
            }

            return $response->successful();
        } catch (\Exception $e) {
            // Keep user logged in if API server is temporarily busy/unreachable
            \Log::warning('validateToken API connection error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Generate unique device identifier
     */
    private function generateDeviceId(): string
    {
        return md5(request()->userAgent() . request()->ip());
    }
}

