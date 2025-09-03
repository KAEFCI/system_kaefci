<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Analysis Data</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/analysis.css') }}" />
</head>

<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    @include('sidebar')
  </aside>

  <!-- MAIN WRAPPER (header + content scrollable) -->
  <div class="main-wrapper">
    <!-- HEADER -->
    <header class="header-bar">
      <h1>{{ isset($title) ? $title : 'Analysis Data' }}</h1>
    </header>

    <!-- CONTENT -->
    <main class="main-content">
      <div class="container">
        <!-- PRINT TITLE (only visible when printing) -->
        <div id="printTitle" class="print-only">Laporan Analysis Data Absensi KFC</div>
        <div class="cards">
          <div class="card"><strong>Total Hari Kerja</strong><span id="statTotalHari">0</span></div>
          <div class="card"><strong>Total Kehadiran</strong><span id="statHadir">0</span></div>
          <div class="card"><strong>Total Ketidakhadiran</strong><span id="statTidakHadir">0</span></div>
          <div class="card"><strong>Rata-rata Kehadiran</strong><span id="statRata">0%</span></div>
          <div class="card"><strong>Persentase Kehadiran</strong><span id="statPersen">0%</span></div>
          <div class="card"><strong>Jumlah Keterlambatan</strong><span id="statTelat">0</span></div>
        </div>

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

        <div class="chart-grid">
          <div class="chart-container">
            <h4 id="barTitle">Grafik Batang: Kehadiran (Hari Ini)</h4>
            <canvas id="barChart"></canvas>
          </div>
          <div class="chart-container">
            <h4>Pie Chart: Alasan Ketidakhadiran</h4>
            <canvas id="pieChart"></canvas>
          </div>
          <div class="chart-container">
            <h4 id="lineTitle">Line Chart: Tren (Hari Ini)</h4>
            <canvas id="lineChart"></canvas>
          </div>
          <div class="chart-container">
            <h4>Heatmap (Simulasi): Hari dengan Absensi Terbanyak</h4>
            <canvas id="heatmapChart"></canvas>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Hadir</th>
              <th>Izin</th>
              <th>Sakit</th>
              <th>Alpa</th>
              <th>Persentase</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody id="tbodyData">
            <tr>
              <td>Budi</td>
              <td>145</td>
              <td>5</td>
              <td>3</td>
              <td>2</td>
              <td>91%</td>
              <td>Sering telat</td>
            </tr>
            <tr>
              <td>Siti</td>
              <td>158</td>
              <td>1</td>
              <td>1</td>
              <td>0</td>
              <td>99%</td>
              <td>Aktif</td>
            </tr>
          </tbody>
        </table>

        <div class="laporan">
          <ul>
            <li>Budi memiliki kehadiran paling rendah dan keterlambatan tinggi.</li>
            <li>Bulan Februari tercatat dengan absensi terendah karena libur panjang.</li>
            <li>Ketidakhadiran meningkat di hari Jumat.</li>
            <li>Departemen IT paling disiplin dibanding departemen lainnya.</li>
          </ul>
          <ul>
            <li>Memberikan peringatan kepada Budi.</li>
            <li>Menawarkan pelatihan disiplin.</li>
            <li>Memberi reward kehadiran 100% untuk Siti.</li>
            <li>Merevisi aturan keterlambatan.</li>
          </ul>
        </div>

        <p>Data diambil dari sistem absensi digital FingerPro (Jan – Agu 2025).</p>
        <p>Catatan: Data Februari tidak lengkap karena perpindahan sistem.</p>

        <div class="button-section">
          <button class="btn" onclick="window.print()">Cetak Data</button>
        </div>
    </main>
    <!-- Print timestamp diposisikan di dalam main-wrapper agar flex kolom mendorong ke bawah -->
    <div id="printTimestamp" class="print-only print-timestamp"></div>
  </div>

  <!-- Chart.js library harus dimuat sebelum inisialisasi chart -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const datasetRange = {
      day: {
        bar: { labels: ["Budi", "Siti", "Andi", "Dina", "Rina"], data: [1, 1, 1, 1, 0], label: "Hadir (Hari Ini)", maxY: 5 },
        line: { labels: ["07:00", "08:00", "09:00", "10:00", "11:00", "12:00", "13:00"], data: [5, 15, 28, 42, 55, 70, 80], label: "Progress Jam", maxY: 100 },
        stats: { totalHari: 1, hadir: 4, tidakHadir: 1, rata: "80%", persen: "80%", telat: 1 }
      },
      week: {
        bar: { labels: ["Budi", "Siti", "Andi", "Dina", "Rina"], data: [5, 5, 4, 5, 3], label: "Hadir (Mingguan)", maxY: 7 },
        line: { labels: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab"], data: [20, 22, 21, 23, 19, 18], label: "Total Harian", maxY: 25 },
        stats: { totalHari: 6, hadir: 25, tidakHadir: 5, rata: "83%", persen: "83%", telat: 3 }
      },
      month: {
        bar: { labels: ["Budi", "Siti", "Andi", "Dina", "Rina"], data: [20, 22, 21, 19, 18], label: "Hadir (Bulanan)", maxY: 26 },
        line: { labels: ["M1", "M2", "M3", "M4", "M5"], data: [80, 82, 81, 79, 78], label: "Akumulasi Mingguan", maxY: 90 },
        stats: { totalHari: 24, hadir: 100, tidakHadir: 20, rata: "83%", persen: "83%", telat: 9 }
      },
      year: {
        bar: { labels: ["Budi", "Siti", "Andi", "Dina", "Rina"], data: [200, 210, 198, 205, 190], label: "Hadir (Tahunan)", maxY: 230 },
        line: { labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"], data: [18, 20, 19, 21, 22, 20, 19, 20, 21, 20, 19, 22], label: "Per Bulan", maxY: 25 },
        stats: { totalHari: 220, hadir: 1003, tidakHadir: 37, rata: "91%", persen: "91%", telat: 32 }
      }
    };

    let barChart, lineChart, pieChart, heatmapChart;

    const pieData = {
      labels: ["Izin", "Sakit", "Tanpa Keterangan"],
      datasets: [{ data: [5, 6, 4], backgroundColor: ["#f39c12", "#e74c3c", "#95a5a6"] }]
    };

    const heatmapData = {
      labels: ["Sen", "Sel", "Rab", "Kam", "Jum"],
      datasets: [
        { label: "Hadir", data: [30, 28, 29, 25, 20], backgroundColor: "#e74c3c" },
        { label: "Tidak Hadir", data: [2, 4, 3, 5, 10], backgroundColor: "#e74d3c4f" }
      ]
    };

    function getWorkdaysSoFarThisWeek() {
      const today = new Date();
      const day = today.getDay();
      let count = 0;
      for (let d = 1; d <= 5; d++) if (day >= d) count++;
      return count === 0 ? 1 : count;
    }

    function initCharts() {
      const ctxBar = document.getElementById("barChart").getContext("2d");
      const ctxLine = document.getElementById("lineChart").getContext("2d");
      const ctxPie = document.getElementById("pieChart").getContext("2d");
      const ctxHeat = document.getElementById("heatmapChart").getContext("2d");

      const init = datasetRange.day;

      barChart = new Chart(ctxBar, {
        type: "bar",
        data: { labels: init.bar.labels, datasets: [{ label: init.bar.label, data: init.bar.data, backgroundColor: "#c8102e" }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: init.bar.maxY } } }
      });

      lineChart = new Chart(ctxLine, {
        type: "line",
        data: { labels: init.line.labels, datasets: [{ label: init.line.label, data: init.line.data, borderColor: "#c8102e", backgroundColor: "#c8102e22", fill: true, tension: .35, pointRadius: 4, pointHoverRadius: 6 }] },
        options: { responsive: true, plugins: { legend: { display: true, position: 'top' } }, scales: { y: { beginAtZero: true, max: init.line.maxY } } }
      });

      pieChart = new Chart(ctxPie, { type: "pie", data: pieData, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });

      heatmapChart = new Chart(ctxHeat, {
        type: "bar",
        data: heatmapData,
        options: {
          responsive: true,
          scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, max: 40 } },
          plugins: { legend: { position: "top" } }
        }
      });

      applyStats('day', init.stats);
    }

    function applyStats(rangeKey, stats) {
      let s = { ...stats };
      if (rangeKey === 'day') s.totalHari = getWorkdaysSoFarThisWeek();
      document.getElementById("statTotalHari").textContent = s.totalHari;
      document.getElementById("statHadir").textContent = s.hadir;
      document.getElementById("statTidakHadir").textContent = s.tidakHadir;
      document.getElementById("statRata").textContent = s.rata;
      document.getElementById("statPersen").textContent = s.persen;
      document.getElementById("statTelat").textContent = s.telat;
    }

    function updateRange(rangeKey) {
      const cfg = datasetRange[rangeKey];
      if (!cfg) return;

      barChart.data.labels = cfg.bar.labels;
      barChart.data.datasets[0].data = cfg.bar.data;
      barChart.data.datasets[0].label = cfg.bar.label;
      barChart.options.scales.y.max = cfg.bar.maxY;
      barChart.update();

      lineChart.data.labels = cfg.line.labels;
      lineChart.data.datasets[0].data = cfg.line.data;
      lineChart.data.datasets[0].label = cfg.line.label;
      lineChart.options.scales.y.max = cfg.line.maxY;
      lineChart.update();

      document.getElementById("barTitle").textContent =
        "Grafik Batang: " + (rangeKey === "day" ? "Kehadiran (Hari Ini)" :
          rangeKey === "week" ? "Kehadiran Mingguan" :
            rangeKey === "month" ? "Kehadiran Bulanan" : "Kehadiran Tahunan");
      document.getElementById("lineTitle").textContent =
        "Line Chart: " + (rangeKey === "day" ? "Tren (Hari Ini)" :
          rangeKey === "week" ? "Tren Mingguan" :
            rangeKey === "month" ? "Tren Bulanan" : "Tren Tahunan");

      applyStats(rangeKey, cfg.stats);
    }

    const filterSelect = document.getElementById("filterRange");
    filterSelect.addEventListener("change", () => updateRange(filterSelect.value));

    window.onload = () => { initCharts(); };

    // Hilangkan resize manual yang menyebabkan layout rusak saat print.
    // Hanya pakai default canvas sizing + CSS.

    // ==== PRINT TITLE & TIMESTAMP HANDLING ====
    function pad(n) { return n.toString().padStart(2, '0'); }
    function buildTimestamp() {
      const d = new Date();
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return `${days[d.getDay()]}, ${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    }
    function updateTimestamp() {
      const el = document.getElementById('printTimestamp');
      if (el) { el.textContent = '' + buildTimestamp(); }
    }
    window.addEventListener('beforeprint', updateTimestamp);
    filterSelect.addEventListener('change', () => { updateRange(filterSelect.value); updateTimestamp(); });
    updateTimestamp();
  </script>
  @include('partials.footer')
</body>

</html>