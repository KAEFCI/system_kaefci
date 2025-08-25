<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Fungsi login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Jika berhasil login, update status dan waktu login
            $user = Auth::user();
            $user->login_status = 'online';
            $user->last_login_at = now();
            $user->save();

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        // Jika gagal login, kembali ke halaman login dengan pesan error dan input tetap ada
        return redirect()->back()->withInput()->with('error', 'Email or password is incorrect.');
    }

    // Fungsi logout
    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->update([
                'login_status' => 'offline',
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
