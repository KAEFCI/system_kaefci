<footer class="app-footer" data-footer>
  <div class="app-footer__inner">
    &copy; {{ date('Y') }} Sistem KFC.
    <a href="#" class="app-footer__link">Kebijakan Privasi</a>
  </div>
</footer>

<style>
  /* Scoped footer styles (aman, tidak menyentuh html/body) */
  .app-footer {
    background: #ffff;
    margin-top: auto !important; /* dorong ke bawah dalam flex-column */
    width: 100%;
    position: relative;
    flex-shrink: 0;
    z-index: 0; /* pastikan selalu di bawah sidebar & dropdown */
  }
  /* Bila ada sidebar fixed di kiri (270px), geser footer agar tidak berada di bawahnya */
  .app-footer.with-sidebar {
    margin-left: 270px;
    width: calc(100% - 270px);
  }
  .app-footer__inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 18px 24px 30px;
    text-align: center;
    font: 500 12px 'Poppins', Arial, sans-serif;
    color: #888;
  }
  .app-footer__link {
    color: #c8102e;
    margin-left: 6px;
    text-decoration: none;
    font-weight: regular;
  }
  .app-footer__link:hover { text-decoration: underline; }

  /* Print: opsional sembunyikan footer (hapus rule ini jika ingin tampil) */
  @media print { .app-footer { display:none !important; } }

  /* Fallback: jika tidak berada dalam container flex-column, pakai fixed */
  .app-footer.is-fixed { position:fixed; bottom:0; left:0; right:0; z-index:0; }
</style>

<script>
// Fallback: bila parent bukan flex column (sticky footer pattern), jadikan fixed
(function(){
  const f=document.querySelector('footer.app-footer[data-footer]');
  if(!f) return;
  let el=f.parentElement, flexFound=false;
  while(el && el!==document.body){
    const st=getComputedStyle(el);
    if(st.display==='flex' && st.flexDirection==='column'){ flexFound=true; break; }
    el=el.parentElement;
  }
  if(!flexFound){ f.classList.add('is-fixed'); }
  // Deteksi sidebar fixed lebar 270px
  const sidebar=document.querySelector('.sidebar');
  if(sidebar){
    const st=getComputedStyle(sidebar);
    if(st.position==='fixed'){
      f.classList.add('with-sidebar');
    }
  }
})();
</script>
