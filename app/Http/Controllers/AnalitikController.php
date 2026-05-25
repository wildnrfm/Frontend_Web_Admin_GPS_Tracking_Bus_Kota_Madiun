<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class AnalitikController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        try {
            $authService = app(\App\Services\AuthService::class);
            $authUser = $authService->getCurrentUser();

            // Fetch analytics data
            $stats = [
                'total_students' => 0,
                'total_buses' => 0,
                'total_drivers' => 0,
                'total_stops' => 0,
                'pending_count' => 0,
            ];

            try {
                $studentData = ApiClient::get('/students', ['per_page' => 1]);
                $stats['total_students'] = $studentData['meta']['total'] ?? 0;
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch student count: ' . $e->getMessage());
            }

            try {
                $busData = ApiClient::get('/buses', ['per_page' => 1]);
                $stats['total_buses'] = $busData['meta']['total'] ?? 0;
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch bus count: ' . $e->getMessage());
            }

            try {
                $driverData = ApiClient::get('/drivers', ['per_page' => 1]);
                $stats['total_drivers'] = $driverData['meta']['total'] ?? 0;
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch driver count: ' . $e->getMessage());
            }

            try {
                $stopData = ApiClient::get('/stops', ['per_page' => 1]);
                $stats['total_stops'] = $stopData['meta']['total'] ?? 0;
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch stop count: ' . $e->getMessage());
            }

            return view('admin.analitik', [
                'authUser' => $authUser,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Analitik page error: ' . $e->getMessage());
            return view('admin.analitik', [
                'authUser' => [],
                'stats' => [],
            ]);
        }
    }

    /**
     * Export analytics to PDF
     */
    public function export(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            
            // Panggil API backend untuk mendownload PDF
            $response = ApiClient::get('/reports/admin/download-pdf', ['tanggal' => $date]);
            
            if ($response->successful()) {
                // Stream PDF content directly to browser
                return response($response->body(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="Laporan_Admin_' . $date . '.pdf"');
            }
            
            \Log::error('Gagal mengambil PDF dari API: Status ' . $response->status() . ' - ' . $response->body());
            return back()->with('error', 'Gagal mengambil data laporan dari server API. Status: ' . $response->status());
        } catch (\Exception $e) {
            \Log::error('Export analytics error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
