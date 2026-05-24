<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display list of admins
     */
    public function index()
    {
        return view('admin.admin');
    }
}
