<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="admin-token" content="{{ session('api_token') }}">
<title>@yield('title', 'Dashboard') — Mobitra Admin</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="{{ asset('admin/css/admin.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
</head>
<body>
<div class="admin-shell">

  {{-- TOPBAR --}}
  <header class="topbar">
    <a href="{{ route('admin.dashboard') }}" class="topbar-brand">
      <img src="{{ asset('admin/images/Logo1.png') }}" alt="Mobitra" class="topbar-logo-img">
      <span class="topbar-brand-name">Mobitra</span>
    </a>
    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
    @yield('topbar-actions')
    <div class="topbar-user" id="topbar-user-btn">
      <div class="topbar-avatar">{{ strtoupper(substr(session('admin_user.name','A'),0,1)) }}</div>
      <span class="topbar-username hide-mobile">{{ session('admin_user.name','Admin') }}</span>
      <span class="material-icons" style="font-size:16px;color:var(--c-text-grey)">expand_more</span>
    </div>
    <div class="topbar-dropdown" id="topbar-dropdown">
      <div class="topbar-dd-header">
        <div style="font-weight:700;font-size:14px">{{ session('admin_user.name','Admin') }}</div>
        <div style="font-size:12px;color:var(--c-text-grey)">{{ session('admin_user.email','') }}</div>
      </div>
      {{-- Kelola links --}}
      <div style="padding:8px 14px 4px;font-size:10px;font-weight:700;color:var(--c-text-grey);letter-spacing:.6px;text-transform:uppercase">Kelola</div>
      <a href="{{ route('admin.siswa') }}"    class="topbar-dd-item"><span class="material-icons">school</span>Siswa</a>
      <a href="{{ route('admin.bus') }}"      class="topbar-dd-item"><span class="material-icons">directions_bus</span>Bus</a>
      <a href="{{ route('admin.driver') }}"   class="topbar-dd-item"><span class="material-icons">badge</span>Driver</a>
      <a href="{{ route('admin.halte') }}"    class="topbar-dd-item"><span class="material-icons">place</span>Halte</a>
      <a href="{{ route('admin.pending') }}"  class="topbar-dd-item"><span class="material-icons">pending_actions</span>Persetujuan</a>
      <a href="{{ route('admin.tracking') }}" class="topbar-dd-item"><span class="material-icons">gps_fixed</span>Live Tracking</a>
      <div style="border-top:1px solid var(--c-divider);margin:6px 0"></div>
      <a href="{{ route('admin.profil') }}"   class="topbar-dd-item"><span class="material-icons">person</span>Profil</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="topbar-dd-item topbar-dd-logout">
          <span class="material-icons">logout</span>Keluar
        </button>
      </form>
    </div>
  </header>

  {{-- MAIN CONTENT --}}
  <main class="page-content">
    @yield('content')
  </main>

  {{-- CURVED NOTCH BOTTOM NAV (3 items) --}}
  <nav class="bottom-nav" id="bottom-nav">
    {{-- SVG background with notch --}}
    <svg class="bn-bg-svg" id="bn-bg-svg" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path id="bn-bg-path"/>
    </svg>
    {{-- Floating active circle --}}
    <div class="bn-fab" id="bn-fab">
      <span class="material-icons" id="bn-fab-icon">home</span>
    </div>
    {{-- Nav items --}}
    <a href="{{ route('admin.dashboard') }}"
       class="bn-item @if(!request()->routeIs('admin.analitik') && !request()->routeIs('admin.profil')) active @endif"
       data-icon="home" data-idx="0">
      <span class="material-icons bn-item-icon">home</span>
      <span class="bn-item-label">Beranda</span>
    </a>
    <a href="{{ route('admin.analitik') }}"
       class="bn-item @if(request()->routeIs('admin.analitik')) active @endif"
       data-icon="bar_chart" data-idx="1">
      <span class="material-icons bn-item-icon">bar_chart</span>
      <span class="bn-item-label">Analitik</span>
    </a>
    <a href="{{ route('admin.profil') }}"
       class="bn-item @if(request()->routeIs('admin.profil')) active @endif"
       data-icon="person" data-idx="2">
      <span class="material-icons bn-item-icon">person</span>
      <span class="bn-item-label">Profil</span>
    </a>
  </nav>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('admin/js/admin.js') }}"></script>
<script>
// ── Topbar dropdown ─────────────────────────────────────────────
const _userBtn  = document.getElementById('topbar-user-btn');
const _dropdown = document.getElementById('topbar-dropdown');
_userBtn?.addEventListener('click', e => { e.stopPropagation(); _dropdown.classList.toggle('open'); });
document.addEventListener('click', () => _dropdown?.classList.remove('open'));

// ── Curved notch bottom nav — Smooth professional edition ────────
(function() {
  const nav     = document.getElementById('bottom-nav');
  const bgSvg   = document.getElementById('bn-bg-svg');
  const bgPath  = document.getElementById('bn-bg-path');
  const fab     = document.getElementById('bn-fab');
  const fabIcon = document.getElementById('bn-fab-icon');
  const items   = nav.querySelectorAll('.bn-item');

  // ── Path builder ─────────────────────────────────────────────
  function buildPath(cx, W, H) {
    const R  = 40;       // notch radius — wide enough for half-embedded FAB
    const S  = R + 16;   // blend width
    const CR = 20;       // top corner radius
    return [
      `M${CR},0`,
      `L${cx - S},0`,
      `Q${cx - R*.9},0 ${cx - R*.68},${R*.56}`,
      `A${R},${R} 0 0,0 ${cx + R*.68},${R*.56}`,
      `Q${cx + R*.9},0 ${cx + S},0`,
      `L${W - CR},0`,
      `Q${W},0 ${W},${CR}`,
      `L${W},${H}`,
      `L0,${H}`,
      `L0,${CR}`,
      `Q0,0 ${CR},0 Z`,
    ].join(' ');
  }

  // ── SVG path lerp animation ──────────────────────────────────
  // Smoothly morphs path 'd' attribute by interpolating cx value
  let currentCx   = null;
  let targetCx    = null;
  let animFrameId = null;

  function lerpPath(from, to, t) {
    // Ease: easeOutExpo for snappy-then-settle feel
    const ease = t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
    return from + (to - from) * ease;
  }

  function animatePath(startCx, endCx) {
    if (animFrameId) cancelAnimationFrame(animFrameId);
    const duration = 400; // ms
    const start    = performance.now();
    function step(now) {
      const elapsed = Math.min((now - start) / duration, 1);
      const cx = lerpPath(startCx, endCx, elapsed);
      const W  = nav.offsetWidth;
      const H  = nav.offsetHeight;
      bgSvg.setAttribute('viewBox', `0 0 ${W} ${H}`);
      bgPath.setAttribute('d', buildPath(cx, W, H));
      currentCx = cx;
      if (elapsed < 1) {
        animFrameId = requestAnimationFrame(step);
      } else {
        animFrameId = null;
      }
    }
    animFrameId = requestAnimationFrame(step);
  }

  // ── Resolve active index & icon ──────────────────────────────
  function getActiveState() {
    let idx  = 0;
    let icon = 'home';
    items.forEach((el, i) => {
      if (el.classList.contains('active')) { idx = i; icon = el.dataset.icon || 'home'; }
    });
    return { idx, icon };
  }

  // ── Initial render (no animation — avoid load glitch) ────────
  function renderImmediate() {
    const W     = nav.offsetWidth;
    const H     = nav.offsetHeight;
    const n     = items.length;
    const itemW = W / n;
    const { idx, icon } = getActiveState();
    const cx     = itemW * idx + itemW / 2;
    currentCx    = cx;
    targetCx     = cx;
    fabIcon.textContent = icon;
    const fabSize = fab.offsetWidth || 54;
    fab.style.left = `${cx - fabSize / 2}px`;
    // FAB center sits exactly at nav top line (half above, half inside notch)
    fab.style.top  = `-${fabSize / 2}px`;
    bgSvg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    bgPath.setAttribute('d', buildPath(cx, W, H));
  }

  renderImmediate();

  // Enable smooth FAB slide + svg morph AFTER first paint
  requestAnimationFrame(() => requestAnimationFrame(() => {
    fab.style.transition = 'left .4s cubic-bezier(.34,1.2,.64,1)';
  }));

  // ── Ripple helper ────────────────────────────────────────────
  function spawnRipple(el, e) {
    const rect   = el.getBoundingClientRect();
    const size   = Math.max(rect.width, rect.height);
    const x      = (e.clientX || rect.left + rect.width / 2) - rect.left - size / 2;
    const y      = (e.clientY || rect.top  + rect.height / 2) - rect.top  - size / 2;
    const ripple = document.createElement('span');
    ripple.className = 'bn-ripple';
    ripple.style.cssText = `width:${size}px;height:${size}px;left:${x}px;top:${y}px;`;
    el.appendChild(ripple);
    ripple.addEventListener('animationend', () => ripple.remove());
  }

  // ── FAB icon cross-fade ──────────────────────────────────────
  function swapFabIcon(newIcon) {
    if (fabIcon.textContent === newIcon) return;
    fab.classList.add('bn-fab-switching');
    setTimeout(() => {
      fabIcon.textContent = newIcon;
      fab.classList.remove('bn-fab-switching');
    }, 160);
  }

  // ── FAB bounce ───────────────────────────────────────────────
  function bounceFab() {
    fab.classList.remove('bn-fab-bounce');
    void fab.offsetWidth; // reflow to restart animation
    fab.classList.add('bn-fab-bounce');
    fab.addEventListener('animationend', () => fab.classList.remove('bn-fab-bounce'), { once: true });
  }

  // ── Click handler: ripple + update active + animate ─────────
  items.forEach((item, i) => {
    item.addEventListener('click', function(e) {
      spawnRipple(this, e);

      const wasActive = this.classList.contains('active');
      if (wasActive) return; // already on this tab

      // Update active class
      items.forEach(el => el.classList.remove('active'));
      this.classList.add('active');

      const newIcon = this.dataset.icon || 'home';
      const W       = nav.offsetWidth;
      const n       = items.length;
      const itemW   = W / n;
      const newCx   = itemW * i + itemW / 2;
      const fabSize = fab.offsetWidth || 54;

      // Move FAB
      fab.style.left = `${newCx - fabSize / 2}px`;

      // Morph SVG path
      animatePath(currentCx ?? newCx, newCx);
      targetCx = newCx;

      // Animate icon & bounce
      swapFabIcon(newIcon);
      bounceFab();
    });
  });

  // ── Resize: re-render without animation ─────────────────────
  window.addEventListener('resize', () => {
    if (animFrameId) { cancelAnimationFrame(animFrameId); animFrameId = null; }
    renderImmediate();
  });
})();
</script>
@stack('scripts')
</body>
</html>
