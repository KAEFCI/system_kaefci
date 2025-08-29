<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('img/logo.png') }}" alt="KFC Logo" class="logo">
    </div>
    <ul class="nav">
        <li class="{{ request()->routeIs('dashboard.hrd') ? 'menu-active' : '' }}">
            <a href="{{ route('dashboard.hrd') }}">
                <span class="indicator"></span>
                <span class="icon"><img src="{{ asset('img/Dashboard_icon.png') }}" alt="Dashboard Icon"></span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('managedata.hrd') ? 'menu-active' : '' }}">
            <a href="{{ route('managedata.hrd') }}">
                <span class="indicator"></span>
                <span class="icon"><img src="{{ asset('img/Document_icon.png') }}" alt="Manage Icon"></span>
                <span class="menu-text">Manage Data</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('absensi.hrd') ? 'menu-active' : '' }}">
            <a href="{{ route('absensi.hrd') }}">
                <span class="indicator"></span>
                <span class="icon"><img src="{{ asset('img/User_icon.png') }}" alt="Document Icon"></span>
                <span class="menu-text">Absensi</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('penggajian.hrd') ? 'menu-active' : '' }}">
            <a href="{{ route('penggajian.hrd') }}">
                <span class="indicator"></span>
                <span class="icon"><img src="{{ asset('img/Penggajian_icon.png') }}" alt="Settings Icon"></span>
                <span class="menu-text">Penggajian</span>
            </a>
        </li>
    </ul>
    <div class="user-info" id="userInfo">
        <div class="user-circle"></div>
        <div class="user-details">
            @include('partials.current-user', ['expectedGuard' => 'hrd'])
        </div>
        <span class="arrow" id="userArrow">&#8250;</span>

        <!-- Dropdown kanan -->
        <div class="user-dd" id="userDropdown">
            <a href="{{ route('settings') }}" class="ud-item">Settings</a>
            <form action="{{ route('logout.role','hrd') }}" method="POST" class="ud-form">
                @csrf
                <button type="submit" class="ud-item logout">Logout (hrd)</button>
            </form>
        </div>
    </div>
</div>


<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Header bar di atas main-content */
    .header-bar {
        position: fixed;
        left: 250px;
        top: 0;
        width: 1190px;
        height: 90px;
        background: #fff;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        z-index: 900;
        padding-left: 48px;
        border-bottom: 1px solid #f2f2f2;
    }
    .header-title {
        color: #E3002C;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.5px;
        font-family: 'Poppins', sans-serif;
        line-height: 1.1;
    }

    /* Sidebar utama */

    .sidebar {
        background: #fff;
        width: 250px;
        height: 100vh;
        overflow: hidden;
        padding: 32px 0 0 0;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.10);
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1000;
    }

    /* Logo di sidebar */

    .logo {
        width: 170px;
        height: auto;
        margin-bottom: 38px;
        margin-top: 0;
        display: block;
    }


    .sidebar .logo img {
        width: 100%;
        margin-bottom: 0;
    }

    /* Navigasi sidebar */

    .nav {
        list-style: none;
        padding-left: 0;
        margin-bottom: 40px;
        width: 85%;
    }


    .nav li {
        position: relative;
        margin: 12px 0;
        font-size: 15px;
    }

    .nav li::before {
        content: "";
        position: absolute;
        left: -30px;
        top: 0;
        height: 100%;
        width: 17px;
        background: transparent;
        border-radius: 10px;
    }

    .nav li.menu-active::before {
        background: #e4002b;
    }

    .nav li.menu-active a {
        background-color: #e4002b;
        color: white;
        border-radius: 6px;
        width: 300px;
        transition: background 0.3s, color 0.3s, width 0.3s;
    }

    .nav li.menu-active a .icon {
        color: white;
    }

    .nav li:hover::before {
        background: #e4002b;
        width: 17px;
    }

    .nav li:hover a {
        background-color: #e4002b;
        color: white;
        border-radius: 6px;
        width: 300px;
        transition: background 0.3s, color 0.3s, width 0.3s;
    }

    .nav li:hover a .icon {
        color: white;
    }

    .nav a {
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px 8px 0;
        border-radius: 5px;
        position: relative;
        z-index: 0;
        background: transparent;
    }

    .menu-text {
        color: #252525;
        font-weight: 500;
        font-size: 13px;
        display: inline-block;
        vertical-align: middle;
        transition: color 0.3s;
    }
    .nav li.menu-active a .menu-text,
    .nav li:hover a .menu-text {
        color: white;
    }


    /* Info user di bawah sidebar */

    .user-info {
        position: absolute;
        bottom: 32px;
        left: 0;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 0 28px;
        cursor: pointer;
    }

    .user-circle {
        width: 28px;
        height: 28px;
        border: 2px solid #e4002b;
        border-radius: 50%;
        margin-right: 10px;
        display: inline-block;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        margin-right: auto;
    }

    .user-details .role {
        font-weight: bold;
        color: #252525;
        font-size: 13px;
    }

    .user-details .email {
        color: #252525;
        font-size: 10px;
        font-weight: regular;
        margin-top: -25px;
    }

    .arrow {
        color: #252525;
        font-size: 18px;
        margin-left: 8px;
        transition: transform .25s ease;
    }

    .arrow.rotated {
        transform: rotate(90deg);
    }

    .icon {
        display: flex;
        align-items: center;
        margin-right: 10px;
        height: 20px;
        width: 20px;
    }

        /* Icon sidebar warna default abu dan berubah jadi putih saat aktif/hover */
        .icon img {
            display: block;
            height: 20px;
            width: 20px;
            object-fit: contain;
            filter: brightness(0) saturate(100%) invert(14%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(90%) contrast(90%);
            /* abu #252525 */
            transition: filter 0.3s;
        }
        .nav li.menu-active a .icon img,
        .nav li:hover a .icon img {
            filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(100%) contrast(100%);
            /* putih */
        }

    /* Override style dropdown user ke kanan */
    .user-dd {
        position: absolute;
        left: 100%;          /* keluar ke kanan sidebar */
        bottom: 0;
        margin-left: 10px;
        background:#fff;
        border:1px solid #e3e3e3;
        border-radius:8px;
        box-shadow:0 6px 18px rgba(0,0,0,.12);
        padding:6px 0;
        min-width:180px;
        opacity:0;
        visibility:hidden;
        transform:translateY(6px);
        transition:all .22s ease;
        z-index:1600;
    }
    .user-dd.show { opacity:1; visibility:visible; transform:translateY(0); }

    .ud-item,
    .ud-form button {
        display:block;
        width:100%;
        text-align:left;
        background:transparent;
        border:0;
        font:500 13px 'Poppins', sans-serif;
        padding:10px 14px;
        color:#252525;
        text-decoration:none;
        cursor:pointer;
        transition:background .15s;
    }
    .ud-item:hover,
    .ud-form button:hover { background:#f5f5f5; }

    .ud-item.logout,
    .ud-form button.logout { color:#c00024; }
    .ud-item.logout:hover,
    .ud-form button.logout:hover { background:#fff1f3; }
</style>

<script>
// Dropdown user (settings + logout) – revisi posisi kanan tanpa ubah CSS sidebar
(function(){
    const info  = document.getElementById('userInfo');
    const menu  = document.getElementById('userDropdown');
    const arrow = document.getElementById('userArrow');

    if(!info || !menu || !arrow) return;

    function openMenu(){
        menu.classList.add('show');
        arrow.classList.add('rotated');

        // Posisi ke kanan (gunakan position:fixed supaya tidak ter-clipping overflow)
        const rect = info.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.left = (rect.right + 8) + 'px';
        // Tampilkan dulu untuk hitung tinggi
        requestAnimationFrame(() => {
            const h = menu.offsetHeight;
            menu.style.top = (rect.bottom - h) + 'px'; // sejajarkan bawah
            menu.style.zIndex = 3000;
        });
    }

    function closeMenu(){
        menu.classList.remove('show');
        arrow.classList.remove('rotated');
        // Jangan hapus seluruh style (biarkan transition); cukup bersihkan koordinat
        menu.style.left = '';
        menu.style.top = '';
        menu.style.position = '';
        menu.style.zIndex = '';
    }

    info.addEventListener('click', function(e){
        if (e.target.closest('.ud-form')) return; // biarkan klik Logout langsung submit
        if (menu.classList.contains('show')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    document.addEventListener('click', function(e){
        if(!info.contains(e.target) && !menu.contains(e.target)){
            closeMenu();
        }
    });

    // Tutup saat resize agar posisi tidak salah
    window.addEventListener('resize', () => {
        if(menu.classList.contains('show')) closeMenu();
    });
})();
</script>