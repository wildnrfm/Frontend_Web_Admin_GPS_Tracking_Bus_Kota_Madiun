@extends('admin/layouts/app')
@section('title','Beranda')
@section('page-title','Beranda')
@section('content')

{{-- ═══ HEADER: Greeting (no card, plain) ══════════════════════════ --}}
<div class="db-greeting">
  <div class="db-greet-small" id="greeting-text">Selamat pagi</div>
  <div class="db-greet-name">{{ $user['name'] ?? 'Admin' }}</div>
</div>

{{-- ═══ STAT CARDS — 3 cards like mobile ════════════════════════════ --}}
<div class="db-stat-row">
  {{-- Card 1: Bus --}}
  <div class="db-stat-bus" style="background-image:url('{{ asset('admin/images/bus.jpeg') }}')">
    <div class="db-stat-overlay"></div>
    <div class="db-stat-val" id="stat-bus-active">0</div>
    <div class="db-stat-lbl">Bus Beroperasi</div>
    <div class="db-stat-sub">dari {{ $stats['total_buses'] ?? 0 }} bus</div>
  </div>

  <div class="db-stat-right">
    {{-- Card 2: Siswa --}}
    <div class="db-stat-white" style="background-image:url('{{ asset('admin/images/siswa.jpeg') }}')">  
      <div class="db-stat-overlay"></div>
      <div class="db-stat-num">{{ $stats['total_students'] ?? 0 }}</div>
      <div class="db-stat-lbl2">Siswa</div>
      <div class="db-stat-sub2">Terdaftar</div>
    </div>
    {{-- Card 3: Persetujuan --}}
    <div class="db-stat-white" style="background-image:url('{{ asset('admin/images/approve.jpeg') }}')">  
      <div class="db-stat-overlay"></div>
      <div class="db-stat-num">{{ $stats['pending_count'] ?? 0 }}</div>
      <div class="db-stat-lbl2">Persetujuan</div>
      <div class="db-stat-sub2">Menunggu</div>
    </div>
  </div>
</div>

{{-- ═══ DESKTOP 2-COLUMN GRID (wraps below sections) ════════════════ --}}
<div class="db-desktop-grid">

  {{-- ── LEFT COLUMN: Map + Bus List ── --}}
  <div class="db-col-left">
    <div class="db-section-header">
      <span>Live Tracking</span>
      <a href="{{ route('admin.tracking') }}" class="db-link-btn">
        <span class="material-icons" style="font-size:14px">open_in_full</span> Buka Peta
      </a>
    </div>

    <div class="db-map-card">
      <div style="position:relative">
        <div id="gps-map"></div>
        <div class="db-map-badge" id="bus-active-badge">
          <span class="material-icons" style="font-size:14px">directions_bus</span>
          <span id="badge-count">— bus aktif</span>
        </div>
      </div>
      <div id="bus-list" class="db-bus-list">
        <div style="padding:16px;text-align:center;color:var(--c-text-grey);font-size:13px">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>
          Memuat data GPS...
        </div>
      </div>
    </div>
  </div>

  {{-- ── RIGHT COLUMN: Kelola + Info + Approval ── --}}
  <div class="db-col-right">
    <div class="db-section-header" style="margin-top:0">
      <span>Kelola</span>
    </div>

    <div class="db-kelola-row">
      <div class="db-kelola-item" onclick="location.href='{{ route('admin.siswa') }}'">
        <div class="db-kelola-icon" style="background:#E8F5ED">
          <span class="material-icons" style="color:var(--c-primary)">school</span>
        </div>
        <div class="db-kelola-lbl">Siswa</div>
        <div class="db-kelola-cnt">{{ $stats['total_students'] ?? 0 }}</div>
      </div>
      <div class="db-kelola-item" onclick="location.href='{{ route('admin.bus') }}'">
        <div class="db-kelola-icon" style="background:#E3F0FB">
          <span class="material-icons" style="color:var(--c-blue)">directions_bus</span>
        </div>
        <div class="db-kelola-lbl">Bus</div>
        <div class="db-kelola-cnt">{{ $stats['total_buses'] ?? 0 }}</div>
      </div>
      <div class="db-kelola-item" onclick="location.href='{{ route('admin.driver') }}'">
        <div class="db-kelola-icon" style="background:#F3E5F5">
          <span class="material-icons" style="color:var(--c-purple)">badge</span>
        </div>
        <div class="db-kelola-lbl">Driver</div>
        <div class="db-kelola-cnt">{{ $stats['total_drivers'] ?? 0 }}</div>
      </div>
      <div class="db-kelola-item" onclick="location.href='{{ route('admin.halte') }}'">
        <div class="db-kelola-icon" style="background:#FFF8E1">
          <span class="material-icons" style="color:#F4A100">place</span>
        </div>
        <div class="db-kelola-lbl">Halte</div>
        <div class="db-kelola-cnt">{{ $stats['total_haltes'] ?? 0 }}</div>
      </div>
      <div class="db-kelola-item db-kelola-admin" onclick="location.href='{{ route('admin.admins') }}'">
        <div class="db-kelola-icon" style="background:#FCE4EC">
          <span class="material-icons" style="color:#E91E63">admin_panel_settings</span>
        </div>
        <div class="db-kelola-lbl">Admin</div>
        <div class="db-kelola-cnt">{{ $stats['total_admins'] ?? 0 }}</div>
      </div>
    </div>

    {{-- Info bar --}}
    <div class="info-bar" style="margin-top:16px">
      <span class="material-icons" style="font-size:16px">info</span>
      <span>Untuk mengatur rute &amp; halte bus, buka menu <strong>Bus</strong> → tap bus yang ingin diatur → "Atur Rute &amp; Halte Bus".</span>
    </div>

    {{-- Persetujuan card --}}
    <div class="db-approval-card" onclick="location.href='{{ route('admin.pending') }}'">
      <div class="db-approval-icon">
        <span class="material-icons" style="color:var(--c-orange)">pending_actions</span>
        @if(($stats['pending_count'] ?? 0) > 0)
        <span class="db-dot"></span>
        @endif
      </div>
      <div style="flex:1">
        <div class="db-approval-title">Persetujuan Akun</div>
        <div class="db-approval-sub">
          @if(($stats['pending_count'] ?? 0) > 0)
            {{ $stats['pending_count'] }} siswa menunggu persetujuan
          @else
            Tidak ada permintaan baru
          @endif
        </div>
      </div>
      <span class="material-icons" style="color:var(--c-text-grey)">chevron_right</span>
    </div>
  </div>

</div>{{-- end db-desktop-grid --}}

{{-- ═══ CSS ═══════════════════════════════════════════════════════ --}}
<style>
/* ── Greeting ───────────────────────────────────────────────────── */
.db-greeting { margin-bottom: 20px; }
.db-greet-small { font-size: 13px; color: var(--c-text-grey); font-weight: 400; }
.db-greet-name  { font-size: 28px; font-weight: 800; color: var(--c-text-dark); line-height: 1.1; }

/* ── Stat row: always 3 equal columns (100%/3 each) ─────────────── */
.db-stat-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
  margin-bottom: 20px;
}
/* Bus card */
.db-stat-bus {
  background-color: var(--c-primary);
  background-size: cover;
  background-position: center;
  border-radius: var(--radius-lg);
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
  cursor: default;
  position: relative;
  overflow: hidden;
}

/* Overlay for text readability on bg images */
.db-stat-overlay {
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
}
.db-stat-bus .db-stat-overlay   { background: rgba(15,61,34,.62); }
.db-stat-white .db-stat-overlay { background: rgba(10,30,15,.58); }

/* All stat card text above overlay */
.db-stat-bus > *:not(.db-stat-overlay),
.db-stat-white > *:not(.db-stat-overlay) { position: relative; z-index: 1; }

.db-stat-icon-bus {
  width: 38px; height: 38px;
  background: rgba(255,255,255,.2);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 4px;
}
.db-stat-icon-bus .material-icons { color: #fff; font-size: 20px; }
.db-stat-val { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
.db-stat-lbl { font-size: 11px; color: rgba(255,255,255,.9); font-weight: 600; }
.db-stat-sub { font-size: 10px; color: rgba(255,255,255,.75); }

/* db-stat-right becomes transparent so children are grid items */
.db-stat-right { display: contents; }

/* White stat cards — bg image, not clickable */
.db-stat-white {
  background-color: var(--c-surface2);
  background-size: cover;
  background-position: center;
  border-radius: var(--radius-lg);
  padding: 14px 12px;
  box-shadow: var(--shadow-card);
  cursor: default;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
  position: relative;
  overflow: hidden;
}
.db-stat-white:hover { box-shadow: var(--shadow-card); transform: none; }
.db-stat-num  { font-size: 20px; font-weight: 800; color: #fff; line-height: 1; }
.db-stat-lbl2 { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.9); }
.db-stat-sub2 { font-size: 10px; color: rgba(255,255,255,.7); }

/* ── Section header ─────────────────────────────────────────────── */
.db-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 16px;
  font-weight: 700;
  color: var(--c-text-dark);
}
.db-link-btn {
  display: inline-flex; align-items: center; gap: 4px;
  color: var(--c-primary); font-size: 13px; font-weight: 600;
  text-decoration: none;
}
.db-link-btn:hover { opacity: .8; }

/* ── Map card ───────────────────────────────────────────────────── */
.db-map-card {
  background: var(--c-white);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-card);
  overflow: hidden;
  margin-bottom: 0;
}
#gps-map { width: 100%; height: 240px; z-index: 0; }
.db-map-badge {
  position: absolute;
  top: 10px; left: 10px;
  background: var(--c-primary);
  color: #fff;
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  font-weight: 700;
  display: flex; align-items: center; gap: 5px;
  box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.db-bus-list { padding: 8px 12px 12px; }
.db-bus-item {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px;
  background: var(--c-bg);
  border-radius: var(--radius-md);
  margin-top: 8px;
}
.db-bus-icon {
  width: 36px; height: 36px;
  background: var(--c-primary-light);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

/* ── Desktop grid (mobile: single column, invisible) ────────────── */
.db-desktop-grid { display: block; }
.db-col-right { margin-top: 20px; }

/* ── Kelola row ─────────────────────────────────────────────────── */
.db-kelola-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.db-kelola-item {
  flex: 1 1 calc(50% - 10px);
  background: var(--c-white);
  border-radius: var(--radius-lg);
  padding: 14px 8px 12px;
  text-align: center;
  box-shadow: var(--shadow-card);
  cursor: pointer;
  transition: var(--transition);
  display: flex; flex-direction: column; align-items: center; gap: 4px;
}
.db-kelola-item:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
.db-kelola-item.db-kelola-admin {
  flex: 1 1 100%;
  flex-direction: row;
  justify-content: center;
  gap: 12px;
  padding: 12px;
}
.db-kelola-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 4px;
}
.db-kelola-icon .material-icons { font-size: 22px; }
.db-kelola-lbl { font-size: 12px; font-weight: 700; color: var(--c-text-dark); }
.db-kelola-cnt { font-size: 11px; color: var(--c-text-grey); }

/* ── Approval card ──────────────────────────────────────────────── */
.db-approval-card {
  margin-top: 14px;
  background: var(--c-white);
  border-radius: var(--radius-lg);
  padding: 16px;
  box-shadow: var(--shadow-card);
  display: flex; align-items: center; gap: 14px;
  cursor: pointer; transition: var(--transition);
}
.db-approval-card:hover { box-shadow: var(--shadow-lg); }
.db-approval-icon {
  width: 46px; height: 46px;
  background: #FFF3CD;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  position: relative; flex-shrink: 0;
}
.db-dot {
  position: absolute; top: -3px; right: -3px;
  width: 10px; height: 10px;
  background: var(--c-red); border-radius: 50%;
}
.db-approval-title { font-size: 14px; font-weight: 700; }
.db-approval-sub   { font-size: 12px; color: var(--c-text-grey); margin-top: 2px; }

/* ── Tablet (≥ 600px): already grid, just increase gap ──────────── */
@media (min-width: 600px) {
  .db-stat-row { gap: 14px; }
  #gps-map { height: 300px; }
}

/* ── Desktop 2-Column Layout (≥ 768px) ──────────────────────────── */
@media (min-width: 768px) {

  /* Greeting bigger */
  .db-greeting { margin-bottom: 28px; }
  .db-greet-name  { font-size: 36px; }
  .db-greet-small { font-size: 14px; }

  /* Stat row: equal 3 columns on desktop too */
  .db-stat-row {
    grid-template-columns: 1fr 1fr 1fr;
    gap: 18px;
    margin-bottom: 28px;
  }
  .db-stat-bus { padding: 22px 18px; border-radius: 18px; }
  .db-stat-icon-bus { width: 50px; height: 50px; margin-bottom: 8px; }
  .db-stat-icon-bus .material-icons { font-size: 26px; }
  .db-stat-val { font-size: 40px; }
  .db-stat-lbl { font-size: 13px; }
  .db-stat-sub { font-size: 12px; }

  .db-stat-white { padding: 20px 18px; border-radius: 18px; }
  .db-stat-icon-sm { width: 40px; height: 40px; border-radius: 12px; margin-bottom: 8px; }
  .db-stat-icon-sm .material-icons { font-size: 20px !important; }
  .db-stat-num  { font-size: 28px; }
  .db-stat-lbl2 { font-size: 13px; }
  .db-stat-sub2 { font-size: 12px; }

  /* Section headers */
  .db-section-header { font-size: 17px; margin-bottom: 14px; }
  .db-link-btn { font-size: 13px; }

  /* ── 2-Column grid ── */
  .db-desktop-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
  }

  /* Right column: card panel background */
  .db-col-right {
    margin-top: 0;
    background: var(--c-white);
    border-radius: 20px;
    box-shadow: var(--shadow-card);
    padding: 24px;
  }

  /* Right column section header — smaller, muted */
  .db-col-right .db-section-header {
    font-size: 14px;
    font-weight: 700;
    color: var(--c-text-grey);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--c-divider);
  }

  /* Right panel kelola items: 2×2 grid */
  .db-col-right .db-kelola-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  .db-col-right .db-kelola-item {
    padding: 18px 12px 14px;
    border-radius: 14px;
    box-shadow: none;
    background: var(--c-bg);
    border: 1px solid var(--c-divider);
  }
  .db-col-right .db-kelola-item:hover {
    background: var(--c-primary-light);
    border-color: var(--c-primary);
    box-shadow: none;
  }
  .db-col-right .db-kelola-row .db-kelola-admin {
    grid-column: span 2;
    flex-direction: row;
    justify-content: center;
    gap: 12px;
    padding: 12px 20px;
  }
  .db-kelola-icon { width: 46px; height: 46px; border-radius: 12px; margin-bottom: 6px; }
  .db-kelola-icon .material-icons { font-size: 22px; }
  .db-kelola-lbl { font-size: 13px; }
  .db-kelola-cnt { font-size: 12px; }

  /* Right panel info bar */
  .db-col-right .info-bar {
    margin-top: 16px;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 12px;
  }

  /* Approval card inside right panel */
  .db-col-right .db-approval-card {
    margin-top: 14px;
    border-radius: 14px;
    padding: 16px;
    gap: 14px;
    box-shadow: none;
    background: var(--c-bg);
    border: 1px solid var(--c-divider);
  }
  .db-col-right .db-approval-card:hover {
    background: var(--c-primary-light);
    border-color: var(--c-primary);
  }
  .db-approval-icon { width: 48px; height: 48px; border-radius: 12px; }
  .db-approval-title { font-size: 14px; }
  .db-approval-sub   { font-size: 12px; }

  /* Map taller */
  #gps-map { height: 360px; }
  .db-map-card { border-radius: 18px; }

  /* Bus list */
  .db-bus-item { padding: 12px 16px; }

  /* Left col section header normal */
  .db-col-left .db-section-header {
    font-size: 17px;
    color: var(--c-text-dark);
    text-transform: none;
    letter-spacing: normal;
    border-bottom: none;
    padding-bottom: 0;
  }
}

/* ── Wide Desktop (≥ 1280px) ────────────────────────────────────── */
@media (min-width: 1280px) {
  .db-desktop-grid {
    grid-template-columns: 1fr 400px;
    gap: 32px;
  }
  .db-col-right { padding: 28px; }
  #gps-map { height: 440px; }
  .db-stat-row { gap: 22px; }
}

/* ── Mobile small (< 380px) ─────────────────────────────────────── */
@media (max-width: 380px) {
  .db-kelola-lbl { font-size: 11px; }
  .db-kelola-icon { width: 38px; height: 38px; }
  .db-kelola-icon .material-icons { font-size: 19px; }
  .db-stat-val { font-size: 26px; }
  .db-stat-num { font-size: 18px; }
}
</style>

@endsection
@push('scripts')
<script>
// ── Greeting ─────────────────────────────────────────────────────
const h = new Date().getHours();
document.getElementById('greeting-text').textContent =
  h < 11 ? 'Selamat pagi' : h < 15 ? 'Selamat siang' : h < 18 ? 'Selamat sore' : 'Selamat malam';

// ── Map init ──────────────────────────────────────────────────────
let map, markers = {}, mapBoundsFitted = false;

function initMap() {
  if (map) return;
  const mapEl = document.getElementById('gps-map');
  if (!mapEl) return;
  map = L.map(mapEl, { zoomControl: true, attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
  map.setView([-7.6298, 111.5233], 13);
  setTimeout(() => map && map.invalidateSize(), 300);
}

// ── GPS Status State (untuk deteksi perubahan) ────────────────────
let prevGpsStatus = {}; // { bus_id: 'on'|'off' }

function processGpsData(buses) {
  if (!Array.isArray(buses)) return;

  const activeBuses = buses.filter(b => b.gps_status === 'on');

  // Update stats
  document.getElementById('stat-bus-active').textContent = activeBuses.length;
  document.getElementById('badge-count').textContent = activeBuses.length + ' bus aktif';

  // Deteksi perubahan GPS status → tampilkan toast
  buses.forEach(b => {
    const prev = prevGpsStatus[b.bus_id];
    const curr = b.gps_status;
    if (prev !== undefined && prev !== curr) {
      // Status berubah!
      showGpsToast(b.driver_name || b.driver?.name || '—', curr, b.bus_code);
    }
    prevGpsStatus[b.bus_id] = curr;
  });

  // Update markers
  const seen = new Set();
  let markerIdx = 0;
  activeBuses.forEach(b => {
    if (!b.position && !b.current_position) return;
    const pos = b.position ?? b.current_position;
    const latLng = [pos.latitude, pos.longitude];
    seen.add(b.bus_id);
    markerIdx++;
    if (markers[b.bus_id]) {
      markers[b.bus_id].setLatLng(latLng);
    } else {
      const icon = L.divIcon({
        html: `<div style="background:var(--c-primary);color:#fff;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;box-shadow:0 2px 8px rgba(0,0,0,.35)">${markerIdx}</div>`,
        iconSize:[30,30], iconAnchor:[15,15], className:''
      });
      markers[b.bus_id] = L.marker(latLng, {icon}).addTo(map)
        .bindPopup(`<b>${b.bus_code ?? '—'}</b><br>${b.driver_name ?? b.driver?.name ?? ''}`);
    }
  });

  // Hapus marker bus yang tidak aktif lagi
  Object.keys(markers).forEach(id => {
    if (!seen.has(+id)) { map.removeLayer(markers[id]); delete markers[id]; }
  });

  // Fit bounds hanya sekali
  if (!mapBoundsFitted && activeBuses.length > 0) {
    const withPos = activeBuses.filter(b => b.position || b.current_position);
    if (withPos.length > 0) {
      const lats = withPos.map(b => (b.position ?? b.current_position).latitude);
      const lngs = withPos.map(b => (b.position ?? b.current_position).longitude);
      map.fitBounds([[Math.min(...lats),Math.min(...lngs)],[Math.max(...lats),Math.max(...lngs)]],{padding:[20,20]});
      mapBoundsFitted = true;
    }
  }

  // Build bus list HTML
  let listHtml = '';
  // Urutkan bus: Aktif (ON) dulu, baru Offline (OFF)
  const sortedBuses = [...buses].sort((a, b) => {
    const aOn = a.gps_status === 'on' ? 1 : 0;
    const bOn = b.gps_status === 'on' ? 1 : 0;
    return bOn - aOn;
  });

  if (sortedBuses.length === 0) {
    listHtml = `<div style="padding:12px;text-align:center;font-size:13px;color:var(--c-text-grey)">Belum ada bus terdaftar</div>`;
  } else {
    sortedBuses.forEach(b => {
      const isOn = b.gps_status === 'on';
      const pos   = b.position ?? b.current_position;
      const speed = pos ? (pos.speed ?? 0).toFixed(0) : '0';
      const name  = b.bus_code ?? '—';
      const driver = b.driver_name ?? b.driver?.name ?? '—';
      
      const busPhotoHtml = b.photo_url 
        ? `<img src="${proxyImgUrl(b.photo_url)}" style="width:36px;height:36px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="${name}">`
        : `<div class="db-bus-icon" style="background:${isOn ? 'rgba(76, 175, 80, 0.1)' : 'rgba(244, 67, 54, 0.1)'}">
            <span class="material-icons" style="color:${isOn ? '#4CAF50' : '#F44336'};font-size:18px">directions_bus</span>
          </div>`;

      if (isOn) {
        listHtml += `<div class="db-bus-item" style="border-left: 3px solid #4CAF50; margin-bottom: 6px;">
          ${busPhotoHtml}
          <div style="flex:1">
            <div style="font-weight:700;font-size:13px">${name}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${driver}</div>
          </div>
          <div style="text-align:right">
            <span class="live-badge" style="background:#4CAF50;padding:2px 6px;border-radius:4px;color:#fff;font-size:10px;font-weight:600">LIVE</span>
            <div style="font-size:13px;font-weight:700;margin-top:3px">${speed} km/h</div>
          </div>
        </div>`;
      } else {
        listHtml += `<div class="db-bus-item" style="border-left: 3px solid #F44336; margin-bottom: 6px; opacity: 0.8;">
          ${busPhotoHtml}
          <div style="flex:1">
            <div style="font-weight:700;font-size:13px;color:var(--c-text-grey)">${name}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${driver}</div>
          </div>
          <div style="text-align:right">
            <span class="live-badge" style="background:#F44336;padding:2px 6px;border-radius:4px;color:#fff;font-size:10px;font-weight:600">OFFLINE</span>
          </div>
        </div>`;
      }
    });
  }

  const busListEl = document.getElementById('bus-list');
  if (busListEl) busListEl.innerHTML = `<div style="padding:8px 12px 12px">${listHtml}</div>`;
}

// ── SSE Connection dengan auto-reconnect ──────────────────────────
let sseSource = null;
let sseReconnectDelay = 1000; // mulai 1 detik, max 30 detik
let sseReconnectTimer = null;
let fallbackPollTimer = null;
let useFallback = false;

function startSSE() {
  if (!window.adminToken) { startFallback(); return; }

  const token = window.adminToken;
  const baseUrl = window.apiBaseUrl || '/api';
  const url = baseUrl + '/gps-tracks/stream?token=' + encodeURIComponent(token);

  if (sseSource) { sseSource.close(); sseSource = null; }

  try {
    sseSource = new EventSource(url);

    sseSource.addEventListener('gps_update', function(e) {
      sseReconnectDelay = 1000; // reset delay saat sukses
      useFallback = false;
      try {
        const data = JSON.parse(e.data);
        const buses = data.buses ?? data;
        initMap();
        processGpsData(buses);
      } catch(err) { /* ignore parse error */ }
    });

    sseSource.addEventListener('ping', function(e) {
      // Heartbeat diterima — koneksi masih hidup
      sseReconnectDelay = 1000;
    });

    sseSource.addEventListener('close', function(e) {
      sseSource.close();
      scheduleReconnect();
    });

    sseSource.onerror = function(e) {
      sseSource.close();
      sseSource = null;
      // Jika gagal 3 kali, fallback ke polling
      if (sseReconnectDelay >= 8000) {
        startFallback();
      } else {
        scheduleReconnect();
      }
    };

    // Stop fallback polling jika SSE berhasil connect
    if (fallbackPollTimer) { clearInterval(fallbackPollTimer); fallbackPollTimer = null; }

  } catch(e) {
    startFallback();
  }
}

function scheduleReconnect() {
  if (sseReconnectTimer) clearTimeout(sseReconnectTimer);
  sseReconnectTimer = setTimeout(() => {
    sseReconnectDelay = Math.min(sseReconnectDelay * 2, 30000);
    startSSE();
  }, sseReconnectDelay);
}

// Fallback: polling biasa jika SSE tidak didukung/gagal terus
async function startFallback() {
  if (useFallback) return; // sudah jalan
  useFallback = true;
  await pollGpsFallback();
  fallbackPollTimer = setInterval(pollGpsFallback, 4000);
}

async function pollGpsFallback() {
  try {
    const res = await api.get('/gps-tracks/dashboard', {}, true).catch(() => null);
    if (!res || !res.ok) return;
    const buses = res?.data?.data?.data ?? [];
    // Konversi format dashboard ke format SSE
    const mapped = buses.map(b => ({
      ...b,
      driver_name: b.driver?.name ?? '',
      position: b.current_position,
    }));
    initMap();
    processGpsData(mapped);
  } catch(e) { /* silent */ }
}

// ── Init ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initMap();
  startSSE();
});
</script>
@endpush

