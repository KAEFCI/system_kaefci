<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MngaccountController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::orderBy('id', 'asc')->get();
        return view('manageaccount', compact('users'));
    }
}