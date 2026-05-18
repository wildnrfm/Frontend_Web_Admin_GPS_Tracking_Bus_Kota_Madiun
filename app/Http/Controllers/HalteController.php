<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class HalteController extends Controller
{
    /**
     * Display list of haltes
     */
    public function index()
    {
        try {
            $response = ApiClient::get('/haltes', ['per_page' => 100]);
            $haltes = $response['data'] ?? [];
            
            // Ensure each item has required fields
            $haltes = array_map(function($item) {
                return array_merge([
                    'id' => null,
                    'nama' => null,
                    'alamat' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'status' => 'active'
                ], is_array($item) ? $item : []);
            }, $haltes);
            
            return view('admin.halte', ['siswa' => $haltes]);
        } catch (\Exception $e) {
            \Log::error('Halte fetch failed: ' . $e->getMessage());
            return view('admin.halte', ['siswa' => []]);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.halte');
    }

    /**
     * Store new halte
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $result = ApiClient::post('/haltes', $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.halte')->with('success', 'Halte berhasil ditambahkan');
            }
            
            return back()->with('error', 'Gagal menambahkan halte');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show halte detail
     */
    public function show($id)
    {
        try {
            $halte = ApiClient::get("/haltes/{$id}");
            return view('admin.halte', ['halte' => $halte['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.halte')->with('error', 'Halte tidak ditemukan');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $halte = ApiClient::get("/haltes/{$id}");
            return view('admin.halte', ['halte' => $halte['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.halte')->with('error', 'Halte tidak ditemukan');
        }
    }

    /**
     * Update halte
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $result = ApiClient::put("/haltes/{$id}", $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.halte')->with('success', 'Halte berhasil diperbarui');
            }
            
            return back()->with('error', 'Gagal memperbarui halte');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete halte
     */
    public function destroy($id)
    {
        try {
            ApiClient::delete("/haltes/{$id}");
            return redirect()->route('admin.halte')->with('success', 'Halte berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus halte');
        }
    }
}
