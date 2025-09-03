<div class="modal compact mode-add" id="teamModalBox" role="dialog" aria-modal="true" aria-labelledby="teamModalTitle">
  <div class="modal-header" id="teamModalTitle"><i class="fa-solid fa-people-group"></i> Tambah Team</div>
  <div class="form-grid" style="row-gap:10px;">
    <div class="form-group full">
      <label for="teamNameInput">Nama Team</label>
      <input type="text" id="teamNameInput" placeholder="Contoh: Drive Thru" autocomplete="off" />
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeTeamModal()">Batal</button>
    <button class="btn btn-primary" id="teamSaveBtn"></button>
  </div>
</div>
