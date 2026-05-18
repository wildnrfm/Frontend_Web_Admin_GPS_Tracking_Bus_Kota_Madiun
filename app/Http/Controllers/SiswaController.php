<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Display list of siswa
     */
    public function index()
    {
        try {
            $response = ApiClient::get('/students', ['per_page' => 100]);
            $siswa = $response['data'] ?? [];
            
            // Ensure each item has required fields
            $siswa = array_map(function($item) {
                return array_merge([
                    'id' => null,
                    'name' => null,
                    'email' => null,
                    'phone' => null,
                    'school' => null,
                    'status' => 'pending'
                ], is_array($item) ? $item : []);
            }, $siswa);
            
            return view('admin.siswa', ['siswa' => $siswa]);
        } catch (\Exception $e) {
            \Log::error('Siswa fetch failed: ' . $e->getMessage());
            return view('admin.siswa', ['siswa' => []]);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.siswa');
    }

    /**
     * Store new siswa
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'school' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $result = ApiClient::post('/students', $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil ditambahkan');
            }
            
            return back()->with('error', 'Gagal menambahkan siswa');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show siswa detail
     */
    public function show($id)
    {
        try {
            $siswa = ApiClient::get("/students/{$id}");
            return view('admin.siswa', ['siswa' => $siswa['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa')->with('error', 'Siswa tidak ditemukan');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $siswa = ApiClient::get("/students/{$id}");
            return view('admin.siswa', ['siswa' => $siswa['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa')->with('error', 'Siswa tidak ditemukan');
        }
    }

    /**
     * Update siswa
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'school' => 'required|string',
            ]);

            $result = ApiClient::put("/students/{$id}", $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil diperbarui');
            }
            
            return back()->with('error', 'Gagal memperbarui siswa');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete siswa
     */
    public function destroy($id)
    {
        try {
            ApiClient::delete("/students/{$id}");
            return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus siswa');
        }
    }

    /**
     * Approve siswa
     */
    public function approve($id)
    {
        try {
            $result = ApiClient::post("/students/{$id}/approve", []);
            
            if ($result) {
                return redirect()->back()->with('success', 'Siswa berhasil disetujui');
            }
            
            return back()->with('error', 'Gagal menyetujui siswa');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject siswa
     */
    public function reject($id)
    {
        try {
            $result = ApiClient::post("/students/{$id}/reject", []);
            
            if ($result) {
                return redirect()->back()->with('success', 'Siswa berhasil ditolak');
            }
            
            return back()->with('error', 'Gagal menolak siswa');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
