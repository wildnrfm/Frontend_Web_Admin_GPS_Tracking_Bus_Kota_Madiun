<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show live tracking map
     */
    public function index()
    {
        return view('admin.tracking');
    }
}
