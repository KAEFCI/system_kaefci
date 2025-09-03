<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Penggajian</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/4f6f1f38e1.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="/css/hrd/penggajian.css" />
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
      <h1>{{ isset($title) ? $title : 'Penggajian' }}</h1>
    </header>
    <div class="container">
      <form autocomplete="off" novalidate>
        <div class="row">
          <div class="col">
            <label for="nama_karyawan">Nama Karyawan</label>
            <input id="nama_karyawan" name="nama_karyawan" type="text" placeholder="Nama lengkap karyawan" required />
          </div>
          <div class="col">
            <label for="jabatan">Jabatan</label>
            <select id="jabatan" name="jabatan" required>
              <option value="" disabled selected>Pilih Jabatan</option>
              <option value="Supervisor">Supervisor</option>
              <option value="Karyawan">Karyawan</option>
            </select>
          </div>
        </div>
        <div class="form-divider"></div>
        <div class="row">
          <div class="col" style="flex: 1 1 100%;">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Email aktif karyawan" required />
          </div>
        </div>
        <div class="form-divider"></div>
        <div class="row">
          <div class="col">
            <label for="gaji_pokok">Gaji Pokok</label>
            <input id="gaji_pokok" name="gaji_pokok" type="number" min="0" step="1000" placeholder="Nominal gaji pokok" required />
          </div>
          <div class="col">
            <label for="total_gaji">Total Gaji</label>
            <input id="total_gaji" name="total_gaji" type="number" placeholder="Total gaji otomatis" readonly />
          </div>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-save"></i> Simpan Penggajian</button>
      </form>
    </div>
  </div>

  <script>
    function toNum(value) {
      return Number(String(value || 0).replace(/[^0-9.-]/g, '')) || 0;
    }

    function updateTotalGaji() {
      const gajiPokok = toNum(document.getElementById('gaji_pokok').value);
      // Kalau mau tambah logika tunjangan dll, tinggal modifikasi di sini
      document.getElementById('total_gaji').value = gajiPokok;
    }

    document.getElementById('gaji_pokok').addEventListener('input', updateTotalGaji);
    updateTotalGaji();

    // Optional: Prevent form submit (remove if backend connected)
    document.querySelector('form').addEventListener('submit', e => {
      e.preventDefault();
      alert('Data penggajian berhasil disimpan!');
    });
  </script>
</body>

</html>
