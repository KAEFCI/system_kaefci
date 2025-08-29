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

        // Generate account_id yang lebih aman (ambil max nomor existing lalu +1)
        $maxNum = User::whereNotNull('account_id')
            ->where('account_id', 'like', 'acc%')
            ->selectRaw("MAX(CAST(SUBSTRING(account_id, 4) AS UNSIGNED)) as max_num")
            ->value('max_num');
        $nextNum = ($maxNum ?? 0) + 1;
        $accountId = 'acc' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);

        User::create([
            'account_id' => $accountId,
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

        $credentials = $request->only('email','password');
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return back()->withErrors(['email'=>'Email tidak terdaftar.'])->withInput();
        }
        if($user->status === 'disable'){
            return back()->withErrors(['email'=>'Akun dinonaktifkan. Hubungi administrator.'])->withInput();
        }
        $roleGuard = $user->role; // guard name = role
        $allowedGuards = ['superadmin','hrd','supervisor','karyawan'];
        if(!in_array($roleGuard,$allowedGuards,true)){
            return back()->withErrors(['email'=>'Role tidak dikenali.'])->withInput();
        }
        // Attempt hanya pada guard role agar sesi lain (guard berbeda) tidak tersentuh
        if(!Auth::guard($roleGuard)->attempt($credentials)){
            return back()->withErrors(['email'=>'Email atau password salah.'])->withInput();
        }
        $authUser = Auth::guard($roleGuard)->user();
        $authUser->forceFill([
            'login_status'  => 'online',
            'last_login_at' => now(),
        ])->save();
        return match($roleGuard){
            'superadmin' => redirect()->route('dashboard'),
            'hrd' => redirect()->route('dashboard.hrd'),
            'supervisor' => redirect()->route('dashboard.supervisor'),
            'karyawan' => redirect()->route('dashboard.karyawan'),
            default => redirect()->route('login')
        };
    }

    public function logout(Request $request)
    {
        $guards = ['superadmin','hrd','supervisor','karyawan'];
        $target = $request->input('guard');
        if(!$target || !in_array($target,$guards,true)){
            foreach($guards as $g){ if(Auth::guard($g)->check()){ $target=$g; break; } }
        }
        if($target && Auth::guard($target)->check()){
            $u = Auth::guard($target)->user();
            if($u){ $u->forceFill(['login_status'=>'offline'])->save(); }
            Auth::guard($target)->logout();
        }
        // Jika masih ada guard lain aktif JANGAN invalidate session (biar tidak logout massal / tidak 419)
        foreach($guards as $g){
            if(Auth::guard($g)->check()){
                return match($g){
                    'superadmin' => redirect()->route('dashboard'),
                    'hrd' => redirect()->route('dashboard.hrd'),
                    'supervisor' => redirect()->route('dashboard.supervisor'),
                    'karyawan' => redirect()->route('dashboard.karyawan'),
                    default => redirect()->route('login')
                };
            }
        }
        // Semua sudah keluar -> reset session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Logout spesifik berdasarkan role (guard) tanpa memengaruhi guard lain
    public function logoutRole(Request $request, string $role)
    {
        $guards = ['superadmin','hrd','supervisor','karyawan'];
        if(!in_array($role,$guards,true)){
            return back()->with('error','Role tidak valid');
        }
        if(Auth::guard($role)->check()){
            $u = Auth::guard($role)->user();
            if($u){ $u->forceFill(['login_status'=>'offline'])->save(); }
            Auth::guard($role)->logout();
        }
        // Jika masih ada guard lain aktif, kembali ke halaman sebelumnya / root (root akan redirect ke guard aktif lain)
        foreach($guards as $g){ if(Auth::guard($g)->check()){ return redirect()->back(); } }
        // Kalau tidak ada guard lain -> ke login tanpa invalidate global (biarkan sesi agar tidak ganggu token tab lain)
        return redirect()->route('login');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::query();

        if ($search) {
            $users->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $users->paginate(10);
        return view('manageaccount', compact('users', 'search'));
    }
}
