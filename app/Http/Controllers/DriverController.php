<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display list of drivers
     */
    public function index()
    {
        try {
            $response = ApiClient::get('/drivers', ['per_page' => 100]);
            $drivers = $response['data'] ?? [];
            
            // Ensure each item has required fields
            $drivers = array_map(function($item) {
                return array_merge([
                    'id' => null,
                    'name' => null,
                    'email' => null,
                    'phone' => null,
                    'sim' => null,
                    'status' => 'active'
                ], is_array($item) ? $item : []);
            }, $drivers);
            
            return view('admin.driver', ['siswa' => $drivers]);
        } catch (\Exception $e) {
            \Log::error('Driver fetch failed: ' . $e->getMessage());
            return view('admin.driver', ['siswa' => []]);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.driver');
    }

    /**
     * Store new driver
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:20',
                'sim' => 'required|string|max:50',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $result = ApiClient::post('/drivers', $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.driver')->with('success', 'Driver berhasil ditambahkan');
            }
            
            return back()->with('error', 'Gagal menambahkan driver');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show driver detail
     */
    public function show($id)
    {
        try {
            $driver = ApiClient::get("/drivers/{$id}");
            return view('admin.driver', ['driver' => $driver['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.driver')->with('error', 'Driver tidak ditemukan');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $driver = ApiClient::get("/drivers/{$id}");
            return view('admin.driver', ['driver' => $driver['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.driver')->with('error', 'Driver tidak ditemukan');
        }
    }

    /**
     * Update driver
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'sim' => 'required|string|max:50',
            ]);

            $result = ApiClient::put("/drivers/{$id}", $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.driver')->with('success', 'Driver berhasil diperbarui');
            }
            
            return back()->with('error', 'Gagal memperbarui driver');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete driver
     */
    public function destroy($id)
    {
        try {
            ApiClient::delete("/drivers/{$id}");
            return redirect()->route('admin.driver')->with('success', 'Driver berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus driver');
        }
    }
}
