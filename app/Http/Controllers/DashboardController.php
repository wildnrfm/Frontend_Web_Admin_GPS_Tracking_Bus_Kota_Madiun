<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard with real-time stats from API
     */
    public function index()
    {
        // Default stats (jika API gagal, tetap bisa render halaman)
        $stats = [
            'total_buses' => 0,
            'total_students' => 0,
            'total_drivers' => 0,
            'total_haltes' => 0,
            'total_admins' => 0,
            'pending_count' => 0,
        ];

        try {
            $token = \Illuminate\Support\Facades\Session::get('api_token');
            \Log::info('Dashboard: Attempting to fetch stats with token: ' . ($token ? 'present' : 'missing'));

            // Fetch total buses (ALL buses, not just operating)
            $busesData = ApiClient::get('/buses', ['per_page' => 1])->json();
            \Log::info('Buses response:', $busesData ?? []);
            if ($busesData && isset($busesData['pagination'])) {
                $stats['total_buses'] = $busesData['pagination']['total'] ?? 0;
            }

            // Fetch total students
            $studentsData = ApiClient::get('/students', ['per_page' => 1])->json();
            \Log::info('Students response:', $studentsData ?? []);
            if ($studentsData && isset($studentsData['pagination'])) {
                $stats['total_students'] = $studentsData['pagination']['total'] ?? 0;
            }

            // Fetch total drivers
            $driversData = ApiClient::get('/drivers', ['per_page' => 1])->json();
            \Log::info('Drivers response:', $driversData ?? []);
            if ($driversData && isset($driversData['pagination'])) {
                $stats['total_drivers'] = $driversData['pagination']['total'] ?? 0;
            }

            // Fetch total haltes
            $haltesData = ApiClient::get('/haltes', ['per_page' => 1])->json();
            \Log::info('Haltes response:', $haltesData ?? []);
            if ($haltesData && isset($haltesData['pagination'])) {
                $stats['total_haltes'] = $haltesData['pagination']['total'] ?? 0;
            }

            // Fetch total admins
            $adminsData = ApiClient::get('/admins', ['per_page' => 1])->json();
            \Log::info('Admins response:', $adminsData ?? []);
            if ($adminsData && isset($adminsData['pagination'])) {
                $stats['total_admins'] = $adminsData['pagination']['total'] ?? 0;
            }

            // Fetch pending students count
            $pendingData = ApiClient::get('/students/pending', ['per_page' => 1])->json();
            \Log::info('Pending response:', $pendingData ?? []);
            if ($pendingData && isset($pendingData['pagination'])) {
                $stats['pending_count'] = $pendingData['pagination']['total'] ?? 0;
            }

            \Log::info('Dashboard stats:', $stats);
        } catch (\Exception $e) {
            // Log error but don't break dashboard display
            \Log::warning('Dashboard stats fetch failed: ' . $e->getMessage());
            \Log::warning('Exception: ' . $e);
        }

        return view('admin.dashboard', ['stats' => $stats]);
    }
}
