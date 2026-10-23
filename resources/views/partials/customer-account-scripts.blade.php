{{-- Dropdown styles + toggle script for the customer account menu (safe to include once per page). --}}
<style>
.cust-dropdown{position:relative}
.cust-menu{position:absolute;right:0;top:calc(100% + 8px);background:#fff;border-radius:10px;box-shadow:0 12px 32px rgba(0,0,0,.18);min-width:190px;padding:6px;z-index:1500}
.cust-menu[hidden]{display:none!important}
.cust-menu a,.cust-menu button{display:flex;align-items:center;width:100%;padding:9px 12px;border-radius:8px;color:#222;text-decoration:none;border:0;background:none;font-size:.9rem}
.cust-menu a:hover,.cust-menu button:hover{background:#f1f2f6}
</style>
<script>
(function(){
    const btn=document.getElementById('custMenuBtn'), menu=document.getElementById('custMenu');
    if(!btn||!menu||window.__custMenuBound) return; window.__custMenuBound=true;
    btn.addEventListener('click',e=>{ e.stopPropagation(); const open=menu.hidden; menu.hidden=!open; btn.setAttribute('aria-expanded',String(open)); });
    document.addEventListener('click',e=>{ if(!e.target.closest('.cust-dropdown')){ menu.hidden=true; btn.setAttribute('aria-expanded','false'); } });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ menu.hidden=true; } });
})();
</script>
