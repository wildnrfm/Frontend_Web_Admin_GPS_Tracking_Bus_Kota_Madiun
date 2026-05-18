<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display admin profile page
     */
    public function index()
    {
        // Get admin user from session
        $user = session('admin_user');
        
        return view('admin.profil', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
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
}
