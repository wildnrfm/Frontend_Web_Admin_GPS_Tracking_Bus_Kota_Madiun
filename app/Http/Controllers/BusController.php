<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display list of buses
     */
    public function index()
    {
        try {
            $response = ApiClient::get('/buses', ['per_page' => 100]);
            $buses = $response['data'] ?? [];
            
            // Ensure each item has required fields
            $buses = array_map(function($item) {
                return array_merge([
                    'id' => null,
                    'nama' => null,
                    'kode_bus' => null,
                    'plat' => null,
                    'driver_name' => null,
                    'status' => 'active'
                ], is_array($item) ? $item : []);
            }, $buses);
            
            return view('admin.bus', ['siswa' => $buses]);
        } catch (\Exception $e) {
            \Log::error('Bus fetch failed: ' . $e->getMessage());
            return view('admin.bus', ['siswa' => []]);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.bus');
    }

    /**
     * Store new bus
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'kode_bus' => 'required|string|max:50',
                'plat' => 'required|string|max:20',
                'driver_id' => 'nullable|integer',
            ]);

            $result = ApiClient::post('/buses', $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.bus')->with('success', 'Bus berhasil ditambahkan');
            }
            
            return back()->with('error', 'Gagal menambahkan bus');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show bus detail
     */
    public function show($id)
    {
        try {
            $bus = ApiClient::get("/buses/{$id}");
            return view('admin.bus', ['bus' => $bus['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.bus')->with('error', 'Bus tidak ditemukan');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $bus = ApiClient::get("/buses/{$id}");
            return view('admin.bus', ['bus' => $bus['data'] ?? []]);
        } catch (\Exception $e) {
            return redirect()->route('admin.bus')->with('error', 'Bus tidak ditemukan');
        }
    }

    /**
     * Update bus
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'kode_bus' => 'required|string|max:50',
                'plat' => 'required|string|max:20',
                'driver_id' => 'nullable|integer',
            ]);

            $result = ApiClient::put("/buses/{$id}", $validated);
            
            if ($result && isset($result['data'])) {
                return redirect()->route('admin.bus')->with('success', 'Bus berhasil diperbarui');
            }
            
            return back()->with('error', 'Gagal memperbarui bus');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete bus
     */
    public function destroy($id)
    {
        try {
            ApiClient::delete("/buses/{$id}");
            return redirect()->route('admin.bus')->with('success', 'Bus berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus bus');
        }
    }
}
