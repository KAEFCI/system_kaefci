<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/hrd/absensi.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    @include('hrd.sidebar')
  </aside>

    <!-- MAIN WRAPPER (header + content scrollable) -->
    <div class="main-wrapper">
        <!-- HEADER -->
        <header class="header-bar">
            <h1>{{ isset($title) ? $title : 'Absensi' }}</h1>
        </header>
        <div class="container">
        <div class="filter-row">
          <div class="filter-wrapper">
            <select id="filterRange" class="filter-with-icon" aria-label="Filter rentang waktu">
              <option value="day">Filters</option>
              <option value="week">Minggu</option>
              <option value="month">Bulan</option>
              <option value="year">Tahun</option>
            </select>
          </div>
        </div>

        <!-- Statistik Cards -->
        <div class="cards">
            <div class="card"><strong>Total Karyawan</strong><span id="totalKaryawan">0</span></div>
            <div class="card"><strong>Hadir</strong><span id="hadir">0</span></div>
            <div class="card"><strong>Izin</strong><span id="izin">0</span></div>
            <div class="card"><strong>Tidak Hadir</strong><span id="tidakHadir">0</span></div>
        </div>

        <!-- Statistik Charts -->
        <div class="chart-grid">
            <div class="chart-container">
                <h4>Grafik Kehadiran</h4>
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-container">
                <h4>Alasan Ketidakhadiran</h4>
                <canvas id="pieChart"></canvas>
            </div>
        </div>

    <!-- Tabel Validasi -->
       <div class="section-title"><h3>Validasi Pengajuan</h3></div>
    <div class="card-table">
    <table id="validasiTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Jam</th>
                    <th>Bukti</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

        <!-- Tabel Laporan -->
       <div class="section-title"><h3>Laporan Absensi</h3></div>
    <div class="card-table">
        <table id="laporanTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    </div>

    <!-- Modal removed for Absensi (no detail modal on this page) -->

    </div>
    </div>

    <script>
        const datasets = {
            hari: {
                stats: { total: 25, hadir: 20, izin: 3, tidak: 2 },
                bar: { labels: ["Budi", "Siti", "Andi", "Dina", "Rina"], data: [1, 1, 1, 1, 0] },
                pie: { labels: ["Sakit", "Izin", "Alpha"], data: [2, 3, 2] },
                validasi: [
                    { nama: "Rina", tanggal: "2025-09-02", status: "Menunggu", jam: "08:30", bukti: "https://via.placeholder.com/100", alasan: "Izin keluarga" },
                    { nama: "Andi", tanggal: "2025-09-02", status: "Menunggu", jam: "09:00", bukti: "https://via.placeholder.com/100", alasan: "Sakit" }
                ],
                laporan: [
                    { nama: "Budi", tanggal: "2025-09-02", status: "Hadir", jamMasuk: "08:00", jamKeluar: "17:00" },
                    { nama: "Siti", tanggal: "2025-09-02", status: "Hadir", jamMasuk: "08:10", jamKeluar: "17:00" },
                    { nama: "Rina", tanggal: "2025-09-02", status: "Izin", jamMasuk: "-", jamKeluar: "-" }
                ]
            },
            minggu: {
                stats: { total: 25, hadir: 120, izin: 10, tidak: 5 },
                bar: { labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"], data: [24, 23, 25, 22, 26] },
                pie: { labels: ["Sakit", "Izin", "Alpha"], data: [5, 3, 2] },
                validasi: [
                    { nama: "Siti", tanggal: "2025-08-30", status: "Menunggu", jam: "-", bukti: "https://via.placeholder.com/100", alasan: "Sakit" },
                ],
                laporan: [
                    { nama: "Andi", tanggal: "2025-08-30", status: "Hadir", jamMasuk: "08:05", jamKeluar: "17:00" },
                    { nama: "Dina", tanggal: "2025-08-31", status: "Alpha", jamMasuk: "-", jamKeluar: "-" }
                ]
            },
            bulan: {
                stats: { total: 25, hadir: 480, izin: 20, tidak: 15 },
                bar: { labels: ["Minggu 1", "Minggu 2", "Minggu 3", "Minggu 4"], data: [120, 118, 125, 117] },
                pie: { labels: ["Sakit", "Izin", "Alpha"], data: [8, 6, 6] },
                validasi: [
                    { nama: "Budi", tanggal: "2025-08-15", status: "Menunggu", jam: "-", bukti: "https://via.placeholder.com/100", alasan: "Perjalanan" },
                ],
                laporan: [
                    { nama: "Siti", tanggal: "2025-08-15", status: "Hadir", jamMasuk: "08:00", jamKeluar: "17:00" }
                ]
            },
            tahun: {
                stats: { total: 25, hadir: 5600, izin: 90, tidak: 70 },
                bar: { labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep"], data: [480, 460, 470, 465, 475, 480, 490, 485, 488] },
                pie: { labels: ["Sakit", "Izin", "Alpha"], data: [30, 20, 20] },
                validasi: [
                    { nama: "Dina", tanggal: "2025-07-01", status: "Menunggu", jam: "-", bukti: "https://via.placeholder.com/100", alasan: "Izin Kuliah" },
                ],
                laporan: [
                    { nama: "Rina", tanggal: "2025-07-01", status: "Izin", jamMasuk: "-", jamKeluar: "-" }
                ]
            }
        };

        let barChart, pieChart;

        function updateDashboard(filter) {
            const data = datasets[filter];
            // Update cards
            document.getElementById("totalKaryawan").textContent = data.stats.total;
            document.getElementById("hadir").textContent = data.stats.hadir;
            document.getElementById("izin").textContent = data.stats.izin;
            document.getElementById("tidakHadir").textContent = data.stats.tidak;
            // Update charts
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            barChart = new Chart(document.getElementById("barChart"), {
                type: "bar",
                data: { labels: data.bar.labels, datasets: [{ label: "Hadir", data: data.bar.data, backgroundColor: "#c8102e" }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
            pieChart = new Chart(document.getElementById("pieChart"), {
                type: "pie",
                data: { labels: data.pie.labels, datasets: [{ data: data.pie.data, backgroundColor: ["#e74c3c", "#f39c12", "#95a5a6"] }] },
                options: { responsive: true }
            });
            // Update tables
            const vBody = document.querySelector("#validasiTable tbody"); vBody.innerHTML = "";
                        data.validasi.forEach((v, i) => {
                                const tr = document.createElement("tr");
                                const badgeClass = v.status === 'Diizinkan' ? 'badge-status badge-diizinkan' : v.status === 'Ditolak' ? 'badge-status badge-ditolak' : 'badge-status';
                                tr.innerHTML = `
                    <td class="td-name">${v.nama}</td>
                    <td class="td-small">${v.tanggal}</td>
                    <td><span class="${badgeClass}">${v.status}</span></td>
                    <td class="td-small">${v.jam}</td>
                    <td><img src="${v.bukti}" width="50"></td>
                    <td class="td-small">${v.alasan}</td>
                    <td>
                        <div class="action-menu small" data-index="${i}" data-filter="${filter || 'hari'}">
                            <button class="action-trigger" aria-expanded="false" title="Aksi">⋮</button>
                            <div class="action-dropdown">
                                <button class="menu-item detail">Detail</button>
                                <button class="menu-item allow">Izinkan</button>
                                <button class="menu-item delete">Tolak</button>
                            </div>
                        </div>
                    </td>`;
                                vBody.appendChild(tr);
                        });
            const lBody = document.querySelector("#laporanTable tbody"); lBody.innerHTML = "";
            data.laporan.forEach(l => {
                const tr = document.createElement("tr");
                tr.innerHTML = `<td>${l.nama}</td><td>${l.tanggal}</td><td>${l.status}</td><td>${l.jamMasuk}</td><td>${l.jamKeluar}</td>`;
                lBody.appendChild(tr);
            });
        }

    // detail modal removed for Absensi; actions are inline (setStatus)

        function setStatus(index, filter, status) {
            datasets[filter].validasi[index].status = status;
            updateDashboard(filter);
        }

        // map select values to dataset keys
        const mapRange = { day: 'hari', week: 'minggu', month: 'bulan', year: 'tahun' };
        document.getElementById("filterRange").addEventListener("change", e => {
            const key = mapRange[e.target.value] || 'hari';
            updateDashboard(key);
        });

        // delegated action menu handlers
        document.addEventListener('click', function (e) {
            // toggle menu when clicking trigger
            const trigger = e.target.closest('.action-trigger');
            if (trigger) {
                const menu = trigger.parentElement;
                const open = menu.classList.contains('open');
                // close any other open menus
                document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
                if (!open) menu.classList.add('open');
                trigger.setAttribute('aria-expanded', !open);
                return;
            }

            // click on menu items
            const item = e.target.closest('.menu-item');
            if (item) {
                const menu = item.closest('.action-menu');
                const idx = parseInt(menu.getAttribute('data-index'));
                const fkey = menu.getAttribute('data-filter') || 'hari';
                if (item.classList.contains('detail')) {
                    showDetail(idx, fkey);
                } else if (item.classList.contains('allow')) {
                    setStatus(idx, fkey, 'Diizinkan');
                } else if (item.classList.contains('delete')) {
                    setStatus(idx, fkey, 'Ditolak');
                }
                menu.classList.remove('open');
                return;
            }

            // click outside closes any open menu
            if (!e.target.closest('.action-menu')) {
                document.querySelectorAll('.action-menu.open').forEach(m => m.classList.remove('open'));
            }
        });

        function showDetail(index, filterKey) {
            const row = datasets[filterKey].validasi[index];
            // lightweight detail overlay
            let overlay = document.getElementById('absensiDetailOverlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'absensiDetailOverlay';
                overlay.className = 'absensi-overlay';
                document.body.appendChild(overlay);
            }
            overlay.innerHTML = `
              <div class="absensi-modal">
                <header><strong>Detail Pengajuan</strong><button class="close-modal" aria-label="Tutup">✕</button></header>
                <div class="absensi-body">
                  <p><strong>Nama:</strong> ${row.nama}</p>
                  <p><strong>Tanggal:</strong> ${row.tanggal}</p>
                  <p><strong>Jam:</strong> ${row.jam}</p>
                  <p><strong>Alasan:</strong> ${row.alasan}</p>
                  <p><strong>Status:</strong> ${row.status}</p>
                  <p><img src="${row.bukti}" alt="Bukti" style="max-width:200px;border-radius:8px;border:1px solid #eee"></p>
                </div>
                <footer>
                  <button class="btn-secondary close-modal">Tutup</button>
                </footer>
              </div>`;
            overlay.classList.add('show');
            // close handlers
            overlay.querySelectorAll('.close-modal').forEach(btn => btn.addEventListener('click', () => overlay.classList.remove('show')));
            overlay.addEventListener('click', function (ev) { if (ev.target === overlay) overlay.classList.remove('show'); });
        }

        // default
        updateDashboard('hari');
    </script>
</body>

</html>