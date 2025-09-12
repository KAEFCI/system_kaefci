<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Data</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/hrd/managedata.css') }}" />
  <style>
    /* Fix z-index dropdown menu aksi */
    .action-dropdown {
      position: absolute;
      z-index: 9999;
      background: #fff;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
      border-radius: 8px;
      min-width: 120px;
      padding: 6px 0;
      display: none;
      top: calc(100% + 6px);
      right: 0;
    }

    .action-menu.open .action-dropdown {
      display: block;
    }

    .action-menu {
      position: relative;
      z-index: 1;
      /* base */
    }

    .action-menu.open {
      z-index: 10000;
    }

    /* Optional: make sure menu is above table overflow */
    table {
      position: relative;
      z-index: 1;
    }

    thead,
    tbody,
    tr,
    th,
    td {
      overflow: visible;
    }

    /* Angkat baris yang sedang menampilkan menu aksi di atas baris lain */
    tbody tr.menu-open {
      position: relative;
      z-index: 20000;
    }
  </style>
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
      <h1>{{ isset($title) ? $title : 'Manage Data' }}</h1>
    </header>
    <div class="container">

      <!-- Section: Tabel Karyawan -->
      <div class="section">
        <div class="section-header">
          <h2><i class="fa-solid fa-users"></i> Daftar Karyawan</h2>
          <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Tambah Karyawan</button>
        </div>
        <div class="toolbar" id="employeeToolbar">
          <div class="field stretch">
            <label for="searchEmployee">Search</label>
            <input type="text" id="searchEmployee" placeholder="Nama / Email..." autocomplete="off" />
          </div>
          <div class="field">
            <label for="filterRole">Jabatan</label>
            <select id="filterRole">
              <option value="">All</option>
              <option value="karyawan">Karyawan</option>
              <option value="kasir">Kasir</option>
              <option value="supervisor">Supervisor</option>
            </select>
          </div>
          <div class="field">
            <label for="filterStatus">Status</label>
            <select id="filterStatus">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="disable">Disable</option>
            </select>
          </div>
          <div class="chips" id="toolbarChips"></div>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Jabatan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="employeeTable"></tbody>
        </table>
        <div id="paginationBar" class="pagination"></div>
      </div>

      <!-- Section: Tim & Struktur -->
      <div class="section">
        <div class="section-header">
          <h2><i class="fa-solid fa-diagram-project"></i> Team</h2>
          <button class="btn btn-primary" onclick="openAddTeamModal()"><i class="fa-solid fa-people-group"></i> Tambah Team</button>
        </div>
        <div class="team-container" id="teamContainer"></div>

      </div>

      <!-- Dynamic Modal Root (single overlay) -->
      <div class="modal-overlay" id="modalOverlayRoot" style="display:none;"></div>
      <!-- Success Popup -->
      <div id="successPopup" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;background:rgba(0,0,0,.35);">
        <div style="background:#fff;padding:28px 34px;border-radius:14px;box-shadow:0 10px 30px -5px rgba(0,0,0,.25);text-align:center;max-width:340px;width:90%;font-family:Poppins;">
          <div style="font-size:46px;line-height:1;margin-bottom:6px;color:#16a34a;">&#10003;</div>
          <h3 style="margin:0 0 6px;font-size:20px;font-weight:600;">Berhasil</h3>
          <p id="successPopupMsg" style="margin:0 0 18px;font-size:14px;color:#444;">Data Berhasil Disimpan</p>
          <button type="button" onclick="hideSuccessPopup()" style="background:#2563eb;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;cursor:pointer;">Tutup</button>
        </div>
      </div>

      <script>
        // ==============================
        // DATA DINAMIS VIA API STAFF
        // State & helpers
        let employees = [];
        let teams = [];
        let filters = {
          search: '',
          role: '',
          status: ''
        };
        let currentPage = 1;
        let totalPages = 1;
        let pageSize = 10;
        let lastFilteredCount = 0;
        let draggedData = null;
        let editingEmployee = null;
        let editingTeam = null;
        const collapsedTeams = new Set();

        function debounce(fn, delay) {
          let t;
          return function(...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
          }
        }

        // Format tampilan ID: ACC01 -> ACC1, ACC015 -> ACC15
        function formatId(id) {
          if (!id) return '-';
          const m = String(id).match(/^acc0*([0-9]+)$/i);
          return m ? 'ACC' + m[1] : id;
        }

        async function fetchEmployees(page = 1) {
          try {
            const params = new URLSearchParams();
            params.set('page', page);
            if (filters.search) params.set('search', filters.search);
            if (filters.role) params.set('role', filters.role);
            if (filters.status) params.set('status', filters.status);

            const res = await fetch(`{{ route('staff.list') }}?${params.toString()}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            if (!res.ok) {
              console.error('Gagal memuat staff', res.status);
              // tampilkan placeholder error agar tidak terlihat kosong tanpa sebab
              const tbody = document.getElementById('employeeTable');
              if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:#666;padding:18px;">Gagal memuat data (${res.status})</td></tr>`;
              const container = document.getElementById('teamContainer');
              if (container) container.innerHTML = `<div style="padding:18px;color:#666;">Gagal memuat tim (${res.status})</div>`;
              return;
            }

            const json = await res.json();
            const payload = json;

            employees = payload.data || [];
            teams = (payload.teams || []).map(t => ({
              id: t.id,
              name: t.name,
              members: (t.members || [])
            }));

            totalPages = payload.meta?.last_page ?? payload.last_page ?? 1;
            currentPage = payload.meta?.current_page ?? payload.current_page ?? page;
            pageSize = payload.meta?.per_page ?? payload.per_page ?? pageSize;
            lastFilteredCount = payload.meta?.total ?? payload.total ?? employees.length;

            updateUI();
          } catch (err) {
            console.error('Error fetchEmployees', err);
            const tbody = document.getElementById('employeeTable');
            if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:#666;padding:18px;">Gagal memuat data</td></tr>`;
            const container = document.getElementById('teamContainer');
            if (container) container.innerHTML = `<div style=\"padding:18px;color:#666;\">Gagal memuat tim</div>`;
          }
        }

        // pagination style now in static CSS

        // ================= MODAL LOADER HELPERS =================
        const modalCache = new Set();
        async function loadModal(filePath, expectedId) {
          if (!modalCache.has(expectedId) || !document.getElementById(expectedId)) {
            const html = await fetch(filePath).then(r => {
              if (!r.ok) throw new Error('Gagal memuat modal: ' + filePath);
              return r.text();
            });
            document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
            modalCache.add(expectedId);
          }
        }

        function showOnlyModal(modalId) {
          const root = document.getElementById('modalOverlayRoot');
          root.querySelectorAll('.modal').forEach(m => {
            if (m.id === modalId) {
              m.style.display = 'block';
            } else {
              m.style.display = 'none';
            }
          });
          root.style.display = 'flex';
        }

        function hideAllModals() {
          const root = document.getElementById('modalOverlayRoot');
          root.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
          root.style.display = 'none';
        }

        function populateTeamSelect(selectedTeamId) {
          const sel = document.getElementById('employeeTeam');
          if (!sel) return;
          sel.innerHTML = '';
          const optNone = document.createElement('option');
          optNone.value = '';
          optNone.textContent = 'Belum Ditugaskan';
          sel.appendChild(optNone);
          teams.forEach(t => {
            const o = document.createElement('option');
            o.value = t.id;
            o.textContent = t.name;
            sel.appendChild(o);
          });
          if (selectedTeamId) {
            sel.value = selectedTeamId;
          }
        }

        async function openAddModal() {
          // Load modal only if not already present
          if (!document.getElementById('employeeModal')) {
            const html = await fetch('/modal/edit').then(r => r.text());
            document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
          }
          editingEmployee = null;
          document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user"></i> Tambah Karyawan';
          document.getElementById('employeeName').value = '';
          document.getElementById('employeeRole').value = 'karyawan';
          document.getElementById('employeeEmail').value = '';
          document.getElementById('employeePassword').value = '';
          document.getElementById('employeeStatus').value = 'active';
          document.getElementById('roleGroup').classList.add('hidden');
          document.getElementById('teamSelectGroup').classList.add('hidden');
          document.getElementById('statusGroup').classList.add('hidden');
          document.getElementById('advSeparator').classList.add('hidden');
          const modalEl = document.getElementById('employeeModal');
          modalEl.classList.remove('wide', 'mode-edit');
          modalEl.classList.add('compact', 'mode-add');
          document.getElementById('employeeFormGrid').classList.remove('two-cols');
          document.getElementById('modalSaveBtn').textContent = 'Simpan';
          document.getElementById('modalSaveBtn').onclick = addEmployee;

          // pastikan team select sudah terisi (meskipun group tersembunyi)
          populateTeamSelect('');

          showOnlyModal('employeeModal');
          setTimeout(() => {
            const el = document.getElementById('employeeName');
            if (el) el.focus();
          }, 50);
        }

        // ================= TEAM MODAL HANDLERS =================
        async function openAddTeamModal() {
          if (!document.getElementById('teamModalBox')) {
            const html = await fetch('/modal/add-team').then(r => r.text());
            document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
          }
          initTeamTaskOptions();
          // Ensure the unassigned dropdown is visible in Add mode
          const unGroupAdd = document.getElementById('unassignedSelect')?.closest('.form-group');
          if (unGroupAdd) unGroupAdd.style.display = '';
          // Reset employee dropdown state until task selected
          const empSelAdd = document.getElementById('unassignedSelect');
          if (empSelAdd) {
            empSelAdd.disabled = true;
            empSelAdd.innerHTML = '<option value="" disabled selected>Pilih tugas terlebih dahulu...</option>';
          }
          editingTeam = null;
          document.getElementById('teamModalTitle').innerHTML = '<i class="fa-solid fa-people-group"></i> Tambah Team';
          const teamSelect = document.getElementById('teamNameSelect');
          if (teamSelect) teamSelect.value = '';
          const teamLabelEl = document.getElementById('selectedTeamLabel');
          if (teamLabelEl) teamLabelEl.textContent = 'Pilih team...';
          const btn = document.getElementById('teamSaveBtn');
          btn.textContent = 'Simpan';
          btn.onclick = saveNewTeam;
          showOnlyModal('teamModalBox');
          setTimeout(() => {
            const el = document.getElementById('teamNameSelect');
            if (el) el.focus();
          }, 40);
        }
        async function openEditTeamModal(name) {
          const t = teams.find(tm => tm.name === name);
          if (!t) return;
          if (!document.getElementById('teamModalBox')) {
            const html = await fetch('/modal/add-team').then(r => r.text());
            document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
          }
          initTeamTaskOptions();
          // hide the unassigned dropdown in Edit mode (not applied on update)
          const unGroupEdit = document.getElementById('unassignedSelect')?.closest('.form-group');
          if (unGroupEdit) unGroupEdit.style.display = 'none';
          editingTeam = t;
          document.getElementById('teamModalTitle').innerHTML = '<i class="fa-solid fa-pen"></i> Edit Team';
          const teamSelect2 = document.getElementById('teamNameSelect');
          if (teamSelect2) teamSelect2.value = t.name;
          const teamLabelEl2 = document.getElementById('selectedTeamLabel');
          if (teamLabelEl2) teamLabelEl2.textContent = t.name;
          const btn = document.getElementById('teamSaveBtn');
          btn.textContent = 'Update';
          btn.onclick = saveEditTeam;
          showOnlyModal('teamModalBox');
          setTimeout(() => {
            const el = document.getElementById('teamNameSelect');
            if (el) el.focus();
          }, 40);
        }

        function initTeamDropdown() {
          const btn = document.getElementById('selectedTeamBtn');
          const menu = document.getElementById('teamDropdownMenu');
          const label = document.getElementById('selectedTeamLabel');
          const hidden = document.getElementById('teamNameSelect');
          if (!btn || !menu || !label || !hidden) return;
          if (btn.dataset.initialized === '1') return; // avoid duplicate binding
          btn.dataset.initialized = '1';
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
          });
          menu.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', (e) => {
              e.preventDefault();
              hidden.value = item.getAttribute('data-value') || '';
              label.textContent = item.textContent.trim();
              menu.style.display = 'none';
            });
          });
          document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
              menu.style.display = 'none';
            }
          });
        }

        // Load unassigned employees into the dropdown inside Team modal
        async function loadUnassignedIntoSelect(teamValue, taskValue) {
          const box = document.getElementById('unassignedSelect');
          if (!box) return;
          box.innerHTML = '<option value="" disabled selected>Memuat data...</option>';
          try {
            const res = await fetch(`{{ route('staff.unassigned') }}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            const items = json.data || [];
            if (items.length === 0) {
              box.innerHTML = '<option value="" disabled selected>Tidak ada karyawan tanpa team</option>';
              return;
            }
            box.innerHTML = '<option value="" disabled selected>Pilih karyawan...</option>';
            items.forEach(u => {
              const opt = document.createElement('option');
              opt.value = u.id;
              opt.textContent = `${formatId(u.id)} — ${u.name} (${u.role})`;
              box.appendChild(opt);
            });
          } catch (e) {
            box.innerHTML = '<option value="" disabled selected>Gagal memuat</option>';
          }
        }

        function initTeamTaskOptions() {
          const teamSelect = document.getElementById('teamNameSelect');
          const taskSelect = document.getElementById('teamTaskSelect');
          const empSelect = document.getElementById('unassignedSelect');
          if (!teamSelect || !taskSelect) return;
          // Avoid multiple bindings
          if (teamSelect.dataset.taskInitialized === '1') return;
          teamSelect.dataset.taskInitialized = '1';

          const TASKS = {
            'Front of House (FOH) / Service Crew': [
              'Kasir',
              'Order taker (penerima pesanan)',
              'Dining area crew (menjaga kebersihan area makan)'
            ],
            'Back of House (BOH) / Kitchen Crew': [
              'Cook / Fryer (menggoreng ayam, membuat menu)',
              'Food preparer (menyiapkan bahan makanan)',
              'Packing (mengemas pesanan dine-in / take away / delivery)'
            ],
            'Drive Thru / Delivery Crew': [
              'Penerima pesanan drive thru',
              'Pengantar pesanan (delivery rider)'
            ]
          };

          const resetEmpSelect = () => {
            if (!empSelect) return;
            empSelect.disabled = true;
            empSelect.innerHTML = '<option value="" disabled selected>Pilih tugas terlebih dahulu...</option>';
          };

          const resetTasks = () => {
            taskSelect.innerHTML = '<option value="" disabled selected>Pilih tugas...</option>';
            taskSelect.disabled = true;
            resetEmpSelect();
          };

          const fillTasks = (teamValue) => {
            resetTasks();
            const list = TASKS[teamValue] || [];
            list.forEach(t => {
              const opt = document.createElement('option');
              opt.value = t;
              opt.textContent = t;
              taskSelect.appendChild(opt);
            });
            taskSelect.disabled = list.length === 0;
          };

          // Initial state
          if (teamSelect.value) fillTasks(teamSelect.value);
          // On change
          teamSelect.addEventListener('change', (e) => {
            fillTasks(e.target.value);
            resetEmpSelect();
          });

          // Enable and populate employees only after a task is chosen
          taskSelect.addEventListener('change', async (e) => {
            if (!empSelect) return;
            const taskVal = e.target.value;
            if (taskVal) {
              empSelect.disabled = false;
              await loadUnassignedIntoSelect(teamSelect.value, taskVal);
            } else {
              resetEmpSelect();
            }
          });
        }

        function closeTeamModal() {
          hideAllModals();
          editingTeam = null;
        }

        async function saveNewTeam() {
          const name = (document.getElementById('teamNameSelect')?.value || '').trim();
          if (!name) {
            alert('Nama team wajib diisi.');
            return;
          }
          const selectedTask = (document.getElementById('teamTaskSelect')?.value || '').trim();
          if (!selectedTask) {
            alert('Pilih tugas terlebih dahulu.');
            return;
          }
          // collect selected unassigned user ID (single-select)
          const sel = document.getElementById('unassignedSelect');
          const memberIds = [];
          if (sel && sel.value) memberIds.push(sel.value);
          if (memberIds.length === 0) {
            alert('Pilih karyawan terlebih dahulu.');
            return;
          }
          const res = await fetch(`{{ route('teams.store') }}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              name,
              member_ids: memberIds
            })
          });
          if (res.ok) {
            closeTeamModal();
            showSuccessPopup('Team Berhasil Ditambah');
            fetchEmployees(currentPage);
          } else {
            const j = await res.json().catch(() => ({}));
            alert('Gagal tambah team: ' + (j.message || res.status));
          }
        }

        async function saveEditTeam() {
          if (!editingTeam) return;
          const newName = (document.getElementById('teamNameSelect')?.value || '').trim();
          if (!newName) {
            alert('Nama team wajib diisi.');
            return;
          }
          const res = await fetch(`{{ url('/hrd/teams') }}/${editingTeam.id}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new URLSearchParams({
              _method: 'PUT',
              name: newName
            })
          });
          if (res.ok) {
            closeTeamModal();
            showSuccessPopup('Team Berhasil Diupdate');
            fetchEmployees(currentPage);
          } else {
            const j = await res.json().catch(() => ({}));
            alert('Gagal update team: ' + (j.message || res.status));
          }
        }

        async function deleteTeam(name) {
          const t = teams.find(tm => tm.name === name);
          if (!t) return;
          if (!confirm(`Hapus team "${name}"? Anggota akan menjadi belum ditugaskan.`)) return;
          const res = await fetch(`{{ url('/hrd/teams') }}/${t.id}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new URLSearchParams({
              _method: 'DELETE'
            })
          });
          if (res.ok) {
            showSuccessPopup('Team Berhasil Dihapus');
            fetchEmployees(currentPage);
          } else {
            alert('Gagal hapus team');
          }
        }

        async function openEditModal(name) {
          const emp = employees.find(e => e.name === name);
          if (!emp) return;
          if (!document.getElementById('employeeModal')) {
            const html = await fetch('/modal/edit').then(r => r.text());
            document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
          }
          editingEmployee = emp;
          document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-pen"></i> Edit Karyawan';
          document.getElementById('employeeName').value = emp.name;
          document.getElementById('roleGroup').classList.remove('hidden');
          document.getElementById('teamSelectGroup').classList.remove('hidden');
          document.getElementById('statusGroup').classList.remove('hidden');
          document.getElementById('advSeparator').classList.remove('hidden');
          document.getElementById('employeeRole').value = emp.role;
          const currentTeam = emp.team_id ? emp.team_id : '';
          populateTeamSelect(currentTeam);
          document.getElementById('employeeEmail').value = emp.email || '';
          document.getElementById('employeePassword').value = '';
          document.getElementById('employeeStatus').value = emp.status || 'active';
          // enable wide two-column layout in edit mode for more fields
          const modalEl2 = document.getElementById('employeeModal');
          modalEl2.classList.add('wide', 'compact', 'mode-edit');
          modalEl2.classList.remove('mode-add');
          document.getElementById('employeeFormGrid').classList.add('two-cols');
          document.getElementById('modalSaveBtn').textContent = 'Update';
          document.getElementById('modalSaveBtn').onclick = saveEdit;
          showOnlyModal('employeeModal');
          setTimeout(() => {
            const el = document.getElementById('employeeName');
            if (el) el.focus();
          }, 50);
        }

        function closeModal() {
          hideAllModals();
          if (!document.getElementById('employeeModal')) return;
          document.getElementById('employeeName').value = "";
          document.getElementById('employeeRole').value = "karyawan";
          document.getElementById('employeeEmail').value = "";
          document.getElementById('employeePassword').value = "";
          editingEmployee = null;
        }
        // click overlay to close
        document.addEventListener('click', e => {
          const root = document.getElementById('modalOverlayRoot');
          if (root.style.display !== 'none' && e.target === root) {
            hideAllModals();
          }
        });

        // Tambah karyawan baru (selalu role karyawan)
        async function addEmployee() {
          const name = document.getElementById('employeeName').value.trim();
          const email = document.getElementById('employeeEmail').value.trim();
          const password = document.getElementById('employeePassword').value.trim();
          if (!name || !email || !password) {
            alert('Lengkapi form.');
            return;
          }
          const body = new FormData();
          body.append('name', name);
          body.append('email', email);
          body.append('password', password);
          body.append('role', 'karyawan');
          body.append('status', 'active');
          const res = await fetch("{{ route('staff.store') }}", {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body
          });
          if (res.ok) {
            closeModal();
            showSuccessPopup('Data Berhasil Disimpan');
            fetchEmployees(currentPage);
          } else {
            const j = await res.json().catch(() => ({}));
            alert('Gagal simpan: ' + (j.message || res.status));
          }
        }

        async function saveEdit() {
          if (!editingEmployee) return;
          const id = editingEmployee.id;
          const newName = document.getElementById('employeeName').value.trim();
          const newRole = document.getElementById('employeeRole').value;
          const newEmail = document.getElementById('employeeEmail').value.trim();
          const newStatus = document.getElementById('employeeStatus').value;
          const newPassword = document.getElementById('employeePassword').value.trim();
          const newTeamId = document.getElementById('employeeTeam').value;
          if (!newName || !newEmail) {
            alert('Nama & Email wajib.');
            return;
          }
          const form = new FormData();
          form.append('name', newName);
          form.append('email', newEmail);
          form.append('role', newRole);
          form.append('status', newStatus);
          if (newPassword) form.append('password', newPassword);
          if (newTeamId) form.append('team_id', newTeamId);
          else form.append('team_id', '');
          form.append('_method', 'PUT');
          const res = await fetch(`{{ url('/hrd/staff') }}/${id}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: form
          });
          if (res.ok) {
            closeModal();
            showSuccessPopup('Data Berhasil Diperbarui');
            fetchEmployees(currentPage);
          } else {
            const j = await res.json().catch(() => ({}));
            alert('Gagal update: ' + (j.message || res.status));
          }
        }

        // Hapus karyawan
        async function deleteEmployee(name) {
          const emp = employees.find(e => e.name === name);
          if (!emp) return;
          if (!confirm(`Hapus staff "${name}"?`)) return;
          const res = await fetch(`{{ url('/hrd/staff') }}/${emp.id}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new URLSearchParams({
              '_method': 'DELETE'
            })
          });
          if (res.ok) {
            showSuccessPopup('Data Berhasil Dihapus');
            fetchEmployees(currentPage);
          } else {
            alert('Gagal hapus');
          }
        }

        // Action menu handling
        document.addEventListener('click', (e) => {
          const trigger = e.target.closest('.action-trigger');
          if (trigger) {
            const menu = trigger.closest('.action-menu');
            const open = menu.classList.contains('open');
            // tutup semua menu dan reset elevasi baris
            document.querySelectorAll('.action-menu.open').forEach(m => {
              m.classList.remove('open');
              const row = m.closest('tr');
              if (row) row.classList.remove('menu-open');
            });
            if (!open) {
              menu.classList.add('open');
              const row = menu.closest('tr');
              if (row) row.classList.add('menu-open');
            }
            return;
          }
          const item = e.target.closest('.menu-item');
          if (item) {
            const action = item.dataset.action;
            const name = item.dataset.name;
            if (action === 'edit') {
              openEditModal(name);
            } else if (action === 'delete') {
              deleteEmployee(name);
            } else if (action === 'edit-team') {
              openEditTeamModal(name);
            } else if (action === 'delete-team') {
              deleteTeam(name);
            }
          }
          // click outside closes
          if (!e.target.closest('.action-menu')) {
            document.querySelectorAll('.action-menu.open').forEach(m => {
              m.classList.remove('open');
              const row = m.closest('tr');
              if (row) row.classList.remove('menu-open');
            });
          }
        });
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            document.querySelectorAll('.action-menu.open').forEach(m => {
              m.classList.remove('open');
              const row = m.closest('tr');
              if (row) row.classList.remove('menu-open');
            });
          }
        });
        // Tutup menu saat scroll/resize untuk hindari posisi salah/tumpang tindih
        window.addEventListener('scroll', () => {
          document.querySelectorAll('.action-menu.open').forEach(m => {
            m.classList.remove('open');
            const row = m.closest('tr');
            if (row) row.classList.remove('menu-open');
          });
        }, true);
        window.addEventListener('resize', () => {
          document.querySelectorAll('.action-menu.open').forEach(m => {
            m.classList.remove('open');
            const row = m.closest('tr');
            if (row) row.classList.remove('menu-open');
          });
        });
        // collapse toggle (btn-collapse)
        document.addEventListener('click', (e) => {
          const collapseBtn = e.target.closest('[data-collapse-team]');
          if (collapseBtn) {
            const teamName = collapseBtn.getAttribute('data-collapse-team');
            if (collapsedTeams.has(teamName)) collapsedTeams.delete(teamName);
            else collapsedTeams.add(teamName);
            updateUI();
          }
        });

        // Filter events (gunakan fetchEmployees agar server-side filtering jalan)
        const debouncedSearch = debounce(() => {
          currentPage = 1;
          fetchEmployees(currentPage);
        }, 300);

        document.addEventListener('input', (e) => {
          if (e.target.id === 'searchEmployee') {
            filters.search = e.target.value.toLowerCase().trim();
            currentPage = 1;
            debouncedSearch();
          }
        });
        document.addEventListener('change', (e) => {
          if (e.target.id === 'filterRole') {
            filters.role = e.target.value;
            currentPage = 1;
            fetchEmployees(currentPage);
          }
          if (e.target.id === 'filterStatus') {
            filters.status = e.target.value;
            currentPage = 1;
            fetchEmployees(currentPage);
          }
        });
        // Pagination button handler (delegated)
        document.addEventListener('click', (e) => {
          const pBtn = e.target.closest('[data-page-btn]');
          if (!pBtn) return;
          const computedTotalPages = Math.max(1, Math.ceil((lastFilteredCount || 1) / pageSize));
          const type = pBtn.dataset.pageBtn;

          if (type === 'prev' && currentPage > 1) {
            currentPage--;
            fetchEmployees(currentPage);
          } else if (type === 'next' && currentPage < computedTotalPages) {
            currentPage++;
            fetchEmployees(currentPage);
          } else if (type === 'num') {
            const target = parseInt(pBtn.dataset.page, 10);
            if (!isNaN(target) && target >= 1 && target <= computedTotalPages && target !== currentPage) {
              currentPage = target;
              fetchEmployees(currentPage);
            }
          }
        });


        // (form tambah tim diganti modal; fungsi lama dihapus)

        // Drag & Drop handlers
        function handleDragStart(e, member, fromTeamIndex) {
          draggedData = {
            member,
            fromTeamIndex
          };
          e.dataTransfer.effectAllowed = "move";
        }

        function allowDrop(e) {
          e.preventDefault();
        }

        function handleDrop(e, toTeamIndex) {
          e.preventDefault();
          if (!draggedData) return;
          const {
            member,
            fromTeamIndex
          } = draggedData;

          if (fromTeamIndex === toTeamIndex) {
            draggedData = null;
            return; // tidak ada perubahan
          }

          if (toTeamIndex !== null) {
            const isSupervisor = member.role === "supervisor";
            const existingSupervisor = teams[toTeamIndex].members.find(m => m.role === "supervisor");
            if (isSupervisor && existingSupervisor) {
              alert("Setiap tim hanya boleh memiliki satu supervisor.");
              updateUI();
              draggedData = null;
              return;
            }
          }

          // Hapus dari tim lama
          if (fromTeamIndex !== null) {
            teams[fromTeamIndex].members = teams[fromTeamIndex].members.filter(m => m.id !== member.id);
          }
          // Tambah ke tim baru
          if (toTeamIndex !== null) {
            teams[toTeamIndex].members.push(member);
          }
          draggedData = null;
          updateUI();
          // Persist ke server (teamId kosong => null)
          persistAssignment(member, toTeamIndex !== null ? teams[toTeamIndex].id : '');
        }

        async function persistAssignment(member, teamId) {
          try {
            const form = new FormData();
            form.append('name', member.name);
            form.append('email', member.email || '');
            form.append('role', member.role);
            form.append('status', member.status || 'active');
            form.append('team_id', teamId);
            form.append('_method', 'PUT');
            const res = await fetch(`{{ url('/hrd/staff') }}/${member.id}`, {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: form
            });
            if (!res.ok) {
              console.warn('Gagal menyimpan perpindahan tim');
            } else {
              // refresh data agar konsisten (termasuk meta pagination jika perlu)
              fetchEmployees(currentPage);
            }
          } catch (err) {
            console.error('Error persistAssignment', err);
          }
        }

        // Update tampilan UI
        function updateUI() {
          // Tabel karyawan
          const tbody = document.getElementById("employeeTable");
          if (!tbody) return;
          tbody.innerHTML = "";

          // Server side pagination handled; employees sudah halaman aktif
          const pageItems = employees;
          if (!pageItems || pageItems.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="6" style="text-align:center;color:#666;padding:18px;">Tidak ada data</td>`;
            tbody.appendChild(tr);
          }

          // *** Hapus override lastFilteredCount di sini (meta harus dikontrol oleh fetchEmployees)
          // lastFilteredCount = employees.length; // <-- sebelumnya ini menimbulkan bug pagination

          pageItems.forEach(e => {
            const tr = document.createElement("tr");
            const roleBadge = `<span class=\"role-badge ${e.role==='supervisor'?'supervisor':''}\">${e.role}</span>`;
            const statusBadge = `<span class=\"status-badge status-${e.status}\">${e.status}</span>`;
            // login column removed
            tr.innerHTML = `
    <td>${formatId(e.id)}</td>
        <td>${e.name}</td>
        <td>${e.email||'-'}</td>
        <td>${roleBadge}</td>
        <td>${statusBadge}</td>
        <td style="text-align:center;">
          <div class="action-menu" data-name="${e.name}">
            <button class="action-trigger" aria-label="Menu aksi"><i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i></button>
            <div class="action-dropdown" role="menu">
              <button class="menu-item" data-action="edit" data-name="${e.name}" role="menuitem"><i class="fa-solid fa-pen"></i>Edit</button>
              <button class="menu-item delete" data-action="delete" data-name="${e.name}" role="menuitem"><i class="fa-solid fa-trash"></i>Delete</button>
            </div>
          </div>
        </td>`;
            tbody.appendChild(tr);
          });

          // Teams dan anggota
          const container = document.getElementById("teamContainer");
          if (!container) return;
          container.innerHTML = "";
          if (!teams || teams.length === 0) {
            const empty = document.createElement('div');
            empty.style.cssText = 'padding:18px;color:#666;';
            empty.textContent = 'Belum ada team.';
            container.appendChild(empty);
            return;
          }

          teams.forEach((team, index) => {
            const teamDiv = document.createElement("div");
            teamDiv.className = "team" + (team.members.length === 0 ? " empty" : "");
            if (collapsedTeams.has(team.name)) teamDiv.classList.add('collapsed');
            teamDiv.ondragover = allowDrop;
            teamDiv.ondrop = (e) => handleDrop(e, index);

            const header = document.createElement("div");
            header.className = 'team-header';
            const activeCount = team.members.filter(m => m.status === 'active').length;
            const disableCount = team.members.filter(m => m.status === 'disable').length;
            const sup = team.members.find(m => m.role === 'supervisor');
            header.innerHTML = `
        <div class="team-left"> 
          <i class="fa-solid fa-helmet-safety"></i>
          <span class="team-title-text">${team.name}</span>
        </div>
        <div class="team-right">
          <button class="btn-collapse" data-collapse-team="${team.name}">${collapsedTeams.has(team.name)?'<i class="fa-solid fa-chevron-down"></i>':'<i class="fa-solid fa-chevron-up"></i>'}</button>
          <span class="pill count-pill">${team.members.length} ORG</span>
          <div class="action-menu team-menu" data-team="${team.name}">
            <button class="action-trigger" aria-label="Menu team"><i class="fa-solid fa-ellipsis-vertical"></i></button>
            <div class="action-dropdown" role="menu">
              <button class="menu-item" data-entity="team" data-action="edit-team" data-name="${team.name}" role="menuitem"><i class="fa-solid fa-pen"></i>Edit</button>
              <button class="menu-item delete" data-entity="team" data-action="delete-team" data-name="${team.name}" role="menuitem"><i class="fa-solid fa-trash"></i>Delete</button>
            </div>
          </div>
        </div>`;
            teamDiv.appendChild(header);

            const body = document.createElement('div');
            body.className = 'team-body';
            if (team.members.length === 0) {
              const p = document.createElement("div");
              p.className = 'placeholder-empty';
              p.textContent = "Belum ada anggota";
              body.appendChild(p);
            }
            team.members.forEach(member => {
              const memberDiv = document.createElement("div");
              memberDiv.className = `member ${member.role === "supervisor" ? "supervisor" : ""}`;
              memberDiv.textContent = `${member.name}`;
              memberDiv.draggable = true;
              memberDiv.ondragstart = (e) => handleDragStart(e, member, index);
              body.appendChild(memberDiv);
            });
            teamDiv.appendChild(body);
            const stats = document.createElement('div');
            stats.className = 'team-footer-stats';
            stats.innerHTML = `<span><i class="fa-solid fa-user-check"></i>${activeCount} Aktif</span><span><i class="fa-solid fa-user-slash"></i>${disableCount} Non</span><span><i class="fa-solid fa-crown"></i>${sup?sup.name:'-'}</span>`;
            teamDiv.appendChild(stats);
            container.appendChild(teamDiv);
          });

          // Unassigned list telah dihapus sesuai permintaan

          // update chips summary
          const chipsWrap = document.getElementById('toolbarChips');
          if (chipsWrap) {
            chipsWrap.innerHTML = '';
            const totalShown = pageItems.length;
            const chip = document.createElement('div');
            chip.className = 'chip-info primary';
            chip.innerHTML = `<i class="fa-solid fa-filter"></i>${totalShown} terlihat`;
            chipsWrap.appendChild(chip);
            if (filters.role) {
              const c = document.createElement('div');
              c.className = 'chip-info role';
              c.innerHTML = `<i class="fa-solid fa-user-tag"></i>${filters.role}`;
              chipsWrap.appendChild(c);
            }
            if (filters.status) {
              const c = document.createElement('div');
              c.className = 'chip-info status';
              c.innerHTML = `<i class="fa-solid fa-toggle-on"></i>${filters.status}`;
              chipsWrap.appendChild(c);
            }
            if (filters.search) {
              const c = document.createElement('div');
              c.className = 'chip-info search';
              c.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i>${filters.search}`;
              chipsWrap.appendChild(c);
            }
          }

          // Pagination bar update
          const pag = document.getElementById('paginationBar');
          if (pag) {
            if (totalPages > 1) {
              const windowSize = 1;
              const pages = [];
              for (let p = 1; p <= totalPages; p++) {
                if (p === 1 || p === totalPages || (p >= currentPage - windowSize && p <= currentPage + windowSize)) pages.push(p);
              }
              const finalSeq = [];
              let prev = 0;
              pages.forEach(p => {
                if (p - prev > 1) finalSeq.push('ellipsis');
                finalSeq.push(p);
                prev = p;
              });
              const pageBtns = finalSeq.map(item => item === 'ellipsis' ? '<span class="ellipsis">...</span>' : `<button class="page-btn ${item===currentPage?'active':''}" data-page-btn="num" data-page="${item}">${item}</button>`).join('');
              pag.innerHTML = `<div class="pager-shell">
          <button data-page-btn="prev" ${currentPage===1?'disabled':''}><i class="fa-solid fa-angle-left"></i></button>
          ${pageBtns}
          <button data-page-btn="next" ${currentPage===totalPages?'disabled':''}><i class="fa-solid fa-angle-right"></i></button>
        </div><div class="page-info">Hal ${currentPage} / ${totalPages}</div>`;
            } else {
              pag.innerHTML = '';
            }
          }
        }
        // Popup helpers
        function showSuccessPopup(message) {
          const pop = document.getElementById('successPopup');
          const msgEl = document.getElementById('successPopupMsg');
          if (msgEl) msgEl.textContent = message || 'Berhasil';
          pop.style.display = 'flex';
          clearTimeout(window.__successTimer);
          window.__successTimer = setTimeout(() => {
            hideSuccessPopup();
          }, 2200);
        }

        function hideSuccessPopup() {
          const pop = document.getElementById('successPopup');
          pop.style.display = 'none';
        }
        // Initial fetch
        fetchEmployees();
      </script>
</body>

</html>