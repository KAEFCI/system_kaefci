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

<script>
  // ==============================
  // DUMMY DATA (CONTOH KFC STORE)
  // ==============================
  // Teams lebih dulu supaya bisa diisi setelah employees dibuat
  let teams = [
    { name:'Dapur Pagi', members:[] },
    { name:'Dapur Malam', members:[] },
    { name:'Frontliner', members:[] },
    { name:'Delivery', members:[] }
  ];

  // Employees contoh: 1 supervisor tiap team + staff
  let employees = [
  {id:'ACC01', name:'Rama',       email:'rama@kfc.test',       role:'supervisor', status:'active',  password:'rama123'}, // Dapur Pagi
  {id:'ACC02', name:'Siti',       email:'siti@kfc.test',       role:'karyawan',   status:'active',  password:'siti123'},
  {id:'ACC03', name:'Tono',       email:'tono@kfc.test',       role:'karyawan',   status:'active',  password:'tono123'},
  {id:'ACC04', name:'Dian',       email:'dian@kfc.test',       role:'karyawan',   status:'disable', password:'dian123'},

  {id:'ACC05', name:'Hendra',     email:'hendra@kfc.test',     role:'supervisor', status:'active',  password:'hend123'}, // Dapur Malam
  {id:'ACC06', name:'Lina',       email:'lina@kfc.test',       role:'karyawan',   status:'active',  password:'lina123'},
  {id:'ACC07', name:'Bagus',      email:'bagus@kfc.test',      role:'karyawan',   status:'disable', password:'bagus12'},
  {id:'ACC08', name:'Putri',      email:'putri@kfc.test',      role:'karyawan',   status:'active',  password:'putri12'},

  {id:'ACC09', name:'Gilang',     email:'gilang@kfc.test',     role:'supervisor', status:'active',  password:'gil1234'}, // Frontliner
  {id:'ACC10', name:'Nadia',      email:'nadia@kfc.test',      role:'karyawan',   status:'active',  password:'nadia12'},
  {id:'ACC11', name:'Yusuf',      email:'yusuf@kfc.test',      role:'karyawan',   status:'active',  password:'yusuf12'},

  {id:'ACC12', name:'Anwar',      email:'anwar@kfc.test',      role:'supervisor', status:'active',  password:'anwar12'}, // Delivery
  {id:'ACC13', name:'Riko',       email:'riko@kfc.test',       role:'karyawan',   status:'active',  password:'riko123'},
  {id:'ACC14', name:'Mega',       email:'mega@kfc.test',       role:'karyawan',   status:'disable', password:'mega123'}
  ];

  // Penugasan awal manual sesuai nama
  function assignInitialTeams(){
    const byName = Object.fromEntries(employees.map(e=>[e.name,e]));
    const map = {
      'Dapur Pagi': ['Rama','Siti','Tono','Dian'],
      'Dapur Malam': ['Hendra','Lina','Bagus','Putri'],
      'Frontliner': ['Gilang','Nadia','Yusuf'],
      'Delivery': ['Anwar','Riko','Mega']
    };
    teams.forEach(t=>{t.members = (map[t.name]||[]).map(n=>byName[n]).filter(Boolean);});
  }
  assignInitialTeams();
  let draggedData = null;
  let editingEmployee = null; // reference to employee object when editing
  let editingTeam = null; // reference to team object when editing
  let collapsedTeams = new Set();
  const filters = { search:'', role:'', status:'' };
  let currentPage = 1;
  const pageSize = 10;
  let lastFilteredCount = 0; // untuk hitung total halaman

  // pagination style now in static CSS

  // ================= MODAL LOADER HELPERS =================
  const modalCache = new Set();
  async function loadModal(filePath, expectedId){
    if(!modalCache.has(expectedId) || !document.getElementById(expectedId)){
      const html = await fetch(filePath).then(r=>{
        if(!r.ok) throw new Error('Gagal memuat modal: '+filePath);
        return r.text();
      });
      document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
      modalCache.add(expectedId);
    }
  }
  function showOnlyModal(modalId){
    const root=document.getElementById('modalOverlayRoot');
    root.querySelectorAll('.modal').forEach(m=>{ if(m.id===modalId){m.style.display='block';} else {m.style.display='none';}});
    root.style.display='flex';
  }
  function hideAllModals(){
    const root=document.getElementById('modalOverlayRoot');
    root.querySelectorAll('.modal').forEach(m=>m.style.display='none');
    root.style.display='none';
  }
  function populateTeamSelect(selectedTeamName){
    const sel = document.getElementById('employeeTeam'); if(!sel) return;
    sel.innerHTML='';
    const optNone = document.createElement('option'); optNone.value=''; optNone.textContent='Belum Ditugaskan'; sel.appendChild(optNone);
    teams.forEach(t=>{ const o=document.createElement('option'); o.value=t.name; o.textContent=t.name; sel.appendChild(o); });
    if(selectedTeamName){ sel.value=selectedTeamName; }
  }

  async function openAddModal(){
  // Load modal only if not already present
  if (!document.getElementById('employeeModal')) {
    const html = await fetch('/modal/edit').then(r=>r.text());
    document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
  }
  editingEmployee = null;
  document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user"></i> Tambah Karyawan';
  document.getElementById('employeeName').value='';
  document.getElementById('employeeRole').value='karyawan';
  document.getElementById('employeeEmail').value='';
  document.getElementById('employeePassword').value='';
  document.getElementById('employeeStatus').value='active';
  document.getElementById('roleGroup').classList.add('hidden');
  document.getElementById('teamSelectGroup').classList.add('hidden');
  document.getElementById('statusGroup').classList.add('hidden');
  document.getElementById('advSeparator').classList.add('hidden');
  const modalEl = document.getElementById('employeeModal');
  modalEl.classList.remove('wide','mode-edit');
  modalEl.classList.add('compact','mode-add');
  document.getElementById('employeeFormGrid').classList.remove('two-cols');
  document.getElementById('modalSaveBtn').textContent='Simpan';
  document.getElementById('modalSaveBtn').onclick = addEmployee;
  showOnlyModal('employeeModal');
  setTimeout(()=>document.getElementById('employeeName').focus(),50);
  }

  // ================= TEAM MODAL HANDLERS =================
  async function openAddTeamModal(){
  if (!document.getElementById('teamModalBox')) {
    const html = await fetch('/modal/add-team').then(r=>r.text());
    document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
  }
  editingTeam = null;
  document.getElementById('teamModalTitle').innerHTML = '<i class="fa-solid fa-people-group"></i> Tambah Team';
  document.getElementById('teamNameInput').value='';
  const btn = document.getElementById('teamSaveBtn');
  btn.textContent='Simpan';
  btn.onclick = saveNewTeam;
  showOnlyModal('teamModalBox');
  setTimeout(()=>document.getElementById('teamNameInput').focus(),40);
  }
  async function openEditTeamModal(name){
    const t = teams.find(tm=>tm.name===name); if(!t) return;
    if (!document.getElementById('teamModalBox')) {
      const html = await fetch('/modal/add-team').then(r=>r.text());
      document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
    }
    editingTeam = t;
    document.getElementById('teamModalTitle').innerHTML = '<i class="fa-solid fa-pen"></i> Edit Team';
    document.getElementById('teamNameInput').value = t.name;
    const btn = document.getElementById('teamSaveBtn');
    btn.textContent='Update';
    btn.onclick = saveEditTeam;
    showOnlyModal('teamModalBox');
    setTimeout(()=>document.getElementById('teamNameInput').focus(),40);
  }
  function closeTeamModal(){hideAllModals();editingTeam=null;}
  function saveNewTeam(){
    const name = document.getElementById('teamNameInput').value.trim();
    if(!name){alert('Nama team wajib diisi.');return;}
    if(teams.find(t=>t.name.toLowerCase()===name.toLowerCase())){alert('Nama team sudah ada.');return;}
    teams.push({name, members:[]});
    closeTeamModal(); updateUI();
  }
  function saveEditTeam(){
    if(!editingTeam) return;
    const newName = document.getElementById('teamNameInput').value.trim();
    if(!newName){alert('Nama team wajib diisi.');return;}
    if(newName.toLowerCase()!==editingTeam.name.toLowerCase() && teams.find(t=>t.name.toLowerCase()===newName.toLowerCase())){alert('Nama team sudah ada.');return;}
    editingTeam.name = newName;
    closeTeamModal(); updateUI();
  }
  function deleteTeam(name){
    if(!confirm(`Hapus team "${name}"? Anggota akan menjadi belum ditugaskan.`)) return;
    teams = teams.filter(t=>t.name!==name);
    updateUI();
  }

  async function openEditModal(name){
    const emp = employees.find(e=>e.name===name); if(!emp) return;
    if (!document.getElementById('employeeModal')) {
      const html = await fetch('/modal/edit').then(r=>r.text());
      document.getElementById('modalOverlayRoot').insertAdjacentHTML('beforeend', html);
    }
    editingEmployee = emp;
    document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-pen"></i> Edit Karyawan';
    document.getElementById('employeeName').value=emp.name;
    document.getElementById('roleGroup').classList.remove('hidden');
    document.getElementById('teamSelectGroup').classList.remove('hidden');
    document.getElementById('statusGroup').classList.remove('hidden');
    document.getElementById('advSeparator').classList.remove('hidden');
    document.getElementById('employeeRole').value=emp.role;
    const currentTeam = teams.find(t=>t.members.includes(emp));
    populateTeamSelect(currentTeam?currentTeam.name:'');
    document.getElementById('employeeEmail').value=emp.email || '';
    document.getElementById('employeePassword').value='';
    document.getElementById('employeeStatus').value=emp.status || 'active';
    // enable wide two-column layout in edit mode for more fields
    const modalEl2 = document.getElementById('employeeModal');
    modalEl2.classList.add('wide','compact','mode-edit');
    modalEl2.classList.remove('mode-add');
    document.getElementById('employeeFormGrid').classList.add('two-cols');
    document.getElementById('modalSaveBtn').textContent='Update';
    document.getElementById('modalSaveBtn').onclick = saveEdit;
    showOnlyModal('employeeModal');
    setTimeout(()=>document.getElementById('employeeName').focus(),50);
  }

  function closeModal() {
    hideAllModals();
    if(!document.getElementById('employeeModal')) return;
    document.getElementById('employeeName').value = "";
  document.getElementById('employeeRole').value = "karyawan";
  document.getElementById('employeeEmail').value = "";
  document.getElementById('employeePassword').value = "";
    editingEmployee = null;
  }
  // click overlay to close
  document.addEventListener('click',e=>{
    const root=document.getElementById('modalOverlayRoot');
    if(root.style.display!=='none' && e.target===root){ hideAllModals(); }
  });

  // Tambah karyawan baru (selalu role karyawan)
  function addEmployee() {
  const name = document.getElementById("employeeName").value.trim();
  const email = document.getElementById("employeeEmail").value.trim();
  const password = document.getElementById("employeePassword").value.trim();
  if (!name) { alert("Nama karyawan wajib diisi."); return; }
  if (!email) { alert("Email wajib diisi."); return; }
  if (!password) { alert("Password wajib diisi."); return; }
  if (password.length < 6) { alert("Password minimal 6 karakter."); return; }
  if (employees.find(e => e.name.toLowerCase() === name.toLowerCase())) { alert("Nama sudah terdaftar."); return; }
  if (employees.find(e => e.email.toLowerCase() === email.toLowerCase())) { alert("Email sudah terdaftar."); return; }
  const nextId = 'ACC' + String(employees.length + 1).padStart(2,'0');
  employees.push({ id:nextId, name, email, role:'karyawan', status:'active', password });
    closeModal(); updateUI();
  }

  function saveEdit(){
    if(!editingEmployee) return;
  const newName = document.getElementById('employeeName').value.trim();
  const newRole = document.getElementById('employeeRole').value;
  const newEmail = document.getElementById('employeeEmail').value.trim();
  const newStatus = document.getElementById('employeeStatus').value;
  const newPassword = document.getElementById('employeePassword').value.trim();
    const teamName = document.getElementById('employeeTeam').value; // '' if unassigned
    if(!newName){alert('Nama wajib diisi.');return;}
  if(!newEmail){alert('Email wajib diisi.');return;}
    if(newName.toLowerCase()!==editingEmployee.name.toLowerCase() && employees.find(e=>e.name.toLowerCase()===newName.toLowerCase())){alert('Nama sudah dipakai.');return;}
  if(newEmail.toLowerCase()!== (editingEmployee.email||'').toLowerCase() && employees.find(e=>e.email.toLowerCase()===newEmail.toLowerCase())){alert('Email sudah dipakai.');return;}
    // Supervisor must have team
    if(newRole==='supervisor' && !teamName){alert('Supervisor harus ditugaskan ke sebuah tim.');return;}
    // Supervisor unique per team
    if(newRole==='supervisor' && teamName){
      const targetTeam = teams.find(t=>t.name===teamName);
      if(targetTeam && targetTeam.members.some(m=>m.role==='supervisor' && m!==editingEmployee)){
        alert('Tim tersebut sudah memiliki supervisor.');return;}
    }
    // Update name & role
  editingEmployee.name = newName;
  editingEmployee.role = newRole;
  editingEmployee.email = newEmail;
  editingEmployee.status = newStatus;
  if(newPassword){
    if(newPassword.length < 6){alert('Password minimal 6 karakter.');return;}
    editingEmployee.password = newPassword;
  }
    // Remove from all teams first
    teams.forEach(t=>{t.members = t.members.filter(m=>m!==editingEmployee);});
    // Assign to team if provided
    if(teamName){
      let target = teams.find(t=>t.name===teamName);
      if(target){target.members.push(editingEmployee);} }
    closeModal(); updateUI();
  }

  // Hapus karyawan
  function deleteEmployee(name) {
    if (!confirm(`Yakin ingin menghapus karyawan "${name}"?`)) return;
    const emp = employees.find(e=>e.name===name);
    employees = employees.filter(e=>e!==emp);
    teams.forEach(t=>{t.members = t.members.filter(m=>m!==emp);});
    updateUI();
  }

  // Action menu handling
  document.addEventListener('click', (e)=>{
    const trigger = e.target.closest('.action-trigger');
    if(trigger){
      const menu = trigger.closest('.action-menu');
      const open = menu.classList.contains('open');
      document.querySelectorAll('.action-menu.open').forEach(m=>m.classList.remove('open'));
      if(!open){ menu.classList.add('open'); }
      return;
    }
    const item = e.target.closest('.menu-item');
    if(item){
      const action = item.dataset.action;
      const name = item.dataset.name;
      if(action==='edit'){openEditModal(name);} 
      else if(action==='delete'){deleteEmployee(name);} 
      else if(action==='edit-team'){openEditTeamModal(name);} 
      else if(action==='delete-team'){deleteTeam(name);} }
    // click outside closes
    if(!e.target.closest('.action-menu')){
      document.querySelectorAll('.action-menu.open').forEach(m=>m.classList.remove('open'));
    }
  });
  document.addEventListener('keydown',(e)=>{if(e.key==='Escape'){document.querySelectorAll('.action-menu.open').forEach(m=>m.classList.remove('open'));}});
  // collapse toggle (btn-collapse)
  document.addEventListener('click',(e)=>{
    const collapseBtn = e.target.closest('[data-collapse-team]');
    if(collapseBtn){
      const teamName = collapseBtn.getAttribute('data-collapse-team');
      if(collapsedTeams.has(teamName)) collapsedTeams.delete(teamName); else collapsedTeams.add(teamName);
      updateUI();
    }
  });

  // Filter events
  document.addEventListener('input',(e)=>{
    if(e.target.id==='searchEmployee'){filters.search=e.target.value.toLowerCase().trim(); currentPage=1; updateUI();}
  });
  document.addEventListener('change',(e)=>{
    if(e.target.id==='filterRole'){filters.role=e.target.value; currentPage=1; updateUI();}
    if(e.target.id==='filterStatus'){filters.status=e.target.value; currentPage=1; updateUI();}
  });
  // Pagination button handler (delegated)
  document.addEventListener('click',(e)=>{
    const pBtn = e.target.closest('[data-page-btn]');
    if(!pBtn) return;
    const totalPages = Math.max(1, Math.ceil(lastFilteredCount / pageSize));
    const type = pBtn.dataset.pageBtn;
    if(type==='prev' && currentPage>1){ currentPage--; updateUI(); }
    else if(type==='next' && currentPage<totalPages){ currentPage++; updateUI(); }
    else if(type==='num'){
      const target = parseInt(pBtn.dataset.page,10);
      if(!isNaN(target) && target>=1 && target<=totalPages && target!==currentPage){ currentPage = target; updateUI(); }
    }
  });

  // (form tambah tim diganti modal; fungsi lama dihapus)

  // Drag & Drop handlers
  function handleDragStart(e, member, fromTeamIndex) {
    draggedData = { member, fromTeamIndex };
    e.dataTransfer.effectAllowed = "move";
  }

  function allowDrop(e) {
    e.preventDefault();
  }

  function handleDrop(e, toTeamIndex) {
    e.preventDefault();
    if (!draggedData) return;
    const { member, fromTeamIndex } = draggedData;

    // Pindah ke tim sama = tidak ada perubahan
    if (fromTeamIndex === toTeamIndex) return;

    // Supervisor hanya 1 per tim
    if (toTeamIndex !== null) {
      const isSupervisor = member.role === "supervisor";
      const existingSupervisor = teams[toTeamIndex].members.find(m => m.role === "supervisor");
      if (isSupervisor && existingSupervisor) {
        alert("Setiap tim hanya boleh memiliki satu supervisor.");
        updateUI();
        return;
      }
    }

    // Hapus dari tim lama (jika ada)
    if (fromTeamIndex !== null) {
      teams[fromTeamIndex].members = teams[fromTeamIndex].members.filter(m => m.name !== member.name);
    }

    // Tambah ke tim baru (jika ada)
    if (toTeamIndex !== null) {
      teams[toTeamIndex].members.push(member);
    }

    draggedData = null;
    updateUI();
  }

  // Update tampilan UI
  function updateUI() {
    // Tabel karyawan
    const tbody = document.getElementById("employeeTable");
    tbody.innerHTML = "";
    const filtered = employees.filter(e=>{
      if(filters.role && e.role!==filters.role) return false;
      if(filters.status && e.status!==filters.status) return false;
      if(filters.search){
        const s = filters.search;
        if(!(e.name.toLowerCase().includes(s) || (e.email||'').toLowerCase().includes(s))) return false;
      }
      return true;
    });
    lastFilteredCount = filtered.length;
    const totalPages = Math.max(1, Math.ceil(lastFilteredCount / pageSize));
    if(currentPage>totalPages) currentPage = totalPages; // clamp
    const startIndex = (currentPage-1)*pageSize;
    const pageItems = filtered.slice(startIndex, startIndex + pageSize);
    pageItems.forEach(e => {
      const tr = document.createElement("tr");
      const roleBadge = `<span class=\"role-badge ${e.role==='supervisor'?'supervisor':''}\">${e.role}</span>`;
      const statusBadge = `<span class=\"status-badge status-${e.status}\">${e.status}</span>`;
  // login column removed
      tr.innerHTML = `
        <td>${e.id||'-'}</td>
        <td>${e.name}</td>
        <td>${e.email||'-'}</td>
        <td>${roleBadge}</td>
        <td>${statusBadge}</td>
        <td style=\"text-align:center;\">
          <div class=\"action-menu\" data-name=\"${e.name}\">
            <button class=\"action-trigger\" aria-label=\"Menu aksi\"><i class=\"fa-solid fa-ellipsis-vertical\" aria-hidden=\"true\"></i></button>
            <div class=\"action-dropdown\" role=\"menu\">
              <button class=\"menu-item\" data-action=\"edit\" data-name=\"${e.name}\" role=\"menuitem\"><i class=\"fa-solid fa-pen\"></i>Edit</button>
              <button class=\"menu-item delete\" data-action=\"delete\" data-name=\"${e.name}\" role=\"menuitem\"><i class=\"fa-solid fa-trash\"></i>Delete</button>
            </div>
          </div>
        </td>`;
      tbody.appendChild(tr);
    });

    // Teams dan anggota
    const container = document.getElementById("teamContainer");
    container.innerHTML = "";

    teams.forEach((team, index) => {
      const teamDiv = document.createElement("div");
      teamDiv.className = "team" + (team.members.length===0?" empty":"");
  if(collapsedTeams.has(team.name)) teamDiv.classList.add('collapsed');
      teamDiv.ondragover = allowDrop;
      teamDiv.ondrop = (e) => handleDrop(e, index);

      const header = document.createElement("div");
      header.className = 'team-header';
      const activeCount = team.members.filter(m=>m.status==='active').length;
      const disableCount = team.members.filter(m=>m.status==='disable').length;
      const sup = team.members.find(m=>m.role==='supervisor');
      header.innerHTML = `
        <div class=\"team-left\"> 
          <i class=\"fa-solid fa-helmet-safety\"></i>
          <span class=\"team-title-text\">${team.name}</span>
        </div>
        <div class=\"team-right\">
          <button class=\"btn-collapse\" data-collapse-team=\"${team.name}\">${collapsedTeams.has(team.name)?'<i class=\"fa-solid fa-chevron-down\"></i>':'<i class=\"fa-solid fa-chevron-up\"></i>'}</button>
          <span class=\"pill count-pill\">${team.members.length} ORG</span>
          <div class=\"action-menu team-menu\" data-team=\"${team.name}\">
            <button class=\"action-trigger\" aria-label=\"Menu team\"><i class=\"fa-solid fa-ellipsis-vertical\"></i></button>
            <div class=\"action-dropdown\" role=\"menu\">
              <button class=\"menu-item\" data-entity=\"team\" data-action=\"edit-team\" data-name=\"${team.name}\" role=\"menuitem\"><i class=\"fa-solid fa-pen\"></i>Edit</button>
              <button class=\"menu-item delete\" data-entity=\"team\" data-action=\"delete-team\" data-name=\"${team.name}\" role=\"menuitem\"><i class=\"fa-solid fa-trash\"></i>Delete</button>
            </div>
          </div>
        </div>`;
      teamDiv.appendChild(header);

      const body = document.createElement('div');
      body.className = 'team-body';
      if (team.members.length === 0) {
        const p = document.createElement("div");
        p.className='placeholder-empty';
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
  stats.className='team-footer-stats';
  stats.innerHTML = `<span><i class=\"fa-solid fa-user-check\"></i>${activeCount} Aktif</span><span><i class=\"fa-solid fa-user-slash\"></i>${disableCount} Non</span><span><i class=\"fa-solid fa-crown\"></i>${sup?sup.name:'-'}</span>`;
  teamDiv.appendChild(stats);
      container.appendChild(teamDiv);
    });

    // Daftar "Belum Ditugaskan" - karyawan yang belum ada tim
    const unassigned = employees.filter(emp => {
      return !teams.some(team => team.members.find(m => m.name === emp.name));
    });

    if (unassigned.length > 0) {
      const name = 'Belum Ditugaskan';
      const unassignedDiv = document.createElement('div');
      unassignedDiv.className='team';
      if(collapsedTeams.has(name)) unassignedDiv.classList.add('collapsed');
      unassignedDiv.ondragover = allowDrop;
      unassignedDiv.ondrop = (e)=>handleDrop(e,null);
      const header = document.createElement('div');
      header.className='team-header';
      header.innerHTML = `
        <div class=\"team-left\">
          <i class=\"fa-solid fa-box-open\"></i>
          <span class=\"team-title-text\">${name}</span>
        </div>
        <div class=\"team-right\">
          <button class=\"btn-collapse\" data-collapse-team=\"${name}\">${collapsedTeams.has(name)?'<i class=\"fa-solid fa-chevron-down\"></i>':'<i class=\"fa-solid fa-chevron-up\"></i>'}</button>
          <span class=\"pill count-pill\">${unassigned.length} ORG</span>
        </div>`;
      unassignedDiv.appendChild(header);
      const body = document.createElement('div');
      body.className='team-body';
      unassigned.forEach(emp=>{
        const div = document.createElement('div');
        div.className=`member ${emp.role==='supervisor'?'supervisor':''}`;
        div.draggable=true;
        div.ondragstart=(e)=>handleDragStart(e,emp,null);
        div.textContent=emp.name + (emp.role==='supervisor'?'':'');
        body.appendChild(div);
      });
      unassignedDiv.appendChild(body);
      const stats = document.createElement('div');
      const activeCount = unassigned.filter(m=>m.status==='active').length;
      const disableCount = unassigned.filter(m=>m.status==='disable').length;
      stats.className='team-footer-stats';
      stats.innerHTML = `<span><i class=\"fa-solid fa-user-check\"></i>${activeCount} Aktif</span><span><i class=\"fa-solid fa-user-slash\"></i>${disableCount} Non</span><span><i class=\"fa-solid fa-crown\"></i>-</span>`;
      unassignedDiv.appendChild(stats);
      container.appendChild(unassignedDiv);
    }

    // update chips summary
    const chipsWrap = document.getElementById('toolbarChips');
    if(chipsWrap){
      chipsWrap.innerHTML='';
      const totalShown = filtered.length;
      const chip = document.createElement('div');
      chip.className='chip-info primary';
      chip.innerHTML = `<i class=\"fa-solid fa-filter\"></i>${totalShown} terlihat`; chipsWrap.appendChild(chip);
      if(filters.role){const c=document.createElement('div');c.className='chip-info role';c.innerHTML=`<i class=\"fa-solid fa-user-tag\"></i>${filters.role}`;chipsWrap.appendChild(c);} 
      if(filters.status){const c=document.createElement('div');c.className='chip-info status';c.innerHTML=`<i class=\"fa-solid fa-toggle-on\"></i>${filters.status}`;chipsWrap.appendChild(c);} 
      if(filters.search){const c=document.createElement('div');c.className='chip-info search';c.innerHTML=`<i class=\"fa-solid fa-magnifying-glass\"></i>${filters.search}`;chipsWrap.appendChild(c);} 
    }

    // Pagination bar update
    const pag = document.getElementById('paginationBar');
    if(pag){
      if(lastFilteredCount>pageSize){
        const totalPages2 = Math.max(1, Math.ceil(lastFilteredCount / pageSize));
        // Build page numbers with window & ellipsis (show first, last, current +/-1, +/-2) limited
        const pages = [];
        const windowSize = 1; // neighbors each side
        for(let p=1; p<=totalPages2; p++){
          if(p===1 || p===totalPages2 || (p>=currentPage-windowSize && p<=currentPage+windowSize)){
            pages.push(p);
          }
        }
        // insert ellipsis markers
        const finalSeq = [];
        let prev = 0;
        pages.forEach(p=>{ if(p-prev>1){ finalSeq.push('ellipsis'); } finalSeq.push(p); prev=p; });
        const pageBtns = finalSeq.map(item=>{
          if(item==='ellipsis') return `<span class="ellipsis">...</span>`;
          const active = item===currentPage ? 'active' : '';
          return `<button class="page-btn ${active}" data-page-btn="num" data-page="${item}">${item}</button>`;
        }).join('');
        pag.innerHTML = `<div class="pager-shell">
            <button data-page-btn="prev" ${currentPage===1?'disabled':''} aria-label="Sebelumnya"><i class="fa-solid fa-angle-left"></i></button>
            ${pageBtns}
            <button data-page-btn="next" ${currentPage===totalPages2?'disabled':''} aria-label="Berikutnya"><i class="fa-solid fa-angle-right"></i></button>
          </div>
          <div class="page-info">Hal ${currentPage} / ${totalPages2}</div>`;
      } else { pag.innerHTML=''; }
    }
  }

  // Initial render
  updateUI();

</script>
</body>
</html>
