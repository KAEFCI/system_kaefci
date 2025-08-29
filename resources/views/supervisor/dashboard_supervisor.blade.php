<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supervisor Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
</head>
<body>
  <aside class="sidebar">@include('sidebar')</aside>
  <div class="main-wrapper">
    <header class="header-bar"><h1>Dashboard Supervisor</h1></header>
    <main class="main-content">
      <p class="welcome-text">Halo, Supervisor 👋</p>
      <div class="metrics">
        <div class="metric"><h3>Info</h3><p>-</p></div>
      </div>
    </main>
  </div>
</body>
</html>
