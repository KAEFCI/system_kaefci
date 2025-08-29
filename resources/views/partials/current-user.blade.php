@php
    // Mendeteksi user aktif dari daftar guard (prioritas pertama yang ter-auth)
    $__guards = ['superadmin','hrd','supervisor','karyawan','web'];
    if(!isset($__activeGuard)) { $__activeGuard = null; }
    if(!isset($__user)) { $__user = null; }
    // Jika ada expectedGuard (dipass dari include) dan guard itu aktif, pakai itu
    if(isset($expectedGuard) && in_array($expectedGuard, $__guards, true) && auth()->guard($expectedGuard)->check()) {
        $__activeGuard = $expectedGuard;
        $__user = auth()->guard($expectedGuard)->user();
    }
    if(!$__user) {
        foreach ($__guards as $__g) {
            if(auth()->guard($__g)->check()) { $__activeGuard=$__g; $__user=auth()->guard($__g)->user(); break; }
        }
    }
    if(!$__user && auth()->check()) { $__user = auth()->user(); $__activeGuard = 'web'; }
@endphp
<span class="role">{{ $__user ? ($__user->role) : '-' }}</span><br>
<span class="email">{{ $__user->email ?? '-' }}</span>