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
            <label for="staff_id">Masukan ID</label>
            <input id="staff_id" name="staff_id" type="text" placeholder="Contoh: ACC01" />
          </div>
          <div class="col">
            <label for="no_rekening">Nomor Rekening</label>
            <input id="no_rekening" name="no_rekening" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Nomor rekening bank" />
          </div>
        </div>
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
        <div class="row">
          <div class="col">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Email aktif karyawan" required />
          </div>
          <div class="col">
            <label for="gaji_pokok">Gaji Pokok</label>
            <input id="gaji_pokok" name="gaji_pokok" type="number" min="0" step="1000" placeholder="Nominal gaji pokok" required readonly aria-readonly="true" />
          </div>
        </div>
        <!-- Total Gaji dihilangkan sesuai permintaan -->
        <div class="form-actions" style="display:flex;justify-content:flex-end;margin-top:16px;">
          <button type="submit" class="btn"><i class="fa-solid fa-save"></i> Simpan Penggajian</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const salaryMap = {
      'Supervisor': 5000000,
      'Karyawan': 3000000
    };

    function toNum(value) {
      return Number(String(value || 0).replace(/[^0-9.-]/g, '')) || 0;
    }

    function applyBaseSalaryFromRole() {
      const role = document.getElementById('jabatan').value;
      if (salaryMap[role] != null) {
        document.getElementById('gaji_pokok').value = salaryMap[role];
        updateTotalGaji();
      }
    }

    document.getElementById('jabatan').addEventListener('change', applyBaseSalaryFromRole);

    // Enforce readonly on gaji_pokok and prevent manual edits
    const gajiInput = document.getElementById('gaji_pokok');
    if (gajiInput) {
      gajiInput.setAttribute('readonly', 'readonly');
      gajiInput.addEventListener('keydown', (e) => {
        // allow Tab for navigation
        if (e.key !== 'Tab') {
          e.preventDefault();
        }
      });
      gajiInput.addEventListener('wheel', (e) => e.preventDefault(), {
        passive: false
      });
    }

    // Optional: Prevent form submit (remove if backend connected)
    document.querySelector('form').addEventListener('submit', e => {
      e.preventDefault();
      alert('Data penggajian berhasil disimpan!');
    });
  </script>
</body>

</html>