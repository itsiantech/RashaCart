// Main site interactions
// Safe for pages that may not include all widgets (e.g., product details without hero slider)
document.addEventListener('livewire:navigated', function() {
  // -------------------- Hero Slider (guarded) --------------------
  const slides = document.querySelector('.slides');
  const slideItems = document.querySelectorAll('.slide');
  const prevBtn = document.querySelector('.prev');
  const nextBtn = document.querySelector('.next');
  let currentIndex = 0;
  let slideInterval;
  const slideTime = 5000; // 5 seconds

  function updateSliderPosition() {
    if (!slides) return;
    slides.style.transform = `translateX(${-currentIndex * 100}%)`;
  }
  function goToSlide(index) {
    if (!slides) return;
    if (index < 0) {
      currentIndex = slideItems.length - 1;
    } else if (index >= slideItems.length) {
      currentIndex = 0;
    } else {
      currentIndex = index;
    }
    slides.style.transition = 'transform 0.5s ease-in-out';
    updateSliderPosition();
  }
  function nextSlide() { goToSlide(currentIndex + 1); }
  function prevSlide() { goToSlide(currentIndex - 1); }
  function startAutoPlay() {
    if (!slides) return;
    stopAutoPlay();
    slideInterval = setInterval(nextSlide, slideTime);
  }
  function stopAutoPlay() {
    if (slideInterval) clearInterval(slideInterval);
  }
  function handleResize() {
    if (!slides) return;
    slides.style.transition = 'none';
    updateSliderPosition();
    void slides.offsetWidth;
    slides.style.transition = 'transform 0.5s ease-in-out';
  }

  if (slides && slideItems.length) {
    updateSliderPosition();
    startAutoPlay();
    window.addEventListener('resize', handleResize);

    if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); startAutoPlay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); startAutoPlay(); });

    slides.addEventListener('transitionend', () => {
      if (currentIndex >= slideItems.length) {
        currentIndex = 0;
        slides.style.transition = 'none';
        updateSliderPosition();
        void slides.offsetWidth;
        slides.style.transition = 'transform 0.5s ease-in-out';
      }
    });

    const slider = document.querySelector('.slider');
    if (slider) {
      slider.addEventListener('mouseenter', stopAutoPlay);
      slider.addEventListener('mouseleave', startAutoPlay);
    }

    // expose controls if needed
    window.slider = { next: nextSlide, prev: prevSlide, goTo: goToSlide, play: startAutoPlay, pause: stopAutoPlay };
  }








  
  // -------------------- Cart Sidebar --------------------
  const cartSidebar = document.querySelector('.cart-sidebar');
  const cartIcon = document.querySelector('.cart-icon');
  const cartDropdown = document.querySelector('.cart-dropdown');
  const closeCart = document.querySelector('.close-cart');
  const continueShoppingBtn = document.querySelector('.btn-continue-shopping');
  const cartCountEls = document.querySelectorAll('.cart-count');



  

  // -------------------- Product Details: Thumbs, Zoom, Qty --------------------
  const mainImage = document.getElementById('mainImage');
  const galleryMain = document.querySelector('.gallery-main');
  const thumbEls = document.querySelectorAll('.gallery-thumbs .thumb');
  // Variant selectors
  const colorSwatches = document.querySelectorAll('.color-swatch');
  const sizeOptions = document.querySelectorAll('.size-option');

  // Thumbnail click -> change main image
  if (mainImage && thumbEls.length) {
    thumbEls.forEach(t => {
      t.addEventListener('click', () => {
        thumbEls.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const full = t.getAttribute('data-full') || t.getAttribute('src');
        if (full) mainImage.src = full;
      });
    });
  }

  // Hover zoom effect with transform-origin following cursor
  if (mainImage && galleryMain) {
    const img = mainImage;
    const onMove = (e) => {
      const rect = galleryMain.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      img.style.transformOrigin = `${x}% ${y}%`;
    };
    galleryMain.addEventListener('mouseenter', () => {
      galleryMain.classList.add('zoom-active');
      img.style.transition = 'transform 0.1s ease-out';
      img.style.transform = 'scale(2)';
    });
    galleryMain.addEventListener('mousemove', onMove);
    galleryMain.addEventListener('mouseleave', () => {
      img.style.transform = 'scale(1)';
      img.style.transformOrigin = 'center center';
      galleryMain.classList.remove('zoom-active');
    });
  }

  // Fullscreen image modal/lightbox
  const imgModal = document.getElementById('imgModal');
  const imgModalImg = document.getElementById('imgModalImg');
  const imgModalClose = document.querySelector('.img-close');
  function openImgModal(src) {
    if (!imgModal || !imgModalImg) return;
    imgModalImg.src = src || mainImage?.src || '';
    imgModal.classList.add('open');
    imgModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeImgModal() {
    if (!imgModal) return;
    imgModal.classList.remove('open');
    imgModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  if (galleryMain && mainImage) {
    galleryMain.addEventListener('click', () => openImgModal(mainImage.src));
  }
  if (imgModal) {
    imgModal.addEventListener('click', (e) => {
      if (e.target === imgModal) closeImgModal();
    });
  }
  if (imgModalClose) {
    imgModalClose.addEventListener('click', closeImgModal);
  }
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeImgModal(); });
  // Update modal image when clicking thumbs too
  if (thumbEls.length && imgModal) {
    thumbEls.forEach(t => t.addEventListener('click', () => {
      if (imgModal.classList.contains('open')) {
        const full = t.getAttribute('data-full') || t.getAttribute('src');
        if (full) imgModalImg.src = full;
      }
    }));
  }

  // Color select
  if (colorSwatches.length) {
    colorSwatches.forEach(btn => {
      btn.addEventListener('click', () => {
        colorSwatches.forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
      });
    });
  }
  // Size select
  if (sizeOptions.length) {
    sizeOptions.forEach(btn => {
      btn.addEventListener('click', () => {
        sizeOptions.forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
      });
    });
  }

  
  // Product Detail tabs (Description / Reviews)
  const pdTabBtns = document.querySelectorAll('.pd-tab-btn');
  const pdPanels = document.querySelectorAll('.pd-tab-panel');
  if (pdTabBtns.length && pdPanels.length) {
    pdTabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-tab');
        pdTabBtns.forEach(b => b.classList.remove('active'));
        pdPanels.forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const panel = document.getElementById(id);
        panel?.classList.add('active');
      });
    });
  }

  // -------------------- Success Toast --------------------
  function ensureToast() {
    let toast = document.querySelector('.cart-toast');
    if (!toast) {
      const div = document.createElement('div');
      div.className = 'cart-toast';
      div.innerHTML = `
        <div class="toast-inner">
          <div class="toast-left"><i class="fa-solid fa-check-circle"></i> <span class="toast-msg">Success! </span><a href="#" class="toast-view">View Cart</a></div>
          <div class="toast-right">
            <button class="btn btn-outline toast-buy"><i class="fa-solid fa-bag-shopping"></i> Buy Now</button>
            <button class="toast-close">Close</button>
          </div>
        </div>`;
      document.body.appendChild(div);

      // Wire actions
      div.querySelector('.toast-close')?.addEventListener('click', () => hideCartToast());
      div.querySelector('.toast-view')?.addEventListener('click', (e) => { e.preventDefault(); openAllCarts(); hideCartToast(); });
      div.querySelector('.toast-buy')?.addEventListener('click', (e) => { e.preventDefault(); openAllCarts(); hideCartToast(); });
    }
    return document.querySelector('.cart-toast');
  }
  function openAllCarts() {
    document.querySelectorAll('.cart-dropdown').forEach(dd => dd.classList.add('active'));
  }
  let toastTimer;
  function showCartToast() {
    const toast = ensureToast();
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => hideCartToast(), 3000);
  }
  function hideCartToast() {
    const toast = document.querySelector('.cart-toast');
    if (toast) toast.classList.remove('show');
  }

  // -------------------- Checkout rendering --------------------
  function renderCheckout() {
    const coWrap = document.querySelector('.checkout-page');
    if (!coWrap) return; // only on checkout page

    const itemsEl = document.querySelector('.co-items');
    const countEl = document.querySelector('.co-item-count');
    const subEl = document.querySelector('.co-subtotal');
    const shipEl = document.querySelector('.co-shipping');
    const discEl = document.querySelector('.co-discount');
    const totalEl = document.querySelector('.co-total');
    if (!itemsEl) return;

    // Determine shipping
    const zone = document.querySelector('input[name="delivery_zone"]:checked')?.value || 'inside';
    const shipping = zone === 'outside' ? 130 : 60;

    // Discount (optional coupon percent stored on dataset)
    const couponPercent = Number(coWrap.dataset.coupon || 0);

    // Items
    const html = cartState.items.map((it, idx) => `
      <div class="co-item" data-index="${idx}">
        <img src="${it.img}" alt="${it.title}">
        <div>
          <div class="title">${it.title}</div>
          ${it.color || it.size ? `<div class="variant">${[it.color, it.size].filter(Boolean).join(' / ')}</div>` : ''}
          <div class="qty">
            <button class="co-minus">-</button>
            <input type="number" class="co-input" value="${it.qty}" min="1">
            <button class="co-plus">+</button>
          </div>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
          <div class="amount">${money(it.price * it.qty)}</div>
          <button class="remove"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>`).join('');
    itemsEl.innerHTML = html || '<div class="cart-empty">Your cart is empty.</div>';
    if (countEl) countEl.textContent = String(cartState.items.reduce((s, i) => s + i.qty, 0));

    const subtotal = getSubtotal();
    const discount = Math.round(subtotal * (couponPercent / 100));
    const total = Math.max(0, subtotal - discount) + shipping;
    if (subEl) subEl.textContent = money(subtotal);
    if (discEl) discEl.textContent = `-` + money(discount);
    if (shipEl) shipEl.textContent = money(shipping);
    if (totalEl) totalEl.textContent = money(total);
  }

  // Delivery zone change
  document.addEventListener('change', (e) => {
    if (e.target.matches('input[name="delivery_zone"]')) {
      renderCheckout();
    }
  });


  // Hover Zoom and Fullscreen Modal for pvMainImg
  const pvImg = document.getElementById('pvMainImg');
  const pvWrap = pvImg?.closest('.pv-image');
  const pvModal = document.getElementById('pvImgModal');
  const pvModalImg = document.getElementById('pvImgModalImg');
  const pvModalClose = document.querySelector('#pvImgModal .pv-close');

  // Move-based transform-origin for smooth magnify
  function pvSetOrigin(e) {
    if (!pvWrap || !pvImg) return;
    const rect = pvWrap.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    pvImg.style.transformOrigin = `${x}% ${y}%`;
  }

  if (pvWrap && pvImg) {
    pvWrap.addEventListener('mouseenter', () => {
      pvWrap.classList.add('zoom-active');
    });
    pvWrap.addEventListener('mousemove', pvSetOrigin);
    pvWrap.addEventListener('mouseleave', () => {
      pvWrap.classList.remove('zoom-active');
      pvImg.style.transformOrigin = 'center center';
    });

    // Click to open fullscreen modal
    pvWrap.addEventListener('click', () => {
      if (!pvModal || !pvModalImg) return;
      pvModalImg.src = pvImg.src;
      pvModal.classList.add('open');
      pvModal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    });
  }

  function pvCloseModal() {
    if (!pvModal) return;
    pvModal.classList.remove('open');
    pvModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (pvModal) {
    pvModal.addEventListener('click', (e) => { if (e.target === pvModal) pvCloseModal(); });
  }
  if (pvModalClose) {
    pvModalClose.addEventListener('click', pvCloseModal);
  }
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') pvCloseModal(); });

  // When thumbnail changes main image while modal open, sync it
  if (pvModal && pvModalImg) {
    document.querySelectorAll('.pv-thumb').forEach(t => {
      t.addEventListener('click', () => {
        const src = t.getAttribute('data-src') || t.querySelector('img')?.getAttribute('src');
        if (pvModal.classList.contains('open') && src) pvModalImg.src = src;
      });
    });
  }
});

// Featured Products Countdown (appended)
// Reads optional data-end (ISO string or timestamp ms). Defaults to 6 hours from now.
document.addEventListener('DOMContentLoaded', function(){
  const timer = document.getElementById('featuredTimer');
  if (!timer) return;
  const hoursEl = timer.querySelector('.time-num[data-unit="hours"]');
  const minsEl = timer.querySelector('.time-num[data-unit="minutes"]');
  const secsEl = timer.querySelector('.time-num[data-unit="seconds"]');
  if (!hoursEl || !minsEl || !secsEl) return;

  const pad2 = (n) => String(Math.max(0, n)).padStart(2, '0');

  const ds = timer.getAttribute('data-end');
  let endTime = null;
  if (ds) {
    const asNum = Number(ds);
    if (!Number.isNaN(asNum) && asNum > Date.now()) {
      endTime = new Date(asNum);
    } else {
      const parsed = new Date(ds);
      if (!isNaN(parsed.getTime())) endTime = parsed;
    }
  }
  if (!endTime) {
    endTime = new Date(Date.now() + 6 * 60 * 60 * 1000);
  }

  function tick() {
    const now = new Date();
    let diff = endTime.getTime() - now.getTime();
    if (diff <= 0) {
      hoursEl.textContent = '00';
      minsEl.textContent = '00';
      secsEl.textContent = '00';
      clearInterval(timer._iv);
      return;
    }
    const hours = Math.floor(diff / 3600000);
    diff %= 3600000;
    const mins = Math.floor(diff / 60000);
    diff %= 60000;
    const secs = Math.floor(diff / 1000);
    hoursEl.textContent = pad2(hours);
    minsEl.textContent = pad2(mins);
    secsEl.textContent = pad2(secs);
  }

  tick();
  timer._iv = setInterval(tick, 1000);
});