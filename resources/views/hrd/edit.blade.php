<div class="modal compact mode-add" id="employeeModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-header" id="modalTitle"><i class="fa-solid fa-user"></i> Tambah Karyawan</div>
  <div class="form-grid" id="employeeFormGrid">
    <div class="form-group full">
      <label for="employeeName">Nama Karyawan</label>
      <input type="text" id="employeeName" placeholder="Contoh: Rudi Santoso" autocomplete="off" />
    </div>
    <div class="form-group" id="emailGroup">
      <label for="employeeEmail">Email</label>
      <input type="email" id="employeeEmail" placeholder="nama@contoh.com" autocomplete="off" />
    </div>
    <div class="form-group" id="passwordGroup">
      <label for="employeePassword">Password</label>
      <input type="password" id="employeePassword" placeholder="Password (≥6) — kosongkan saat edit jika tidak ganti" autocomplete="new-password" />
    </div>
    <div class="form-group edit-only hidden" id="roleGroup">
      <label for="employeeRole">Peran</label>
      <select id="employeeRole">
        <option value="karyawan">Karyawan</option>
        <option value="supervisor">Supervisor</option>
      </select>
    </div>
    <div class="form-group edit-only hidden" id="statusGroup">
      <label for="employeeStatus">Status</label>
      <select id="employeeStatus">
        <option value="active">Active</option>
        <option value="disable">Disable</option>
      </select>
    </div>
    <div class="form-group edit-only hidden full" id="teamSelectGroup">
      <label for="employeeTeam">Tim</label>
      <select id="employeeTeam"></select>
    </div>
    <div class="group-separator edit-only hidden" id="advSeparator" data-label="PENUGASAN & STATUS"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
    <button class="btn btn-primary" id="modalSaveBtn"></button>
  </div>
</div>
