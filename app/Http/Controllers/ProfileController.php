<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Sync and refresh the PHP session with the latest database state from the API
     */
    private function syncSessionData()
    {
        $token = session('api_token');
        if ($token) {
            try {
                $apiBase = rtrim(env('API_BASE_URL', 'http://localhost:8000/api'), '/');
                $headers = ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token];
                if (str_contains($apiBase, 'ngrok')) {
                    $headers['ngrok-skip-browser-warning'] = 'true';
                }

                $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                    ->withoutVerifying()
                    ->timeout(5)
                    ->get($apiBase . '/auth/me');

                if ($response->successful()) {
                    $userData = $response->json('data.user') ?? $response->json('data') ?? null;
                    if ($userData) {
                        session(['admin_user' => $userData]);
                    }
                }
            } catch (\Exception $e) {
                // Ignore and use current session
            }
        }
    }

    /**
     * Display admin profile page
     */
    public function index()
    {
        $this->syncSessionData();
        $user = session('admin_user');
        
        return view('admin.profil', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $this->syncSessionData();
        $user = session('admin_user');

        
        return view('admin.edit-profil', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Update user in API
        $response = \App\Services\ApiClient::put('/admin/profile', $validated);
        
        if ($response['success']) {
            session(['admin_user' => $response['data']]);
            return redirect()->route('admin.profil')->with('success', 'Profil berhasil diperbarui');
        }

        return back()->with('error', $response['message'] ?? 'Gagal memperbarui profil');
    }

    /**
     * Refresh admin_user session from API /auth/me
     * Called via AJAX after photo upload or delete so the session reflects
     * the new photo_url without requiring a full logout/login.
     */
    public function refreshSession(Request $request)
    {
        $token = session('api_token');
        if (!$token) {
            return response()->json(['ok' => false, 'message' => 'Not authenticated'], 401);
        }

        try {
            $apiBase = rtrim(env('API_BASE_URL', 'http://localhost:8000/api'), '/');
            $headers = ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token];
            if (str_contains($apiBase, 'ngrok')) {
                $headers['ngrok-skip-browser-warning'] = 'true';
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->withoutVerifying()
                ->timeout(10)
                ->get($apiBase . '/auth/me');

            if ($response->successful()) {
                $userData = $response->json('data.user') ?? $response->json('data') ?? null;
                if ($userData) {
                    session(['admin_user' => $userData]);
                    return response()->json(['ok' => true]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail — page reload will still work with old session
        }

        return response()->json(['ok' => false]);
    }
}
