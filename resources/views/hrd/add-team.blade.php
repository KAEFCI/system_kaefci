<div class="modal compact mode-add" id="teamModalBox" role="dialog" aria-modal="true" aria-labelledby="teamModalTitle">
  <div class="modal-header" id="teamModalTitle"><i class="fa-solid fa-people-group"></i> Tambah Team</div>
  <div class="form-grid" style="row-gap:10px;">
    <div class="form-group full">
      <label for="teamNameSelect">Pilih Team</label>
      <select id="teamNameSelect" style="width:100%">
        <option value="" disabled selected>Pilih team...</option>
        <option value="Front of House (FOH) / Service Crew">Front of House (FOH) / Service Crew</option>
        <option value="Back of House (BOH) / Kitchen Crew">Back of House (BOH) / Kitchen Crew</option>
        <option value="Drive Thru / Delivery Crew">Drive Thru / Delivery Crew</option>
      </select>
    </div>
    <div class="form-group full">
      <label for="teamTaskSelect">Tugas</label>
      <select id="teamTaskSelect" style="width:100%" disabled>
        <option value="" disabled selected>Pilih tugas...</option>
      </select>
    </div>
    <div class="form-group full">
      <label for="unassignedSelect">Pilih Karyawan (belum masuk team)</label>
      <select id="unassignedSelect" style="width:100%">
        <option value="" disabled selected>Memuat data...</option>
      </select>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeTeamModal()">Batal</button>
    <button class="btn btn-primary" id="teamSaveBtn"></button>
  </div>
</div>