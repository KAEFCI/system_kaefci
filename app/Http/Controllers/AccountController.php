<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        // Cek jika role superadmin dan sudah ada di database
        if ($request->role == 'superadmin' && User::where('role', 'superadmin')->exists()) {
            return redirect()->back()->with('error', 'Super Admin sudah ada, tidak bisa ditambah lagi.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'role' => 'required|string',
            'status' => 'required|string',
            'password' => 'required|min:6'
        ]);

        User::create([
            'account_id' => 'acc' . str_pad(User::count() + 1, 2, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            'login_status' => 'offline'
        ]);

        return redirect()->back()->with('success', 'Account created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        // Jika password diisi, update dan hash
        if ($request->filled('password')) {
            $user->password = \Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
        }

        if ($user->status === 'disable') {
            return back()->withErrors(['email' => 'Akun dinonaktifkan. Hubungi administrator.'])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update([
                'login_status'  => 'online',
                'last_login_at' => now(), // selalu update saat sukses login
            ]);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

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
