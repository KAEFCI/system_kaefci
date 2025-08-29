<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
    // Hitung semua user
    $totalAccount = \App\Models\User::count();
        return view('dashboard', compact('totalAccount'));
    }
}