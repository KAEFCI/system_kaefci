<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/settings.css') }}" />
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar-holder">
        @include('sidebar')
    </aside>

    <!-- MAIN WRAPPER (header + content + footer) -->
    <div class="main-wrapper">
        <header class="header-bar">
            <h1>{{ isset($title) ? $title : 'Settings' }}</h1>
        </header>
        <div class="page-wrapper">
                <div class="container">

            <div class="card">
                <h2><i class="fa-solid fa-fingerprint"></i> Metode Absensi</h2>
                <div class="method-list">
                    <label class="method-item"><input type="checkbox" checked /> <i class="fa-solid fa-qrcode"></i> QR
                        Code</label>
                    <label class="method-item"><input type="checkbox" /> <i class="fa-solid fa-hand"></i>
                        Fingerprint</label>
                    <label class="method-item"><input type="checkbox" checked /> <i
                            class="fa-regular fa-face-smile"></i> Face Recognition</label>
                </div>
                <hr class="divider" />
                <div class="compact-triplet" aria-label="Pengaturan grace dan batas telat">
                    <div class="compact-field">
                        <label for="grace-in">Grace Masuk</label>
                        <input id="grace-in" type="number" value="10" min="0" />
                    </div>
                    <div class="compact-field">
                        <label for="grace-out">Grace Pulang</label>
                        <input id="grace-out" type="number" value="5" min="0" />
                    </div>
                    <div class="compact-field">
                        <label for="max-late">Max Telat</label>
                        <input id="max-late" type="number" value="3" min="0" />
                    </div>
                </div>
            </div>

            <!-- Dipindahkan ke sini agar sejajar dengan Metode Absensi -->
            <div class="card">
                <h2><i class="fa-solid fa-location-dot"></i> Validasi Lokasi</h2>
                <div class="form-row lokasi-row">
                    <div class="form-group radius-group">
                        <label>Radius Validasi (m)</label>
                        <input type="number" value="200" />
                        <div class="hint">Jarak maksimum dari titik kantor.</div>
                    </div>
                    <div class="form-group ip-whitelist full-width">
                        <label>IP Whitelist</label>
                        <input type="text" value="192.168.1.1" />
                        <div class="hint">Pisahkan dengan koma untuk banyak IP.</div>
                    </div>
                </div>
            </div>

            <!-- Manajemen Shift (Aturan Shift Global Dummy) -->
            <div class="card shift-card full-span">
                <h2><i class="fa-solid fa-calendar-days"></i> Manajemen Shift</h2>
                <div class="table-wrapper">
                    <table id="shift-table">
                        <colgroup>
                            <col style="width:32%" />
                            <col style="width:26%" />
                            <col style="width:21%" />
                            <col style="width:21%" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Nama Shift</th>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge"><i class="fa-solid fa-clock"></i> Shift Pagi</span></td>
                                <td>-</td>
                                <td>08:00</td>
                                <td>16:00</td>
                            </tr>
                            <tr>
                                <td><span class="badge"><i class="fa-solid fa-clock"></i> Shift Siang</span></td>
                                <td>-</td>
                                <td>12:00</td>
                                <td>20:00</td>
                            </tr>
                            <tr>
                                <td><span class="badge"><i class="fa-solid fa-clock"></i> Shift Malam</span></td>
                                <td>-</td>
                                <td>20:00</td>
                                <td>04:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="card superadmin-only" id="superadmin-section">
                <h2><i class="fa-solid fa-users-gear"></i> Manajemen Role & Log</h2>
                <div class="form-row" style="align-items:flex-start;">
                    <div class="form-group" style="max-width:220px;">
                        <label>Ubah Role Karyawan</label>
                        <select>
                            <option>HRD</option>
                            <option>Supervisor</option>
                            <option>Karyawan</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label>Log Aktivitas</label>
                        <ul class="log-list">
                            <li><i class="fa-solid fa-circle"></i> Superadmin menambah shift malam - 30/08/2025</li>
                            <li><i class="fa-solid fa-circle"></i> HRD update metode absensi - 28/08/2025</li>
                        </ul>
                    </div>
                </div>
            </div>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    @include('partials.footer')
</body>
</html>
