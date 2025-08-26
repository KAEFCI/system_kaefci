<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MngaccountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = User::query()->orderBy('id', 'asc');

        if ($search) {
            $like = "%{$search}%";
            $query->where(function($q) use ($like) {
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('role', 'like', $like)
                  ->orWhere('account_id', 'like', $like);
            });
        }

        // Pagination agar tetap ringan saat data besar & append parameter search
        $users = $query->paginate(10)->appends(['search' => $search]);

        return view('manageaccount', compact('users', 'search'));
    }
}