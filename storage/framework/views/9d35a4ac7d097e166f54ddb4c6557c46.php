
<?php
    use App\Services\CustomerAuthSettings;
    $custEnabled = CustomerAuthSettings::enabled();
    $custIdentifier = CustomerAuthSettings::identifier();
    $custGoogle = CustomerAuthSettings::googleEnabled();
?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custEnabled): ?>
<div class="cust-modal-backdrop" id="custAuthBackdrop" hidden <?php if(session('customer_login_required')): ?> data-auto="1" <?php endif; ?>>
    <div class="cust-modal" role="dialog" aria-modal="true" aria-labelledby="custAuthTitle" id="custAuthModal">
        <button type="button" class="cust-close" data-cust-close aria-label="Close">&times;</button>
        <h5 id="custAuthTitle" class="text-center fw-bold mb-3">Welcome to <?php echo e(setting('app_name') ?: 'Bandrose'); ?></h5>

        <div class="cust-tabs" role="tablist" aria-label="Login or register">
            <button role="tab" aria-selected="true" data-cust-tab="login" class="active" id="custTabLogin">Login</button>
            <button role="tab" aria-selected="false" data-cust-tab="register" id="custTabRegister">Register</button>
        </div>

        
        <form id="custLoginForm" data-cust-pane="login" novalidate>
            <?php echo csrf_field(); ?>
            <div class="mb-2">
                <label class="form-label" for="custLoginId">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custIdentifier === 'email'): ?> Email <?php elseif($custIdentifier === 'phone'): ?> Phone <?php else: ?> Email or Phone <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> *
                </label>
                <input id="custLoginId" name="identifier" class="form-control"
                    type="<?php echo e($custIdentifier === 'phone' ? 'tel' : ($custIdentifier === 'email' ? 'email' : 'text')); ?>"
                    placeholder="<?php echo e($custIdentifier === 'phone' ? '01XXXXXXXXX' : ($custIdentifier === 'email' ? 'you@mail.com' : 'Email or phone')); ?>"
                    autocomplete="username" required>
                <div class="invalid-feedback" data-err="identifier"></div>
            </div>
            <div class="mb-2">
                <label class="form-label" for="custLoginPw">Password *</label>
                <div class="input-group">
                    <input id="custLoginPw" name="password" type="password" class="form-control" autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary" data-cust-toggle-pw="custLoginPw" aria-label="Show password"><i class="far fa-eye"></i></button>
                </div>
                <div class="invalid-feedback" data-err="password"></div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-check small mb-0"><input type="checkbox" name="remember" value="1" class="form-check-input me-1">Remember me</label>
                <button type="button" class="btn btn-link btn-sm p-0" data-cust-forgot>Forgot password?</button>
            </div>
            <button class="btn btn-dark w-100" type="submit" data-cust-submit><span class="label">Login</span><span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span></button>
        </form>

        
        <form id="custRegForm" data-cust-pane="register" hidden novalidate>
            <?php echo csrf_field(); ?>
            <div class="mb-2"><label class="form-label" for="custRegName">Name *</label><input id="custRegName" name="name" class="form-control" autocomplete="name" required><div class="invalid-feedback" data-err="name"></div></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custIdentifier !== 'phone'): ?>
            <div class="mb-2"><label class="form-label" for="custRegEmail">Email *</label><input id="custRegEmail" name="email" type="email" class="form-control" autocomplete="email" required><div class="invalid-feedback" data-err="email"></div></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custIdentifier !== 'email'): ?>
            <div class="mb-2"><label class="form-label" for="custRegPhone">Phone *</label><input id="custRegPhone" name="phone" type="tel" class="form-control" placeholder="01XXXXXXXXX" autocomplete="tel" required><div class="invalid-feedback" data-err="phone"></div></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="mb-2"><label class="form-label" for="custRegUser">Username <span class="text-muted">(optional)</span></label><input id="custRegUser" name="username" class="form-control" autocomplete="username"><div class="invalid-feedback" data-err="username"></div></div>
            <div class="mb-3"><label class="form-label" for="custRegPw">Password *</label>
                <div class="input-group"><input id="custRegPw" name="password" type="password" class="form-control" autocomplete="new-password" required><button type="button" class="btn btn-outline-secondary" data-cust-toggle-pw="custRegPw" aria-label="Show password"><i class="far fa-eye"></i></button></div>
                <div class="invalid-feedback" data-err="password"></div>
            </div>
            <button class="btn btn-dark w-100" type="submit" data-cust-submit><span class="label">Create Account</span><span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span></button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custGoogle): ?>
            <div class="cust-or"><span>or</span></div>
            <a class="btn btn-outline-dark w-100" href="<?php echo e(route('customer.google.redirect', ['return_url' => request()->path() !== '/' ? '/' . request()->path() : '/'])); ?>"><i class="fab fa-google me-2"></i>Continue with Google</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>

        
        <form id="custForgotForm" data-cust-pane="forgot" hidden novalidate>
            <?php echo csrf_field(); ?>
            <button type="button" class="btn btn-link btn-sm p-0 mb-2" data-cust-back>&larr; Back to login</button>
            <h6 class="fw-bold">Reset password</h6>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custIdentifier !== 'phone'): ?>
            <div class="mb-2"><label class="form-label" for="custFgEmail">Email</label><input id="custFgEmail" type="email" class="form-control" placeholder="you@mail.com"><button class="btn btn-outline-primary w-100 mt-2" type="button" data-cust-send-email>Send reset link</button></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custIdentifier !== 'email'): ?>
            <div class="mb-2"><label class="form-label" for="custFgPhone">Phone</label><input id="custFgPhone" type="tel" class="form-control" placeholder="01XXXXXXXXX"><button class="btn btn-outline-primary w-100 mt-2" type="button" data-cust-send-otp>Send OTP</button>
                <div class="d-flex gap-2 mt-2"><input id="custFgOtp" class="form-control" placeholder="6-digit OTP" maxlength="6" inputmode="numeric"><input id="custFgNew" type="password" class="form-control" placeholder="New password"></div>
                <button class="btn btn-dark w-100 mt-2" type="button" data-cust-reset-otp>Reset Password</button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="small text-success" data-cust-forgot-msg role="status"></div>
        </form>
    </div>
</div>

<style>
.cust-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;display:flex;align-items:flex-start;justify-content:center;padding:16px;overflow-y:auto}
.cust-modal-backdrop[hidden]{display:none!important}
.cust-modal{background:#fff;border-radius:14px;max-width:420px;width:100%;padding:22px;position:relative;margin:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.cust-close{position:absolute;top:8px;right:12px;border:0;background:none;font-size:1.6rem;line-height:1;color:#666}
.cust-close:focus,.cust-modal button:focus,.cust-modal input:focus{outline:2px solid #12225B;outline-offset:2px}
.cust-tabs{display:flex;background:#f1f2f6;border-radius:10px;padding:4px;margin-bottom:16px}
.cust-tabs button{flex:1;border:0;background:none;border-radius:8px;padding:8px;font-weight:600;color:#555}
.cust-tabs button.active{background:#12225B;color:#fff}
.cust-or{display:flex;align-items:center;gap:8px;color:#888;font-size:.8rem;margin:12px 0}
.cust-or::before,.cust-or::after{content:"";flex:1;height:1px;background:#e5e5e5}
.invalid-feedback{display:none;color:#dc3545;font-size:.8rem}
.invalid-feedback.show{display:block}
.cust-toast{position:fixed;top:16px;right:16px;z-index:3000;background:#198754;color:#fff;border-radius:8px;padding:10px 16px;box-shadow:0 8px 24px rgba(0,0,0,.2)}
</style>

<script>
(function(){
"use strict";
const backdrop=document.getElementById('custAuthBackdrop');
if(!backdrop) return;
const modal=document.getElementById('custAuthModal');
let lastFocus=null;
const csrf=document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('#custLoginForm input[name="_token"]')?.value;

function safeReturn(url){ if(!url||typeof url!=='string') return location.pathname+location.search; if(!url.startsWith('/')||url.startsWith('//')) return location.pathname+location.search; if(/[:\\\r\n]/.test(url)) return location.pathname+location.search; for(const b of ['/admin','/vendor','/login','/register','/auth/']) if(url.startsWith(b)) return '/account'; return url; }
function openModal(tab){ lastFocus=document.activeElement; backdrop.hidden=false; document.body.style.overflow='hidden'; switchTab(tab||'login'); const f=modal.querySelector('[data-cust-pane="'+(tab||'login')+'"] input'); if(f) setTimeout(()=>f.focus(),50); }
function closeModal(){ backdrop.hidden=true; document.body.style.overflow=''; if(lastFocus&&lastFocus.focus) lastFocus.focus(); }
window.openCustAuth=openModal;
document.addEventListener('click',e=>{
  const opener=e.target.closest('[data-cust-open]');
  if(opener){ e.preventDefault(); openModal(opener.dataset.custOpen||'login'); return; }
  if(e.target.closest('[data-cust-close]')||e.target===backdrop) closeModal();
  const t=e.target.closest('[data-cust-tab]'); if(t) switchTab(t.dataset.custTab);
  const pw=e.target.closest('[data-cust-toggle-pw]'); if(pw){ const i=document.getElementById(pw.dataset.custTogglePw); if(i){ i.type=i.type==='password'?'text':'password'; pw.setAttribute('aria-label',i.type==='password'?'Show password':'Hide password'); } }
  if(e.target.closest('[data-cust-forgot]')) switchTab('forgot');
  if(e.target.closest('[data-cust-back]')) switchTab('login');
});
document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&!backdrop.hidden) closeModal(); });
function switchTab(name){ modal.querySelectorAll('[data-cust-tab]').forEach(b=>{ const on=b.dataset.custTab===name; b.classList.toggle('active',on); b.setAttribute('aria-selected',on); }); modal.querySelectorAll('[data-cust-pane]').forEach(p=>{ p.hidden=p.dataset.custPane!==name; }); }
function setLoading(form,on){ const b=form.querySelector('[data-cust-submit]'); if(!b) return; b.disabled=on; b.querySelector('.spinner-border')?.classList.toggle('d-none',!on); const l=b.querySelector('.label'); if(l) l.textContent=on?'Please wait…':(form.id==='custRegForm'?'Create Account':'Login'); }
function showErrors(form,errors){ form.querySelectorAll('[data-err]').forEach(el=>{ el.classList.remove('show'); el.textContent=''; }); for(const k in (errors||{})){ const el=form.querySelector(`[data-err="${k}"]`)||form.querySelector('[data-err="identifier"]'); if(el){ el.textContent=errors[k][0]; el.classList.add('show'); } } }
async function post(url,body){ const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(body)}); const j=await r.json().catch(()=>({})); if(!r.ok) throw {status:r.status,data:j}; return j; }
function toast(msg){ const t=document.createElement('div'); t.className='cust-toast'; t.textContent=msg; document.body.appendChild(t); setTimeout(()=>t.remove(),3000); }
async function handleAuth(form,url){ setLoading(form,true); try{ const fd=new FormData(form); const body=Object.fromEntries(fd.entries()); body.return_url=backdrop.dataset.auto==='1'?'':safeReturn(location.pathname+location.search); const j=await post(url,body); toast(j.message||'Login successful'); closeModal(); const dest=safeReturn(j.redirect||'/account'); setTimeout(()=>{ location.href=dest; },400); }catch(err){ const d=err.data||{}; if(d.errors) showErrors(form,d.errors); else toast(d.message||'Something went wrong'); }finally{ setLoading(form,false); } }
document.getElementById('custLoginForm')?.addEventListener('submit',e=>{ e.preventDefault(); handleAuth(e.target,'/customer/login'); });
document.getElementById('custRegForm')?.addEventListener('submit',e=>{ e.preventDefault(); handleAuth(e.target,'/customer/register'); });
// Forgot flows
document.querySelector('[data-cust-send-email]')?.addEventListener('click',async()=>{ const em=document.getElementById('custFgEmail').value; const m=document.querySelector('[data-cust-forgot-msg]'); try{ const j=await post('/customer/password/email',{email:em}); m.textContent=j.message; }catch(e){ m.textContent=e.data?.message||'Failed'; } });
document.querySelector('[data-cust-send-otp]')?.addEventListener('click',async()=>{ const ph=document.getElementById('custFgPhone').value; const m=document.querySelector('[data-cust-forgot-msg]'); try{ const j=await post('/customer/password/phone-otp',{phone:ph}); m.textContent=j.message; }catch(e){ m.textContent=e.data?.message||'Failed'; } });
document.querySelector('[data-cust-reset-otp]')?.addEventListener('click',async()=>{ const m=document.querySelector('[data-cust-forgot-msg]'); try{ const j=await post('/customer/password/phone-reset',{phone:document.getElementById('custFgPhone').value,otp:document.getElementById('custFgOtp').value,password:document.getElementById('custFgNew').value,password_confirmation:document.getElementById('custFgNew').value}); m.textContent=j.message; setTimeout(()=>switchTab('login'),1200); }catch(e){ const d=e.data||{}; m.textContent=d.errors?Object.values(d.errors)[0][0]:(d.message||'Failed'); } });
// Auto-open if redirected as guest to protected page
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('customer_login_required')): ?> openModal('login'); <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/partials/customer-auth-modal.blade.php ENDPATH**/ ?>