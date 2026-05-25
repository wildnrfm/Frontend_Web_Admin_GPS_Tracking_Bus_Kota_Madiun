@php
  $topbarUser = session('admin_user');
  $topbarPhotoUrl = '/images/admin/default.svg';
  if ($topbarUser) {
      if (!empty($topbarUser['photo_url'])) {
          $path = parse_url($topbarUser['photo_url'], PHP_URL_PATH);
          $topbarPhotoUrl = '/storage-proxy' . $path;
      } elseif (!empty($topbarUser['photo'])) {
          $topbarPhotoUrl = '/storage-proxy/' . ltrim($topbarUser['photo'], '/');
      }
  }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="admin-token" content="{{ session('api_token') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — Mobitra Admin</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="{{ asset('admin/css/admin.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
<style>
/* ── Professional Clean Topbar Redesign ── */
.topbar {
  background: #ffffff !important;
  border-bottom: 1.5px solid #e4ece8 !important;
  box-shadow: 0 2px 12px rgba(15, 61, 34, 0.03) !important;
  height: 64px !important;
  display: flex !important;
  align-items: center !important;
  padding: 0 24px !important;
}

/* Remove wrapper style from logo as requested */
.topbar-logo-img {
  border: none !important;
  background: none !important;
  padding: 0 !important;
  width: 32px !important;
  height: 32px !important;
  object-fit: contain !important;
  border-radius: 0 !important;
}

.topbar-brand {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  border-right: 1.5px solid #ebf0ed !important;
  padding-right: 20px !important;
  height: 32px !important;
}

.topbar-brand-name {
  color: #1B5E37 !important;
  font-weight: 800 !important;
  font-size: 17px !important;
  letter-spacing: -0.2px !important;
}

.topbar-title {
  color: #2d3732 !important;
  font-weight: 700 !important;
  font-size: 15.5px !important;
  padding-left: 8px !important;
  opacity: 0.9 !important;
}

.topbar-user {
  color: #2D2D2D !important;
  background: #f4f8f6 !important;
  border: 1px solid #dde6e0 !important;
  border-radius: 20px !important;
  padding: 4px 12px 4px 4px !important;
  gap: 8px !important;
  transition: all 0.2s ease !important;
}

.topbar-user:hover {
  background: #e8f5ed !important;
  border-color: #c3d9cc !important;
}

.topbar-user .material-icons {
  color: #6b7b73 !important;
}

.topbar-avatar {
  background: #1B5E37 !important;
  color: #ffffff !important;
  border: none !important;
  font-weight: 700 !important;
  width: 28px !important;
  height: 28px !important;
  font-size: 12px !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: hidden !important;
}


.topbar-username {
  color: #2d3732 !important;
  font-size: 12.5px !important;
  font-weight: 600 !important;
}

/* Professional White Dropdown with Forest Green Accents */
.topbar-dropdown {
  background: #ffffff !important;
  border: 1px solid #dde6e0 !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 30px rgba(15, 61, 34, 0.08) !important;
  top: 58px !important;
}

.topbar-dd-header {
  border-bottom: 1px solid #ebf0ed !important;
  padding: 16px 20px !important;
  background: #f8faf9 !important;
}

.topbar-dd-header div:first-child {
  color: #0F3D22 !important;
  font-weight: 700 !important;
  font-size: 14px !important;
}

.topbar-dd-header div:last-child {
  color: #6B7B73 !important;
}

.topbar-dropdown div[style*="uppercase"] {
  color: #1B5E37 !important;
  font-weight: 700 !important;
  font-size: 9.5px !important;
  letter-spacing: 0.8px !important;
  opacity: 0.9 !important;
}

.topbar-dd-item {
  color: #4A5568 !important;
  transition: all 0.2s ease !important;
  font-weight: 500 !important;
  font-size: 13px !important;
  padding: 12px 20px !important;
}

.topbar-dd-item:hover {
  background: #E8F5ED !important;
  color: #1B5E37 !important;
  padding-left: 24px !important;
}

.topbar-dd-item .material-icons {
  color: #1B5E37 !important;
}

.topbar-dd-item:hover .material-icons {
  color: #1B5E37 !important;
}

.topbar-dd-logout {
  color: #d32f2f !important;
}

.topbar-dd-logout .material-icons {
  color: #d32f2f !important;
}

.topbar-dd-logout:hover {
  background: #fee2e2 !important;
  color: #b71c1c !important;
}

.topbar-dd-logout:hover .material-icons {
  color: #b71c1c !important;
}

/* ── Bottom Nav Accents ── */
#bn-bg-path {
  fill: #ffffff !important;
}

.bn-fab {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%) !important;
  border-color: #ffffff !important;
  box-shadow: 0 4px 18px rgba(15, 61, 34, 0.35) !important;
}

.bn-item.active {
  color: #1B5E37 !important;
}
</style>
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
      <div class="topbar-avatar">
        <img src="{{ $topbarPhotoUrl }}" 
             alt="" 
             style="width:100%; height:100%; object-fit:cover; border-radius:50%;"
             onerror="this.src='/images/admin/default.svg'">
      </div>
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
      <a href="{{ route('admin.admins') }}"   class="topbar-dd-item"><span class="material-icons">admin_panel_settings</span>Admin</a>
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
<script>
  window.apiBaseUrl = '{{ env('API_BASE_URL', url('/api')) }}';
  window.adminToken = '{{ session('api_token') }}';
</script>
<script src="{{ asset('admin/js/admin.js') }}"></script>

{{-- ── Global Toast Notification Container ───────────────────── --}}
<div id="gps-toast-container" style="
  position:fixed;top:72px;right:16px;z-index:9999;
  display:flex;flex-direction:column;gap:8px;
  pointer-events:none;max-width:320px;
"></div>
<style>
.gps-toast {
  display:flex;align-items:flex-start;gap:10px;
  background:#1e2329;color:#fff;
  border-radius:12px;padding:12px 14px;
  box-shadow:0 4px 24px rgba(0,0,0,.35);
  font-size:13px;line-height:1.4;
  pointer-events:all;
  animation:gpsToastIn .3s cubic-bezier(.34,1.2,.64,1) both;
  border-left:4px solid #4CAF50;
  max-width:320px;
}
.gps-toast.gps-off  { border-left-color:#F44336; }
.gps-toast.gps-warn { border-left-color:#FF9800; }
.gps-toast-icon {
  font-size:18px;flex-shrink:0;margin-top:1px;
}
.gps-toast-body { flex:1; }
.gps-toast-title { font-weight:700;font-size:12px;opacity:.7;margin-bottom:2px; }
.gps-toast-msg   { font-weight:500; }
.gps-toast-time  { font-size:11px;opacity:.5;margin-top:3px; }
.gps-toast-close {
  background:none;border:none;color:rgba(255,255,255,.5);
  cursor:pointer;font-size:16px;padding:0;line-height:1;
  flex-shrink:0;
}
.gps-toast-close:hover { color:#fff; }
@keyframes gpsToastIn {
  from { opacity:0;transform:translateX(100%); }
  to   { opacity:1;transform:translateX(0); }
}
@keyframes gpsToastOut {
  from { opacity:1;transform:translateX(0); }
  to   { opacity:0;transform:translateX(100%); }
}
.gps-toast.removing {
  animation:gpsToastOut .25s ease both;
}
</style>
<script>
// ── Global GPS Toast Notification Function ────────────────────────
window.showGpsToast = function(driverName, status, busCode) {
  const container = document.getElementById('gps-toast-container');
  if (!container) return;

  const isOn = status === 'on';
  const icon = isOn ? '📡' : '📵';
  const title = isOn ? 'GPS DIAKTIFKAN' : 'GPS DIMATIKAN';
  const msg = isOn
    ? `Driver <b>${driverName || '—'}</b> telah <b style="color:#4CAF50">mengaktifkan GPS</b>${busCode ? ' (' + busCode + ')' : ''}`
    : `Driver <b>${driverName || '—'}</b> telah <b style="color:#F44336">mematikan GPS</b>${busCode ? ' (' + busCode + ')' : ''}`;
  const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

  const el = document.createElement('div');
  el.className = 'gps-toast' + (isOn ? '' : ' gps-off');
  el.innerHTML = `
    <span class="gps-toast-icon">${icon}</span>
    <div class="gps-toast-body">
      <div class="gps-toast-title">${title}</div>
      <div class="gps-toast-msg">${msg}</div>
      <div class="gps-toast-time">${now}</div>
    </div>
    <button class="gps-toast-close" onclick="this.closest('.gps-toast').remove()">✕</button>
  `;
  container.appendChild(el);

  // Auto-remove setelah 6 detik
  setTimeout(() => {
    if (!el.parentNode) return;
    el.classList.add('removing');
    el.addEventListener('animationend', () => el.remove(), { once: true });
  }, 6000);
};

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
