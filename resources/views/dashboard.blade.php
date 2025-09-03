<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
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
      <h1>{{ isset($title) ? $title : 'Dashboard' }}</h1>
    </header>

    <!-- CONTENT -->
    <main class="main-content">
      <p class="welcome-text">Halo, Super Admin 👋</p>

      <div class="metrics">
  <div class="metric"><h3>Total Account</h3><p>{{ $totalAccount }}</p></div>
        <div class="metric"><h3>Total karyawan</h3><p>500</p></div>
        <div class="metric"><h3>Pendapatan hari ini</h3><p>Rp100.500.999</p></div>
        <div class="metric"><h3>Absensi hari ini</h3><p>200/<span class="muted">500</span></p></div>
      </div>

      <div class="charts">
        <div class="chart"><h3>Pendapatan</h3><canvas id="income-chart"></canvas></div>
        <div class="chart"><h3>Absensi</h3><canvas id="absence-chart"></canvas></div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="{{ asset('js/dashboard.js') }}"></script>
  @include('partials.footer')
</body>
</html>
