<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>History Karyawan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/hrd/hiskaryawan.css') }}" />
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
      <h1>{{ isset($title) ? $title : 'History Karyawan' }}</h1>
    </header>

    <!-- CONTENT -->
    <main class="main-content">
      <div class="history-wrapper">
        <div class="section-header">
            <h2><i class="fa-solid fa-users"></i> Daftar Karyawan</h2>
            <button class="btn btn-primary" id="btnOpenModal">Tambah Data</button>
        </div>

        <div class="toolbar">
            <form method="GET" id="searchForm" class="search-box" style="margin:0;">
                <div class="search-field">
                    <input type="text" id="searchEmployee" name="search" value="{{ request('search', '') }}" placeholder="Cari nama / jabatan / status..." aria-label="Cari">
                </div>
            </form>
            <div class="chips" id="toolbarChips"></div>
        </div>

        @if(session('success'))
        <div class="toast-success" id="toastSuccess">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table-hist">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Status</th>
                        <th>Masalah</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // If controller didn't pass $data, use dummy rows for preview
                        $preview = collect([
                            (object)[ 'id'=>1, 'nama_karyawan'=>'Andi Wijaya', 'jabatan'=>'Staff Admin', 'tanggal_masuk'=>now(), 'tanggal_keluar'=>null, 'status_kepegawaian'=>'aktif', 'is_bermasalah'=>0, 'catatan'=>'Tidak ada' ],
                            (object)[ 'id'=>2, 'nama_karyawan'=>'Siti Rahma', 'jabatan'=>'Accountant', 'tanggal_masuk'=>now()->subYears(2), 'tanggal_keluar'=>null, 'status_kepegawaian'=>'aktif', 'is_bermasalah'=>0, 'catatan'=>'Good performance' ],
                            (object)[ 'id'=>3, 'nama_karyawan'=>'Budi Santoso', 'jabatan'=>'Staff Teknik', 'tanggal_masuk'=>now()->subYears(1), 'tanggal_keluar'=>now()->subMonths(1), 'status_kepegawaian'=>'resign', 'is_bermasalah'=>0, 'catatan'=>'Resigned last month' ],
                        ]);
                        $rows = ($data ?? null) ?: $preview;
                    @endphp

                    @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->nama_karyawan }}</td>
                        <td>{{ $row->jabatan }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td>{{ $row->tanggal_keluar ? \Illuminate\Support\Carbon::parse($row->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge status-{{ $row->status_kepegawaian }}">{{ ucfirst($row->status_kepegawaian) }}</span></td>
                        <td>{!! $row->is_bermasalah ? '<span class="badge badge-red">Ya</span>' : '<span class="badge badge-green">Tidak</span>' !!}</td>
                        <td class="catatan">{{ Str::limit($row->catatan,40) }}</td>
                        <td class="aksi">
                            <div class="dropdown" data-id="{{ $row->id }}">
                                <button class="dot-btn">⋮</button>
                                <div class="dd-menu">
                                    <button class="dd-item" data-edit='@json($row)'>Edit</button>
                                    <form method="POST" action="{{ isset($data) ? route('history.karyawan.destroy',$row) : '#' }}" onsubmit="return confirm('delete karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dd-item delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:25px;">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($data) && method_exists($data, 'links'))
        <div class="pagination-wrap">{{ $data->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal" id="modalForm">
        <div class="modal-content">
            <h3 id="modalTitle">Tambah Karyawan</h3>
            <form id="formHistory" method="POST" action="{{ route('history.karyawan.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <div class="grid">
                    <div class="form-group"><label>Nama Karyawan</label><input name="nama_karyawan" required></div>
                    <div class="form-group"><label>Jabatan</label><input name="jabatan" required></div>
                    <div class="form-group"><label>Tanggal Masuk</label><input type="date" name="tanggal_masuk" required></div>
                    <div class="form-group"><label>Tanggal Keluar</label><input type="date" name="tanggal_keluar"></div>
                    <div class="form-group"><label>Status Kepegawaian</label>
                        <select name="status_kepegawaian" required>
                            <option value="aktif">Aktif</option>
                            <option value="resign">Resign</option>
                            <option value="dipecat">Dipecat</option>
                        </select>
                    </div>
                    <div class="form-group checkbox">
                        <label><input type="checkbox" name="is_bermasalah" value="1"> Bermasalah</label>
                    </div>
                    <div class="form-group full"><label>Catatan</label><textarea name="catatan" rows="3"></textarea></div>
                </div>
                <div class="actions">
                    <button type="button" class="btn btn-outline" id="btnCancel">Batal</button>
                    <button type="submit" class="btn" id="btnSubmit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const openBtn = document.getElementById('btnOpenModal');
            const modal = document.getElementById('modalForm');
            const cancel = document.getElementById('btnCancel');
            const form = document.getElementById('formHistory');
            const methodField = document.getElementById('methodField');
            const title = document.getElementById('modalTitle');
            const submit = document.getElementById('btnSubmit');

            function openModal() {
                modal.classList.add('show');
            }

            function closeModal() {
                modal.classList.remove('show');
                form.reset();
                methodField.value = 'POST';
                form.action = "{{ route('history.karyawan.store') }}";
                title.textContent = 'Tambah History';
                submit.textContent = 'Simpan';
            }

            openBtn && openBtn.addEventListener('click', openModal);
            cancel && cancel.addEventListener('click', closeModal);

            window.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeModal();
            });

            modal.addEventListener('click', e => {
                if (e.target === modal) closeModal();
            });

            document.querySelectorAll('.dropdown').forEach(dd => {
                const btn = dd.querySelector('.dot-btn');
                const menu = dd.querySelector('.dd-menu');
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    btn.classList.toggle('active');
                });
                document.addEventListener('click', () => btn.classList.remove('active'));
                menu.addEventListener('click', e => e.stopPropagation());
                const editBtn = menu.querySelector('[data-edit]');
                editBtn.addEventListener('click', () => {
                    const obj = JSON.parse(editBtn.getAttribute('data-edit'));
                    openModal();
                    title.textContent = 'Edit History';
                    submit.textContent = 'Update';
                    methodField.value = 'PUT';
                    form.action = "{{ url('history-karyawan') }}/" + obj.id;
                    form.nama_karyawan.value = obj.nama_karyawan;
                    form.jabatan.value = obj.jabatan;
                    form.tanggal_masuk.value = (obj.tanggal_masuk || '').substring(0, 10);
                    form.tanggal_keluar.value = obj.tanggal_keluar ? obj.tanggal_keluar.substring(0, 10) : '';
                    form.status_kepegawaian.value = obj.status_kepegawaian;
                    form.is_bermasalah.checked = obj.is_bermasalah ? true : false;
                    form.catatan.value = obj.catatan || '';
                });
            });

            const toast = document.getElementById('toastSuccess');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('toast-hide');
                    setTimeout(() => toast.remove(), 600);
                }, 2600);
            }
        })();
    </script>
    <script>
        // Client-side filtering to mimic managedata behavior when $data is not paginated
        (function(){
            const searchInput = document.getElementById('searchEmployee');
            const tableBody = document.querySelector('.table-hist tbody');
            const chips = document.getElementById('toolbarChips');

            function updateChips(count){
                if(!chips) return;
                chips.innerHTML='';
                const chip = document.createElement('div');
                chip.className = 'chip-info primary';
                chip.innerHTML = `<i class="fa-solid fa-filter"></i> ${count} terlihat`;
                chips.appendChild(chip);
            }

            function filterRows(q){
                const term = (q||'').toLowerCase().trim();
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                let shown = 0;
                rows.forEach(tr=>{
                    // skip the placeholder empty row
                    const tds = Array.from(tr.querySelectorAll('td')).map(td=>td.textContent.toLowerCase());
                    const matched = term === '' || tds.some(txt => txt.includes(term));
                    tr.style.display = matched ? '' : 'none';
                    if(matched) shown++;
                });
                updateChips(shown);
            }

            if(searchInput){
                searchInput.addEventListener('input', e=>{ filterRows(e.target.value); });
                // run once on load
                filterRows(searchInput.value || '');
            }
        })();
    </script>
</body>

</html>
  </div>
</body>
</html>
