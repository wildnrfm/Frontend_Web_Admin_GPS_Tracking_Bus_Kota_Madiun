@extends('admin/layouts/app')
@section('title','Beranda')
@section('page-title','Beranda')
@section('content')

{{-- ═══ HEADER: Greeting Hero Card ══════════════════════════ --}}
<div class="db-hero-card">
  <div class="db-hero-content">
    <div id="greeting-text" class="db-hero-greet">Selamat pagi</div>
    <div class="db-hero-name">{{ $user['name'] ?? 'Admin' }}</div>
    <p class="db-hero-desc">Sistem Monitoring Real-Time & Pemantauan Armada Bus Sekolah Kota Madiun</p>
  </div>
  <div class="db-hero-deco">
    <span class="material-icons db-hero-icon-bg">directions_bus</span>
  </div>
</div>

{{-- ═══ STAT CARDS — 3 cards matching profil page accents ════════════ --}}
<div class="db-stat-grid">
  {{-- Card 1: Bus --}}
  <div class="db-card-stat">
    <div class="db-card-stat-icon bg-green">
      <span class="material-icons text-green">directions_bus</span>
    </div>
    <div class="db-card-stat-info">
      <div class="db-card-stat-val text-green" id="stat-bus-active">0</div>
      <div class="db-card-stat-lbl">Bus Beroperasi</div>
      <div class="db-card-stat-sub">dari {{ $stats['total_buses'] ?? 0 }} armada</div>
    </div>
  </div>

  {{-- Card 2: Siswa --}}
  <div class="db-card-stat">
    <div class="db-card-stat-icon bg-blue">
      <span class="material-icons text-blue">school</span>
    </div>
    <div class="db-card-stat-info">
      <div class="db-card-stat-val text-blue">{{ $stats['total_students'] ?? 0 }}</div>
      <div class="db-card-stat-lbl">Siswa Terdaftar</div>
      <div class="db-card-stat-sub">siswa terverifikasi</div>
    </div>
  </div>

  {{-- Card 3: Persetujuan --}}
  <div class="db-card-stat">
    <div class="db-card-stat-icon bg-orange">
      <span class="material-icons text-orange">pending_actions</span>
    </div>
    <div class="db-card-stat-info">
      <div class="db-card-stat-val text-orange">{{ $stats['pending_count'] ?? 0 }}</div>
      <div class="db-card-stat-lbl">Persetujuan Pending</div>
      <div class="db-card-stat-sub">butuh konfirmasi admin</div>
    </div>
  </div>
</div>

{{-- ═══ DESKTOP 2-COLUMN GRID (wraps below sections) ════════════════ --}}
<div class="db-desktop-grid">

  {{-- ── LEFT COLUMN: Map + Bus List ── --}}
  <div class="db-col-left">
    <div class="db-section-header">
      <span class="db-section-title">Live Tracking</span>
      <a href="{{ route('admin.tracking') }}" class="db-link-btn">
        <span class="material-icons">open_in_full</span> Buka Peta
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
      <span class="db-section-title">Kelola</span>
    </div>

    <div class="db-kelola-card">
      {{-- Siswa --}}
      <a href="{{ route('admin.siswa') }}" class="db-kelola-row">
        <div class="db-kelola-row-icon bg-green"><span class="material-icons text-green">school</span></div>
        <div class="db-kelola-row-body">
          <div class="db-kelola-row-title">Siswa</div>
          <div class="db-kelola-row-desc">Kelola data siswa & persetujuan</div>
        </div>
        <div class="db-kelola-row-badge bg-green text-green">{{ $stats['total_students'] ?? 0 }}</div>
        <span class="material-icons db-kelola-row-arrow">chevron_right</span>
      </a>

      {{-- Bus --}}
      <a href="{{ route('admin.bus') }}" class="db-kelola-row">
        <div class="db-kelola-row-icon bg-blue"><span class="material-icons text-blue">directions_bus</span></div>
        <div class="db-kelola-row-body">
          <div class="db-kelola-row-title">Bus</div>
          <div class="db-kelola-row-desc">Daftar armada, rute, & halte</div>
        </div>
        <div class="db-kelola-row-badge bg-blue text-blue">{{ $stats['total_buses'] ?? 0 }}</div>
        <span class="material-icons db-kelola-row-arrow">chevron_right</span>
      </a>

      {{-- Driver --}}
      <a href="{{ route('admin.driver') }}" class="db-kelola-row">
        <div class="db-kelola-row-icon bg-purple"><span class="material-icons text-purple">badge</span></div>
        <div class="db-kelola-row-body">
          <div class="db-kelola-row-title">Driver</div>
          <div class="db-kelola-row-desc">Kelola pengemudi bus sekolah</div>
        </div>
        <div class="db-kelola-row-badge bg-purple text-purple">{{ $stats['total_drivers'] ?? 0 }}</div>
        <span class="material-icons db-kelola-row-arrow">chevron_right</span>
      </a>

      {{-- Halte --}}
      <a href="{{ route('admin.halte') }}" class="db-kelola-row">
        <div class="db-kelola-row-icon bg-yellow"><span class="material-icons text-yellow">place</span></div>
        <div class="db-kelola-row-body">
          <div class="db-kelola-row-title">Halte</div>
          <div class="db-kelola-row-desc">Titik penjemputan & perhentian</div>
        </div>
        <div class="db-kelola-row-badge bg-yellow text-yellow">{{ $stats['total_haltes'] ?? 0 }}</div>
        <span class="material-icons db-kelola-row-arrow">chevron_right</span>
      </a>

      {{-- Admin --}}
      <a href="{{ route('admin.admins') }}" class="db-kelola-row">
        <div class="db-kelola-row-icon bg-pink"><span class="material-icons text-pink">admin_panel_settings</span></div>
        <div class="db-kelola-row-body">
          <div class="db-kelola-row-title">Admin</div>
          <div class="db-kelola-row-desc">Pengaturan hak akses sistem</div>
        </div>
        <div class="db-kelola-row-badge bg-pink text-pink">{{ $stats['total_admins'] ?? 0 }}</div>
        <span class="material-icons db-kelola-row-arrow">chevron_right</span>
      </a>
    </div>

    {{-- Info bar --}}
    <div class="info-bar-new">
      <span class="material-icons">info</span>
      <span>Untuk mengatur rute &amp; halte bus, buka menu <strong>Bus</strong> → tap bus yang ingin diatur → "Atur Rute &amp; Halte Bus".</span>
    </div>

    {{-- Persetujuan card --}}
    <div class="db-approval-card-new" onclick="location.href='{{ route('admin.pending') }}'">
      <div class="db-approval-row-icon bg-orange">
        <span class="material-icons text-orange">pending_actions</span>
        @if(($stats['pending_count'] ?? 0) > 0)
        <span class="db-dot-new"></span>
        @endif
      </div>
      <div style="flex:1">
        <div class="db-approval-title">Persetujuan Akun</div>
        <div class="db-approval-sub">
          @if(($stats['pending_count'] ?? 0) > 0)
            <strong>{{ $stats['pending_count'] }}</strong> siswa menunggu persetujuan
          @else
            Tidak ada permintaan baru
          @endif
        </div>
      </div>
      <span class="material-icons db-approval-arrow">chevron_right</span>
    </div>
  </div>

</div>{{-- end db-desktop-grid --}}

{{-- ═══ CSS ═══════════════════════════════════════════════════════ --}}
<style>
/* ── Greeting Hero Card ── */
.db-hero-card {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px 24px;
  color: #ffffff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.2);
  margin-bottom: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.db-hero-card::before {
  content: '';
  position: absolute;
  top: -80px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
  pointer-events: none;
}
.db-hero-card::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255,255,255,.04);
  pointer-events: none;
}
.db-hero-content {
  position: relative;
  z-index: 2;
  flex: 1;
}
.db-hero-greet {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 4px;
}
.db-hero-name {
  font-size: 26px;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 6px;
  letter-spacing: -0.3px;
}
.db-hero-desc {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.65);
  margin: 0;
  max-width: 480px;
  line-height: 1.45;
}
.db-hero-deco {
  position: relative;
  z-index: 1;
  opacity: 0.12;
  margin-left: 20px;
}
.db-hero-icon-bg {
  font-size: 72px !important;
  color: #ffffff;
}

/* ── Stat Cards ── */
.db-stat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}
.db-card-stat {
  background: var(--c-white, #fff);
  border-radius: 18px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--c-border, #dde6e0);
  box-shadow: 0 4px 16px rgba(15, 61, 34, 0.03);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.db-card-stat:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(15, 61, 34, 0.08);
}
.db-card-stat:nth-child(1):hover { border-color: var(--c-primary, #1B5E37); }
.db-card-stat:nth-child(2):hover { border-color: var(--c-blue, #1565C0); }
.db-card-stat:nth-child(3):hover { border-color: var(--c-orange, #E67E00); }

.db-card-stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.25s ease;
}
.db-card-stat:hover .db-card-stat-icon {
  transform: scale(1.18);
}
.db-card-stat-icon .material-icons {
  font-size: 22px;
}

/* Colors & Icons */
.bg-green { background: rgba(27, 94, 55, 0.1); }
.text-green { color: #1B5E37 !important; }

.bg-blue { background: rgba(21, 101, 192, 0.1); }
.text-blue { color: #1565C0 !important; }

.bg-orange { background: rgba(230, 126, 0, 0.1); }
.text-orange { color: #E67E00 !important; }

.db-card-stat-info {
  display: flex;
  flex-direction: column;
}
.db-card-stat-val {
  font-size: 24px;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 2px;
  letter-spacing: -0.5px;
}
.db-card-stat-lbl {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--c-text-dark, #2D2D2D);
}
.db-card-stat-sub {
  font-size: 10.5px;
  color: var(--c-text-grey, #6B7B73);
  margin-top: 1px;
}

/* ── Section header ── */
.db-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.db-section-title {
  font-size: 11px;
  font-weight: 700;
  color: var(--c-text-grey, #6B7B73);
  text-transform: uppercase;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}
.db-section-title::after {
  content: '';
  height: 1px;
  background: var(--c-divider, #EBF0ED);
  margin-right: 16px;
  flex: 1;
}
.db-link-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--c-primary, #1B5E37);
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  background: var(--c-primary-light, #E8F5ED);
  padding: 6px 14px;
  border-radius: 20px;
  transition: all 0.2s ease;
  border: 1px solid rgba(27, 94, 55, 0.15);
}
.db-link-btn:hover {
  background: var(--c-primary, #1B5E37);
  color: #fff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(15, 61, 34, 0.12);
}
.db-link-btn .material-icons {
  font-size: 14px;
}

/* ── Map card ── */
.db-map-card {
  background: var(--c-white, #fff);
  border-radius: 20px;
  box-shadow: 0 4px 16px rgba(15, 61, 34, 0.03);
  border: 1px solid var(--c-border, #dde6e0);
  overflow: hidden;
  margin-bottom: 0;
}
#gps-map { width: 100%; height: 260px; z-index: 0; }
.db-map-badge {
  position: absolute;
  top: 14px; right: 14px;
  background: var(--c-primary-dark, #0F3D22);
  color: #fff;
  border-radius: 20px;
  padding: 6px 14px;
  font-size: 11.5px;
  font-weight: 700;
  display: flex; align-items: center; gap: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,.25);
  z-index: 400;
}

/* ── Bus List Styling ── */
.db-bus-list {
  padding: 16px;
  background: var(--c-white, #fff);
  border-top: 1px solid var(--c-divider, #EBF0ED);
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.db-bus-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 16px;
  background: #fdfdfd;
  border: 1px solid var(--c-border, #DDE6E0);
  border-radius: 14px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}
.db-bus-item:hover {
  background: rgba(27, 94, 55, 0.02);
  border-color: var(--c-primary, #1B5E37);
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(15, 61, 34, 0.03);
}
.db-bus-item.bus-online {
  border-left: 4px solid var(--c-primary, #1B5E37);
}
.db-bus-item.bus-offline {
  border-left: 4px solid var(--c-red, #D32F2F);
  opacity: 0.85;
}
.db-bus-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.db-bus-icon.online {
  background: rgba(27, 94, 55, 0.1);
  color: var(--c-primary, #1B5E37);
}
.db-bus-icon.offline {
  background: rgba(211, 47, 47, 0.1);
  color: var(--c-red, #D32F2F);
}
.db-bus-photo {
  width: 40px; height: 40px;
  object-fit: cover;
  border-radius: 10px;
  flex-shrink: 0;
  border: 1px solid var(--c-border, #DDE6E0);
}
.db-bus-name {
  font-weight: 700;
  font-size: 13.5px;
  color: var(--c-text-dark, #2D2D2D);
}
.db-bus-driver {
  font-size: 12px;
  color: var(--c-text-grey, #6B7B73);
}
.db-bus-badge {
  font-size: 9.5px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: .5px;
  display: inline-block;
}
.db-bus-badge.online {
  background: var(--c-primary, #1B5E37);
}
.db-bus-badge.offline {
  background: var(--c-red, #D32F2F);
}
.db-bus-speed {
  font-size: 13px;
  font-weight: 800;
  color: var(--c-text-dark, #2D2D2D);
  margin-top: 2px;
  letter-spacing: -.2px;
}

/* ── Desktop grid (mobile: single column) ── */
.db-desktop-grid { display: block; }
.db-col-right { margin-top: 20px; }

/* ── Kelola card (New List-Style matching Profile) ── */
.db-kelola-card {
  background: var(--c-white, #fff);
  border: 1px solid var(--c-border, #dde6e0);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(15, 61, 34, 0.03);
  margin-bottom: 16px;
}
.db-kelola-row {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 20px;
  text-decoration: none;
  color: inherit;
  border-bottom: 1px solid var(--c-divider, #ebf0ed);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}
.db-kelola-row:last-child {
  border-bottom: none;
}
.db-kelola-row:hover {
  background: rgba(27, 94, 55, 0.03);
  padding-left: 24px;
}
.db-kelola-row-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.25s ease;
}
.db-kelola-row:hover .db-kelola-row-icon {
  transform: scale(1.08);
}
.db-kelola-row-icon .material-icons {
  font-size: 20px;
}
.db-kelola-row-body {
  flex: 1;
  min-width: 0;
}
.db-kelola-row-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--c-text-dark, #2D2D2D);
  margin-bottom: 1px;
}
.db-kelola-row-desc {
  font-size: 11.5px;
  color: var(--c-text-grey, #6B7B73);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.db-kelola-row-badge {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 12px;
  margin-right: 4px;
}
.db-kelola-row-arrow {
  color: var(--c-text-light, #ABB8B0);
  transition: transform .2s, color .2s;
  font-size: 20px;
}
.db-kelola-row:hover .db-kelola-row-arrow {
  transform: translateX(4px);
  color: var(--c-primary, #1B5E37);
}

/* Extra colors for icons */
.bg-purple { background: rgba(106, 27, 154, 0.1); }
.text-purple { color: #6A1B9A !important; }

.bg-yellow { background: rgba(176, 125, 0, 0.1); }
.text-yellow { color: #B07D00 !important; }

.bg-pink { background: rgba(233, 30, 99, 0.1); }
.text-pink { color: #E91E63 !important; }

/* ── Info bar ── */
.info-bar-new {
  background: var(--c-primary-light, #E8F5ED);
  border: 1px dashed rgba(27, 94, 55, 0.3);
  border-radius: 14px;
  padding: 14px 18px;
  font-size: 11.5px;
  color: var(--c-primary-dark, #0F3D22);
  display: flex;
  align-items: flex-start;
  gap: 10px;
  line-height: 1.5;
  margin-bottom: 16px;
  box-shadow: inset 0 1px 2px rgba(27, 94, 55, 0.05);
}
.info-bar-new .material-icons {
  font-size: 18px;
  color: var(--c-primary, #1B5E37);
  flex-shrink: 0;
  margin-top: 1px;
}

/* ── Approval card ── */
.db-approval-card-new {
  background: var(--c-white, #fff);
  border: 1px solid var(--c-border, #dde6e0);
  border-radius: 18px;
  padding: 16px 20px;
  box-shadow: 0 4px 16px rgba(15, 61, 34, 0.03);
  display: flex;
  align-items: center;
  gap: 14px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
.db-approval-card-new:hover {
  background: rgba(230, 126, 0, 0.02);
  border-color: var(--c-orange, #E67E00);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(230, 126, 0, 0.1);
}
.db-approval-row-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  flex-shrink: 0;
  background: rgba(230, 126, 0, 0.1);
  color: var(--c-orange, #E67E00);
  transition: transform 0.25s ease;
}
.db-approval-card-new:hover .db-approval-row-icon {
  transform: scale(1.08);
}
.db-dot-new {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 10px;
  height: 10px;
  background: var(--c-red, #D32F2F);
  border-radius: 50%;
  border: 2px solid var(--c-white, #ffffff);
}
.db-approval-title {
  font-size: 13.5px;
  font-weight: 700;
  color: var(--c-text-dark, #2D2D2D);
}
.db-approval-sub {
  font-size: 11.5px;
  color: var(--c-text-grey, #6B7B73);
}
.db-approval-arrow {
  color: var(--c-text-light, #ABB8B0);
  transition: transform 0.2s, color 0.2s;
  font-size: 20px;
}
.db-approval-card-new:hover .db-approval-arrow {
  transform: translateX(4px);
  color: var(--c-orange, #E67E00);
}

/* Leaflet popup customization */
.leaflet-popup-content-wrapper {
  background: var(--c-white, #fff) !important;
  border-radius: 12px !important;
  box-shadow: 0 4px 16px rgba(15, 61, 34, 0.12) !important;
  border: 1px solid var(--c-border, #dde6e0) !important;
  padding: 4px !important;
}
.leaflet-popup-content {
  font-family: 'Poppins', sans-serif !important;
  font-size: 12px !important;
  color: var(--c-text-dark, #2D2D2D) !important;
  line-height: 1.4 !important;
}
.leaflet-popup-tip {
  background: var(--c-white, #fff) !important;
  border: 1px solid var(--c-border, #dde6e0) !important;
}

/* ── Custom Pin Map Bus Marker ── */
.map-bus-marker {
  width: 44px;
  height: 44px;
  position: relative;
  filter: drop-shadow(0 4px 10px rgba(15, 61, 34, 0.25));
}
.map-bus-marker-pulse {
  position: absolute;
  top: -2px; left: -2px;
  width: 44px; height: 44px;
  border-radius: 50%;
  border: 2px solid var(--c-primary, #1B5E37);
  animation: markerPulse 1.8s infinite ease-out;
  pointer-events: none;
  z-index: -1;
}
@keyframes markerPulse {
  0% { transform: scale(0.95); opacity: 0.8; }
  100% { transform: scale(1.4); opacity: 0; }
}
.map-bus-marker-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 3px solid var(--c-primary, #1B5E37);
  background: var(--c-primary-light, #E8F5ED);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.map-bus-marker-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.map-bus-marker-fallback {
  font-size: 20px;
  line-height: 1;
}
.map-bus-marker-label {
  position: absolute;
  top: -6px;
  right: -6px;
  background: var(--c-primary-dark, #0F3D22);
  color: #fff;
  border-radius: 10px;
  padding: 2px 6px;
  font-size: 9px;
  font-weight: 800;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  white-space: nowrap;
  border: 1.5px solid #fff;
}
.map-bus-marker-arrow {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid var(--c-primary, #1B5E37);
  z-index: 2;
}

/* ── Desktop Layout Media Queries ── */
@media (min-width: 768px) {
  .db-hero-card {
    padding: 36px 32px;
    border-radius: 24px;
    margin-bottom: 28px;
  }
  .db-hero-name {
    font-size: 34px;
  }
  .db-hero-desc {
    font-size: 13px;
  }
  .db-hero-icon-bg {
    font-size: 88px !important;
  }

  .db-stat-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 28px;
  }
  .db-card-stat {
    padding: 20px 22px;
    border-radius: 18px;
  }
  .db-card-stat-icon {
    width: 48px;
    height: 48px;
  }
  .db-card-stat-val {
    font-size: 26px;
  }

  .db-desktop-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 28px;
    align-items: start;
  }
  .db-col-right {
    margin-top: 0;
    background: #ffffff;
    border-radius: 22px;
    box-shadow: 0 4px 20px rgba(15, 61, 34, 0.04);
    border: 1px solid #dde6e0;
    padding: 24px;
  }
  #gps-map { height: 360px; }
}

@media (min-width: 1280px) {
  .db-desktop-grid {
    grid-template-columns: 1fr 420px;
    gap: 32px;
  }
  #gps-map { height: 420px; }
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
      const markerHtml = `
        <div class="map-bus-marker">
          <div class="map-bus-marker-pulse"></div>
          <div class="map-bus-marker-avatar">
            ${b.photo_url 
              ? `<img src="${proxyImgUrl(b.photo_url)}" class="map-bus-marker-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">` 
              : ''}
            <span class="map-bus-marker-fallback" style="${b.photo_url ? 'display:none' : ''}">🚌</span>
          </div>
          <div class="map-bus-marker-label">${b.bus_code ?? markerIdx}</div>
          <div class="map-bus-marker-arrow"></div>
        </div>
      `;
      const icon = L.divIcon({
        html: markerHtml,
        iconSize: [44, 48],
        iconAnchor: [22, 48],
        className: ''
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
        ? `<img src="${proxyImgUrl(b.photo_url)}" class="db-bus-photo" alt="${name}">`
        : `<div class="db-bus-icon ${isOn ? 'online' : 'offline'}">
            <span class="material-icons" style="font-size:20px">directions_bus</span>
          </div>`;

      if (isOn) {
        listHtml += `<div class="db-bus-item bus-online">
          ${busPhotoHtml}
          <div style="flex:1; min-width:0">
            <div class="db-bus-name">${name}</div>
            <div class="db-bus-driver">${driver}</div>
          </div>
          <div style="text-align:right; flex-shrink:0">
            <span class="db-bus-badge online">LIVE</span>
            <div class="db-bus-speed">${speed} km/h</div>
          </div>
        </div>`;
      } else {
        listHtml += `<div class="db-bus-item bus-offline">
          ${busPhotoHtml}
          <div style="flex:1; min-width:0">
            <div class="db-bus-name">${name}</div>
            <div class="db-bus-driver">${driver}</div>
          </div>
          <div style="text-align:right; flex-shrink:0">
            <span class="db-bus-badge offline">OFFLINE</span>
          </div>
        </div>`;
      }
    });
  }

  const busListEl = document.getElementById('bus-list');
  if (busListEl) busListEl.innerHTML = listHtml;
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

