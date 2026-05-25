@extends('admin.layouts.app')
@section('title','Analitik')
@section('page-title','Analitik')
@section('content')

<style>
  :root {
    --c-primary: #1B5E37;
    --c-primary-light: rgba(46, 125, 82, 0.14);
  }

  /* ── Stat Grid Responsif ── */
  .stat-grid {
    display: grid;
    gap: 12px;
    margin-bottom: 24px;
  }

  /* Mobile: 2 columns */
  @media (max-width: 519px) {
    .stat-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* Tablet: 2-3 columns */
  @media (min-width: 520px) and (max-width: 767px) {
    .stat-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* Desktop: 4 columns */
  @media (min-width: 768px) {
    .stat-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  /* Desktop large: 4 columns dengan spacing lebih besar */
  @media (min-width: 1200px) {
    .stat-grid {
      gap: 16px;
    }
  }

  .stat-card {
    background: var(--c-card, white);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 24px;
  }

  .stat-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--c-primary);
    margin-bottom: 4px;
  }

  .stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--c-text-dark, #1f1f1f);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-sub {
    font-size: 11px;
    color: var(--c-text-grey, #5f6368);
    margin-top: 4px;
  }

  /* ── Header Layout Responsif ── */
  .analitik-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }

  .analitik-header-left {
    flex: 1;
    min-width: 150px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .analitik-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--c-text-dark, #1f1f1f);
    font-family: 'Poppins', sans-serif;
    margin: 0;
  }

  .analitik-hero .analitik-title {
    color: #ffffff;
  }

  .analitik-date-label {
    font-size: 13px;
    color: var(--c-text-grey, #5f6368);
    font-weight: 500;
  }

  .analitik-hero .analitik-date-label {
    color: rgba(255,255,255,0.8);
  }

  .analitik-header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .analitik-hero {
    background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
    border-radius: 24px;
    padding: 26px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(15, 61, 34, 0.18);
    margin-bottom: 24px;
  }

  .analitik-hero::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -60px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    pointer-events: none;
  }

  .analitik-hero::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: -50px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.04);
    pointer-events: none;
  }

  .analitik-hero-top {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
  }

  .analitik-hero-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
  }

  .analitik-hero-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .analitik-hero-desc {
    font-size: 13px;
    color: rgba(255,255,255,0.84);
    max-width: 620px;
    line-height: 1.5;
  }

  .analitik-header-actions input[type="date"] {
    height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid var(--c-divider, #e8eaed);
    border-radius: 20px;
    background: var(--c-card, white);
  }

  .analitik-filters {
    display: flex;
    gap: 8px;
  }

  .chip {
    background: var(--c-card, white);
    border: 1px solid var(--c-divider, #e8eaed);
    color: var(--c-text-grey, #5f6368);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .chip:hover {
    background: var(--c-background, #f8f9fa);
  }

  .chip.active {
    background: var(--c-primary);
    color: white;
    border-color: var(--c-primary);
  }

  .btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--c-card, white);
    border: 1px solid var(--c-divider, #e8eaed);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0;
  }

  .btn-icon:hover {
    background: var(--c-background, #f8f9fa);
  }

  .btn-export-pdf {
    background: var(--c-primary);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
  }

  .btn-export-pdf:hover {
    background: #154f31;
  }

  /* ── Content Sections Responsif ── */
  #daily-section,
  #weekly-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  /* Mobile: stacked sections */
  @media (max-width: 767px) {
    .section-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
  }

  /* Desktop: 2-column grid untuk sections */
  @media (min-width: 768px) {
    #daily-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      grid-auto-flow: dense;
    }

    #daily-section > div:nth-child(1) {
      grid-column: 1 / -1;
    }

    #daily-section > div:nth-child(2) {
      grid-column: 1;
    }

    #daily-section > div:nth-child(3) {
      grid-column: 2;
    }

    #daily-section > div:nth-child(4) {
      grid-column: 1 / -1;
    }
  }

  /* Desktop large: buat layout lebih optimal */
  @media (min-width: 1200px) {
    #daily-section {
      grid-template-columns: repeat(3, 1fr);
    }

    #daily-section > div:nth-child(1) {
      grid-column: 1 / -1;
    }

    #daily-section > div:nth-child(2) {
      grid-column: 1 / 2;
    }

    #daily-section > div:nth-child(3) {
      grid-column: 2 / 3;
    }

    #daily-section > div:nth-child(4) {
      grid-column: 3;
    }
  }

  /* ── Tabs Styling ── */
  .tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--c-divider, #e8eaed);
    margin: 20px 0;
  }

  .tab-btn {
    background: none;
    border: none;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--c-text-grey, #5f6368);
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
  }

  .tab-btn:hover {
    color: var(--c-primary);
  }

  .tab-btn.active {
    color: var(--c-primary);
  }

  .tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--c-primary);
  }

  .tab-panel {
    display: none;
  }

  .tab-panel.active {
    display: block;
  }

  /* ── Mobile Optimization ── */
  @media (max-width: 519px) {
    .analitik-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }

    .analitik-title {
      font-size: 24px;
      margin: 0;
    }

    .analitik-header-left {
      width: 100%;
    }

    .analitik-header-actions {
      width: 100%;
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-start;
      flex-wrap: wrap;
    }

    .btn-export-pdf {
      order: 1;
      flex: 0 0 auto;
      padding: 10px 16px;
      font-size: 12px;
    }

    .analitik-header-actions input[type="date"] {
      order: 2;
      flex: 1;
      min-width: 140px;
      height: 40px;
      padding: 8px 12px;
      font-size: 12px;
    }

    .analitik-header-actions .btn-icon {
      order: 3;
      flex: 0 0 auto;
      width: 40px;
      height: 40px;
    }

    .analitik-filters {
      width: 100%;
      order: 4;
      display: flex;
      gap: 8px;
      margin-top: 8px;
    }

    .chip {
      padding: 8px 14px;
      font-size: 12px;
    }

    .tabs {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .tab-btn {
      white-space: nowrap;
      padding: 10px 12px;
      font-size: 12px;
    }
  }

  /* ── Desktop Layout ── */
  @media (min-width: 768px) {
    .analitik-header {
      align-items: center;
      gap: 20px;
    }

    .analitik-title {
      font-size: 32px;
    }

    .analitik-header-left {
      flex: 0;
    }

    .analitik-header-actions {
      flex: 1;
      justify-content: flex-end;
    }
  }

  /* ── Section Header ── */
  .section-header {
    font-size: 14px;
    font-weight: 700;
    color: var(--c-text-dark, #1f1f1f);
    margin-bottom: 12px;
  }

  /* ── Card Styling ── */
  .card {
    background: var(--c-card, white);
    border-radius: 12px;
    border: 1px solid var(--c-divider, #e8eaed);
  }

  .empty-state {
    text-align: center;
    padding: 32px 20px;
    color: var(--c-text-grey, #5f6368);
  }

  .empty-state .material-icons {
    font-size: 48px;
    display: block;
    margin-bottom: 12px;
    opacity: 0.5;
  }

  .loading-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid var(--c-divider, #e8eaed);
    border-top: 3px solid var(--c-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* ── Table Wrap ── */
  .table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table-wrap table {
    width: 100%;
    border-collapse: collapse;
  }

  .table-wrap th {
    text-align: left;
    padding: 12px 16px;
    font-weight: 600;
    font-size: 12px;
    background: var(--c-background, #f8f9fa);
    border-bottom: 2px solid var(--c-divider, #e8eaed);
  }

  .table-wrap td {
    padding: 12px 16px;
    font-size: 13px;
    border-bottom: 1px solid var(--c-divider, #e8eaed);
  }

  .table-wrap tr:hover {
    background: var(--c-background, #f8f9fa);
  }

  /* ── Horizontal Bar Chart ── */
  .bar-chart-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--c-divider, #e8eaed);
  }

  .bar-chart-item:last-child {
    border-bottom: none;
  }

  .bar-label {
    font-size: 13px;
    color: var(--c-text-grey, #5f6368);
    width: 140px;
    flex-shrink: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .bar-container {
    flex: 1;
    height: 24px;
    background: var(--c-background, #f8f9fa);
    border-radius: 4px;
    overflow: hidden;
    position: relative;
  }

  .bar-fill {
    height: 100%;
    background: #1B5E20;
    border-radius: 4px;
    transition: width 0.3s ease;
    min-width: 2px;
  }

  .bar-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--c-text-dark, #1f1f1f);
    min-width: 50px;
    text-align: right;
    flex-shrink: 0;
  }
</style>

<div class="analitik-hero">
  <div class="analitik-hero-top">
    <div class="analitik-hero-icon"><span class="material-icons">analytics</span></div>
    <div class="analitik-hero-body">
      <h1 class="analitik-title">Analitik</h1>
      <div class="analitik-hero-desc">Lihat ringkasan armada, performa pengguna, dan aktivitas sistem dalam satu tampilan yang mudah dipahami.</div>
    </div>
  </div>

  <div class="analitik-header">
    <div class="analitik-header-left">
      <div class="analitik-date-label" id="analitik-date-label">Hari ini</div>
    </div>
    <div class="analitik-header-actions">
      <button class="btn-export-pdf" id="btn-export-pdf" title="Ekspor PDF" onclick="exportPDF()">
        <span class="material-icons" style="font-size:18px">download</span>
        <span>Ekspor PDF</span>
      </button>
      <input type="date" id="date-picker" class="form-control" value="">
      <button class="btn-icon" title="Refresh" onclick="loadAll()">
        <span class="material-icons" style="font-size:18px">refresh</span>
      </button>
    </div>
  </div>
</div>

{{-- Filter Mode --}}
<div class="filter-bar">
  <div class="analitik-filters">
    <button class="chip active" id="chip-harian" onclick="setMode('harian')">Harian</button>
    <button class="chip" id="chip-mingguan" onclick="setMode('mingguan')">Mingguan</button>
  </div>
</div>

{{-- Tabs --}}
<div id="analitik-tabs">
  <div class="tabs">
    <button class="tab-btn active" data-tab="tab-ringkasan">Ringkasan</button>
    <button class="tab-btn" data-tab="tab-armada">Armada</button>
    <button class="tab-btn" data-tab="tab-pengguna">Pengguna</button>
  </div>

{{-- Tab: Ringkasan --}}
  <div class="tab-panel active" id="tab-ringkasan">
    {{-- Stat Cards Grid --}}
    <div class="stat-grid" id="ringkasan-stats">
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light,#f0f0f0)">
          <span class="material-icons" style="color:var(--c-primary)">directions_bus</span>
        </div>
        <div class="stat-value" id="s-bus">—</div>
        <div class="stat-label">TOTAL BUS</div>
        <div class="stat-sub" id="s-bus-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">school</span>
        </div>
        <div class="stat-value" id="s-siswa">—</div>
        <div class="stat-label">TOTAL SISWA</div>
        <div class="stat-sub" id="s-siswa-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">badge</span>
        </div>
        <div class="stat-value" id="s-driver">—</div>
        <div class="stat-label">TOTAL DRIVER</div>
        <div class="stat-sub" id="s-driver-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">admin_panel_settings</span>
        </div>
        <div class="stat-value" id="s-admin">—</div>
        <div class="stat-label">TOTAL ADMIN</div>
        <div class="stat-sub" id="s-admin-sub"></div>
      </div>
    </div>

    {{-- Daily Mode Content --}}
    {{-- Absensi Hari Ini --}}
    <div style="margin-top:20px">
      <div class="section-header">Absensi Hari Ini</div>
      <div id="attendance-card" class="card" style="margin-bottom:16px;padding:20px">
        <div class="loading-spinner" style="margin:0 auto"></div>
      </div>
    </div>

    {{-- Laporan Driver Hari Ini --}}
    <div style="margin-top:20px">
      <div class="section-header">Laporan Driver Hari Ini</div>
      <div id="report-card" class="card" style="padding:0;overflow-x:auto">
        <div style="padding:20px;display:flex;justify-content:center;">
          <div class="loading-spinner"></div>
        </div>
      </div>
    </div>

    {{-- Aktivitas Sistem --}}
    <div style="margin-top:20px">
      <div class="section-header">Aktivitas Sistem (24 Jam)</div>
      <div id="activity-card" class="card" style="padding:16px;display:flex;gap:12px;flex-wrap:wrap;justify-content:space-between">
        <div class="loading-spinner" style="margin:0 auto"></div>
      </div>
    </div>

    {{-- Pengguna Paling Aktif --}}
    <div style="margin-top:20px">
      <div class="section-header">Pengguna Paling Aktif (7 Hari)</div>
      <div id="active-users-list" class="card" style="padding:0">
        <div style="padding:20px;display:flex;justify-content:center;">
          <div class="loading-spinner"></div>
        </div>
      </div>
    </div>

    {{-- Weekly Mode Content --}}
    <div id="weekly-section" style="display:none">
      {{-- Week Range Display --}}
      <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin:20px 0 24px">
        <button style="background:none;border:none;cursor:pointer;padding:0" onclick="changeWeek(-1)"><span class="material-icons" style="color:var(--c-text-grey)">chevron_left</span></button>
        <div style="font-weight:600;font-size:14px;color:var(--c-text-dark);min-width:200px;text-align:center" id="week-range-display">—</div>
        <button style="background:none;border:none;cursor:pointer;padding:0" onclick="changeWeek(1)"><span class="material-icons" style="color:var(--c-text-grey)">chevron_right</span></button>
      </div>

      {{-- Weekly Summary Cards --}}
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px;margin-top:16px">
        <div class="card" style="padding:14px;text-align:center;background:#F1F8F5;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
          <div style="width:48px;height:48px;border-radius:50%;background:#A5D6A7;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;flex-shrink:0">
            <span class="material-icons" style="color:#fff;font-size:24px">groups</span>
          </div>
          <div style="font-size:22px;font-weight:900;color:var(--c-text-dark);margin-bottom:4px" id="w-penumpang">0</div>
          <div style="font-size:10px;color:#666;font-weight:500;line-height:1.3;letter-spacing:-0.3px">Total<br>Penumpang</div>
        </div>
        <div class="card" style="padding:14px;text-align:center;background:#F0F7FF;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
          <div style="width:48px;height:48px;border-radius:50%;background:#90CAF9;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;flex-shrink:0">
            <span class="material-icons" style="color:#fff;font-size:24px">assignment_turned_in</span>
          </div>
          <div style="font-size:22px;font-weight:900;color:var(--c-text-dark);margin-bottom:4px" id="w-laporan">0</div>
          <div style="font-size:10px;color:#666;font-weight:500;line-height:1.3;letter-spacing:-0.3px">Laporan<br>Masuk</div>
        </div>
        <div class="card" style="padding:14px;text-align:center;background:#FFF8F0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
          <div style="width:48px;height:48px;border-radius:50%;background:#FFB74D;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;flex-shrink:0">
            <span class="material-icons" style="color:#fff;font-size:24px">directions_bus</span>
          </div>
          <div style="font-size:22px;font-weight:900;color:var(--c-text-dark);margin-bottom:4px" id="w-bus-aktif">0</div>
          <div style="font-size:10px;color:#666;font-weight:500;line-height:1.3;letter-spacing:-0.3px">Bus<br>Aktif</div>
        </div>
      </div>

      {{-- Weekly Chart & Table --}}
      <div style="margin-top:28px">
        <div class="section-header">Penumpang per Hari</div>
        <div id="weekly-chart" class="card" style="padding:24px;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:280px;background:#fafafa">
          <div style="display:flex;align-items:flex-end;justify-content:space-around;width:100%;max-width:400px;height:180px;gap:8px">
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-sen"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Sen</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-sel"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Sel</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-rab"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Rab</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-kom"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Kom</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-jum"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Jum</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-sab"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Sab</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <div style="background:#81C784;height:120px;width:32px;border-radius:4px" id="bar-min"></div>
              <div style="font-size:11px;color:var(--c-text-grey)">Min</div>
            </div>
          </div>
        </div>
      </div>

      <div style="margin-top:28px">
        <div class="section-header">Ringkasan Harian</div>
        <div id="weekly-summary-list" style="display:flex;flex-direction:column;gap:8px">
          <div style="padding:20px;display:flex;justify-content:center;">
            <div class="loading-spinner"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tab: Armada --}}
  <div class="tab-panel" id="tab-armada">
    <div class="stat-grid" id="armada-stats" style="margin-bottom:20px">
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">check_circle</span>
        </div>
        <div class="stat-value" id="a-aktif">—</div>
        <div class="stat-label">BUS AKTIF</div>
        <div class="stat-sub" id="a-aktif-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">location_on</span>
        </div>
        <div class="stat-value" id="a-gps">—</div>
        <div class="stat-label">GPS ON</div>
        <div class="stat-sub" id="a-gps-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">build</span>
        </div>
        <div class="stat-value" id="a-maint">—</div>
        <div class="stat-label">PERAWATAN</div>
        <div class="stat-sub" id="a-maint-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">people</span>
        </div>
        <div class="stat-value" id="a-penumpang">—</div>
        <div class="stat-label">PENUMPANG HARI INI</div>
        <div class="stat-sub" id="a-penumpang-sub"></div>
      </div>
    </div>
    
    {{-- Status Semua Armada --}}
    <div class="section-header">Status Semua Armada</div>
    <div id="armada-list" class="card" style="padding:0">
      <div style="padding:20px;display:flex;justify-content:center;">
        <div class="loading-spinner"></div>
      </div>
    </div>
  </div>

  {{-- Tab: Pengguna --}}
  <div class="tab-panel" id="tab-pengguna">
    <div class="stat-grid" style="margin-bottom:20px">
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">school</span>
        </div>
        <div class="stat-value" id="p-siswa">—</div>
        <div class="stat-label">TOTAL SISWA</div>
        <div class="stat-sub" id="p-siswa-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">badge</span>
        </div>
        <div class="stat-value" id="p-driver">—</div>
        <div class="stat-label">TOTAL DRIVER</div>
        <div class="stat-sub" id="p-driver-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:var(--c-primary-light)">
          <span class="material-icons" style="color:var(--c-primary)">admin_panel_settings</span>
        </div>
        <div class="stat-value" id="p-admin">—</div>
        <div class="stat-label">TOTAL ADMIN</div>
        <div class="stat-sub" id="p-admin-sub"></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#FFEBEE">
          <span class="material-icons" style="color:var(--c-red)">block</span>
        </div>
        <div class="stat-value" id="p-suspend">—</div>
        <div class="stat-label">AKUN SUSPEND</div>
        <div class="stat-sub" id="p-suspend-sub"></div>
      </div>
    </div>

    {{-- Aktivitas Login (24 Jam) --}}
    <div class="section-header">Aktivitas Login (24 Jam)</div>
    <div id="login-activity" class="card" style="padding:0;display:flex;gap:20px;flex-wrap:wrap;padding:20px;justify-content:space-around">
      <div style="text-align:center">
        <div style="font-size:28px;font-weight:800;color:var(--c-primary)" id="act-login">0</div>
        <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Login Berhasil</div>
      </div>
      <div style="text-align:center">
        <div style="font-size:28px;font-weight:800;color:var(--c-red)" id="act-failed">0</div>
        <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Login Gagal</div>
      </div>
    </div>

    {{-- Jenis Aktivitas (30 Hari) --}}
    <div style="margin-top:20px">
      <div class="section-header">Jenis Aktivitas (30 Hari)</div>
      <div id="activity-types" class="card" style="padding:20px">
        <div class="loading-spinner" style="margin:0 auto"></div>
      </div>
    </div>

    {{-- Daftar Driver --}}
    <div style="margin-top:20px">
      <div class="section-header">Daftar Driver</div>
      <div id="driver-list" class="card" style="padding:0">
        <div style="padding:20px;display:flex;justify-content:center;">
          <div class="loading-spinner"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
initTabs('analitik-tabs');
let mode = 'harian';
const dp = document.getElementById('date-picker');
dp.value = new Date().toISOString().split('T')[0];
dp.addEventListener('change', loadAll);

function setMode(m) {
  mode = m;
  document.querySelectorAll('#analitik-tabs ~ * .chip, .chip').forEach(c => c.classList.remove('active'));
  document.getElementById('chip-' + m)?.classList.add('active');
  document.getElementById('weekly-section').style.display = m === 'mingguan' ? '' : 'none';
  
  // Sembunyikan tombol Ekspor PDF ketika filter Mingguan aktif
  const exportBtn = document.getElementById('btn-export-pdf');
  if (exportBtn) {
    exportBtn.style.display = m === 'harian' ? 'flex' : 'none';
  }
  
  loadAll();
}

function exportPDF() {
  const date = dp.value;
  const filename = `Analitik-${date}.pdf`;
  // Buka halaman PDF export
  window.open(`{{ route('admin.analitik.export') }}?date=${date}`, '_blank');
}

async function loadAll() {
  const date = dp.value;
  const todayStr = new Date().toISOString().split('T')[0];
  document.getElementById('analitik-date-label').textContent = date === todayStr ? 'Hari ini' : date;

  // Update card headers dynamically based on mode
  const attHeader = document.getElementById('attendance-card')?.previousElementSibling;
  const repHeader = document.getElementById('report-card')?.previousElementSibling;
  const actHeader = document.getElementById('activity-card')?.previousElementSibling;
  const usrHeader = document.getElementById('active-users-list')?.previousElementSibling;
  const loginActHeader = document.getElementById('login-activity')?.previousElementSibling;
  if (attHeader) attHeader.textContent = mode === 'harian' ? 'Absensi Hari Ini' : 'Absensi Pekan Ini';
  if (repHeader) repHeader.textContent = mode === 'harian' ? 'Laporan Driver Hari Ini' : 'Laporan Driver Pekan Ini';
  if (actHeader) actHeader.textContent = mode === 'harian' ? 'Aktivitas Sistem (24 Jam)' : 'Aktivitas Sistem (7 Hari)';
  if (usrHeader) usrHeader.textContent = mode === 'harian' ? 'Pengguna Paling Aktif (Hari Ini)' : 'Pengguna Paling Aktif (7 Hari terakhir)';
  if (loginActHeader) loginActHeader.textContent = mode === 'harian' ? 'Aktivitas Login (24 Jam)' : 'Aktivitas Login (7 Hari)';

  // Display loading spinners
  if (document.getElementById('attendance-card')) {
    document.getElementById('attendance-card').innerHTML = '<div style="padding:20px;display:flex;justify-content:center;"><div class="loading-spinner"></div></div>';
  }
  if (document.getElementById('report-card')) {
    document.getElementById('report-card').innerHTML = '<div style="padding:20px;display:flex;justify-content:center;"><div class="loading-spinner"></div></div>';
  }
  if (document.getElementById('activity-card')) {
    document.getElementById('activity-card').innerHTML = '<div style="padding:20px;display:flex;justify-content:center;"><div class="loading-spinner"></div></div>';
  }
  if (document.getElementById('active-users-list')) {
    document.getElementById('active-users-list').innerHTML = '<div style="padding:20px;display:flex;justify-content:center;"><div class="loading-spinner"></div></div>';
  }

  if (mode === 'harian') {
    await Promise.all([
      loadStats(date), 
      loadAttendance(date), 
      loadReport(date), 
      loadActivity(date), 
      loadBusStats(), 
      loadArmadaList(), 
      loadActivityChart(), 
      loadDriverList()
    ]);
  } else {
    await Promise.all([
      loadStats(date),
      loadWeekly(),
      loadBusStats(),
      loadArmadaList(),
      loadActivityChart(),
      loadDriverList()
    ]);
  }
}

async function loadStats(date) {
  const todayStr = new Date().toISOString().split('T')[0];
  const queryDate = date || todayStr;

  // Gunakan endpoint yang tepat per role — tidak filter /users
  const [busRes, stuApprovedRes, stuPendingRes, drvRes, admRes, gpsRes, attRes] = await Promise.all([
    api.get('/buses'),
    api.get('/students', { per_page: 1, approval_status: 'approved' }),
    api.get('/students', { per_page: 1, approval_status: 'pending' }),
    api.get('/drivers'),
    api.get('/admins'),
    api.get('/gps-tracks/dashboard').catch(() => ({ data: null })),
    api.get('/attendance', { date: queryDate, per_page: 500 }),
  ]);
  const buses   = busRes.data?.data ?? [];
  const siswaAktif = stuApprovedRes.data?.pagination?.total ?? (stuApprovedRes.data?.data?.length ?? 0);
  const siswaPending = stuPendingRes.data?.pagination?.total ?? (stuPendingRes.data?.data?.length ?? 0);
  const siswaTotal = siswaAktif + siswaPending;
  const drivers = drvRes.data?.data ?? [];
  const admins  = admRes.data?.data ?? [];
  const adminTotal = admRes.data?.pagination?.total ?? admins.length;
  
  // Handle GPS data - ensure it's always an array
  let gpsOn = 0;
  if (gpsRes?.data) {
    let gpsBuses = gpsRes.data?.data?.data ?? (Array.isArray(gpsRes.data?.data) ? gpsRes.data.data : []);
    gpsOn = Array.isArray(gpsBuses) ? gpsBuses.filter(b => b.gps_status === 'on' || b.status === 'on').length : 0;
  }

  const rawAttendance = attRes.data?.data ?? [];
  const busIdsOperating = new Set(rawAttendance.filter(a => a.bus_id).map(a => a.bus_id));
  const busAktifCount = busIdsOperating.size;

  // Ringkasan stats
  if (mode === 'harian') {
    document.getElementById('s-bus').textContent = busAktifCount;
    document.getElementById('s-bus-sub').textContent = `${gpsOn} GPS aktif · ${buses.length} total`;
  }
  document.getElementById('s-siswa').textContent = siswaTotal;
  document.getElementById('s-siswa-sub').textContent = `${siswaAktif} aktif · ${siswaPending} pending`;
  
  const activeDrivers = drivers.filter(d => !d.user?.is_suspended).length;
  document.getElementById('s-driver').textContent = activeDrivers;
  document.getElementById('s-driver-sub').textContent = `${drivers.length} total driver`;
  document.getElementById('s-admin').textContent = adminTotal;
  document.getElementById('s-admin-sub').textContent = 'Pengelola sistem';
  
  // Pengguna stats
  const adminCard = document.getElementById('p-admin');
  const adminSub = document.getElementById('p-admin-sub');
  const driverSub = document.getElementById('p-driver-sub');
  const siswaSub = document.getElementById('p-siswa-sub');
  
  document.getElementById('p-driver').textContent = activeDrivers;
  if (driverSub) driverSub.textContent = `${drivers.length} total driver`;
  document.getElementById('p-siswa').textContent = siswaTotal;
  if (siswaSub) siswaSub.textContent = `${siswaAktif} aktif · ${siswaPending} pending`;
  if (adminCard) adminCard.textContent = adminTotal;
  if (adminSub) adminSub.textContent = 'Pengelola sistem';
  
  // Armada stats
  if (mode === 'harian') {
    let gpsOnCount = 0;
    if (queryDate === todayStr) {
      gpsOnCount = gpsOn;
    } else {
      gpsOnCount = busAktifCount; // Assume GPS active if bus operated on a past date
    }

    const total = buses.length;
    const maint = buses.filter(b => b.status === 'maintenance').length;
    const checkoutCount = rawAttendance.filter(a => a.waktu_turun).length;

    document.getElementById('a-aktif').textContent = busAktifCount;
    document.getElementById('a-aktif-sub').textContent = `${total} total armada`;
    document.getElementById('a-gps').textContent = gpsOnCount;
    document.getElementById('a-gps-sub').textContent = `${total - gpsOnCount} GPS mati`;
    document.getElementById('a-maint').textContent = maint;
    document.getElementById('a-maint-sub').textContent = 'Bus dalam servis';
    document.getElementById('a-penumpang').textContent = checkoutCount;
    document.getElementById('a-penumpang-label').textContent = 'PENUMPANG HARI INI';
    document.getElementById('a-penumpang-sub').textContent = `${rawAttendance.length} total naik`;
  }
}

async function loadAttendance(date) {
  const res = await api.get('/attendance', { date, per_page: 500 });
  const raw = res.data?.data ?? [];
  const checkout = raw.filter(r => r.waktu_turun).length;
  const checkin = raw.length - checkout;
  const checkoutPct = raw.length > 0 ? Math.round((checkout / raw.length) * 100) : 0;
  
  // Update penumpang stat
  const penumpangCard = document.getElementById('a-penumpang');
  const penumpangSub = document.getElementById('a-penumpang-sub');
  if (mode === 'harian') {
    if (penumpangCard) penumpangCard.textContent = checkout;
    if (penumpangSub) penumpangSub.textContent = raw.length + ' naik bus';
  }
  
  // Update attendance card display - mobile style
  const attendanceCard = document.getElementById('attendance-card');
  if (attendanceCard) {
    attendanceCard.innerHTML = `
      <div style="display:flex;gap:12px;margin-bottom:14px">
        <div style="flex:1;text-align:center;padding:14px;background:var(--c-primary-light);border-radius:12px">
          <span class="material-icons" style="font-size:32px;color:var(--c-primary);display:block;margin-bottom:6px">directions_bus</span>
          <div style="font-size:20px;font-weight:800;color:var(--c-primary)">${raw.length}</div>
          <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Naik Bus</div>
        </div>
        <div style="flex:1;text-align:center;padding:14px;background:#E0F7FA;border-radius:12px">
          <span class="material-icons" style="font-size:32px;color:#00BCD4;display:block;margin-bottom:6px">check_circle</span>
          <div style="font-size:20px;font-weight:800;color:#00BCD4">${checkout}</div>
          <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Sudah Turun</div>
        </div>
      </div>
      <div style="padding:12px;background:var(--c-background);border-radius:10px;text-align:center">
        <div style="font-size:12px;color:var(--c-text-grey);margin-bottom:8px">Rate Checkout</div>
        <div style="font-size:18px;font-weight:700;color:var(--c-primary);margin-bottom:8px">${checkoutPct}%</div>
        <div style="width:100%;height:6px;background:#E0E0E0;border-radius:3px;overflow:hidden">
          <div style="height:100%;background:var(--c-primary);width:${checkoutPct}%;border-radius:3px;transition:width 0.3s ease"></div>
        </div>
      </div>`;
  }
}

async function loadReport(date) {
  const res = await api.get('/reports/admin', { tanggal: date });
  const d = res.data?.data ?? res.data ?? {};
  const rows = d.reports ?? [];
  if (!rows.length) {
    const emptyHtml = `<div class="empty-state" style="padding:20px;text-align:center"><span class="material-icons" style="font-size:48px;color:var(--c-text-grey);display:block;margin-bottom:12px">description</span><p>Belum ada laporan driver</p></div>`;
    const reportCard = document.getElementById('report-card');
    if (reportCard) reportCard.innerHTML = emptyHtml;
    return;
  }
  const html = `<div class="table-wrap"><table style="width:100%">
    <thead>
      <tr style="background:var(--c-background);border-bottom:2px solid var(--c-divider)">
        <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Bus</th>
        <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Plat</th>
        <th style="text-align:center;padding:12px 16px;font-weight:600;font-size:12px">Penumpang</th>
        <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Catatan</th>
      </tr>
    </thead>
    <tbody>
      ${rows.map((r, i) => `<tr style="border-bottom:1px solid var(--c-divider);${i % 2 === 0 ? 'background:var(--c-background)' : ''}">
        <td style="padding:12px 16px;font-size:13px">${r.bus?.kode_bus ?? '-'}</td>
        <td style="padding:12px 16px;font-size:13px;font-weight:600">${r.bus?.plat_nomor ?? '-'}</td>
        <td style="text-align:center;padding:12px 16px"><b style="color:var(--c-primary);font-size:14px">${r.total_penumpang ?? 0}</b></td>
        <td style="padding:12px 16px;font-size:12px;color:var(--c-text-grey);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.catatan_driver ?? '-'}</td>
      </tr>`).join('')}
    </tbody>
  </table></div>`;
  const reportCard = document.getElementById('report-card');
  if (reportCard) reportCard.innerHTML = html;
}

async function loadActivity(date) {
  const res = await api.get('/activity/dashboard', { date });
  const d = res.data?.data ?? res.data ?? {};
  const summary = d.summary ?? {};
  const loginBerhasil = summary.recent_logins_24h ?? 0;
  const loginGagal = summary.failed_logins_24h ?? 0;
  const akunSuspend = (summary.suspended_accounts ?? 0);
  
  // Update pengguna tab stats
  document.getElementById('act-login').textContent = loginBerhasil;
  document.getElementById('act-failed').textContent = loginGagal;
  
  const suspendCard = document.getElementById('p-suspend');
  const suspendSub = document.getElementById('p-suspend-sub');
  if (suspendCard) suspendCard.textContent = akunSuspend;
  if (suspendSub) suspendSub.textContent = 'Akun nonaktif';
  
  // Update activity card in ringkasan tab - mobile style
  const activityCard = document.getElementById('activity-card');
  if (activityCard) {
    activityCard.dataset.suspended = akunSuspend;
    activityCard.innerHTML = `
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:var(--c-primary-light);border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:var(--c-primary);display:block;margin-bottom:8px">done</span>
        <div style="font-size:22px;font-weight:800;color:var(--c-primary)">${loginBerhasil}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Login Berhasil</div>
      </div>
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:#FFE5E5;border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:#D32F2F;display:block;margin-bottom:8px">close</span>
        <div style="font-size:22px;font-weight:800;color:#D32F2F">${loginGagal}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Login Gagal</div>
      </div>
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:#FFF3CD;border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:#F57C00;display:block;margin-bottom:8px">warning</span>
        <div style="font-size:22px;font-weight:800;color:#F57C00">${akunSuspend}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Akun Suspend</div>
      </div>`;
  }

  // Load top active users for ringkasan tab
  const topUsers = d.top_active_users ?? [];
  const usersList = document.getElementById('active-users-list');
  if (usersList) {
    if (!topUsers || topUsers.length === 0) {
      usersList.innerHTML = `<div style="text-align:center;color:var(--c-text-grey);padding:20px">Tidak ada data pengguna aktif</div>`;
      return;
    }
    const colors = ['#1B5E37', '#2E7D52', '#4CAF50', '#66BB6A', '#81C784', '#A5D6A7', '#C8E6C9', '#9CCC65', '#43A047', '#2E7D32'];
    const html = topUsers.slice(0, 10).map((u, i) => {
      const initial = (u.user?.name?.charAt(0) ?? 'U').toUpperCase();
      const role = u.user?.role ?? 'user';
      const count = u.activity_count ?? 0;
      const bgColor = colors[i % colors.length];
      return `
        <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--c-divider);${i === topUsers.length - 1 ? 'border-bottom:none' : ''}">
          <div style="width:44px;height:44px;border-radius:50%;background:${bgColor};display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:16px;flex-shrink:0">${initial}</div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:13px;color:var(--c-text-dark)">${u.user?.name ?? 'Pengguna'}</div>
            <div style="font-size:12px;color:var(--c-text-grey);text-transform:capitalize;margin-top:2px">${role}</div>
          </div>
          <div style="font-size:13px;font-weight:700;color:var(--c-primary);flex-shrink:0">${count} aksi</div>
        </div>`;
    }).join('');
    usersList.innerHTML = html;
  }
}

async function loadActivityChart() {
  const res = await api.get('/activity/dashboard'); // 30 days default
  const d = res.data?.data ?? res.data ?? {};
  const byType = d.activity_by_type ?? [];
  
  // Load activity types chart - Horizontal Bar Chart
  const activityTypesContainer = document.getElementById('activity-types');
  if (activityTypesContainer) {
    if (!byType || byType.length === 0) {
      activityTypesContainer.innerHTML = `<div style="text-align:center;color:var(--c-text-grey);padding:20px">Tidak ada data aktivitas</div>`;
    } else {
      // Find max value for percentage calculation
      const maxValue = Math.max(...byType.map(item => item.count || 0), 1);
      
      const actionLabels = {
        'login': 'Login Berhasil',
        'logout': 'Logout',
        'login_failed': 'Login Gagal',
        'login_blocked': 'Login Diblokir',
        'password_change_failed': 'Gagal Ubah Password',
        'password_changed': 'Password Diubah',
        'profile_updated': 'Perbarui Profil',
        'token_refreshed': 'Token Diperbarui',
        'attendance_check_in': 'Siswa Naik Bus',
        'attendance_check_out': 'Siswa Turun Bus',
        'report_generated': 'Unduh Laporan'
      };

      const chartHtml = byType.map(item => {
        const action = item.action || 'unknown';
        const count = item.count || 0;
        const percentage = (count / maxValue) * 100;
        const displayLabel = actionLabels[action] || action.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        return `
          <div class="bar-chart-item">
            <div class="bar-label" title="${displayLabel}">${displayLabel}</div>
            <div class="bar-container">
              <div class="bar-fill" style="width:${percentage}%"></div>
            </div>
            <div class="bar-value">${count}</div>
          </div>`;
      }).join('');
      
      activityTypesContainer.innerHTML = chartHtml;
    }
  }
}

async function loadArmadaList() {
  const date = dp.value;
  const todayStr = new Date().toISOString().split('T')[0];
  
  // 1. Fetch buses and current GPS status
  const [busRes, gpsRes] = await Promise.all([
    api.get('/buses'),
    api.get('/gps-tracks/dashboard').catch(() => ({ data: null })),
  ]);
  const buses = busRes.data?.data ?? [];
  const gpsData = gpsRes.data?.data?.data ?? [];
  const gpsStatusMap = {};
  gpsData.forEach(g => {
    gpsStatusMap[g.bus_id] = g.gps_status ?? g.status;
  });

  // 2. Fetch attendance for the selected day or week to know which buses are/were operating
  let operatingBusIds = new Set();
  if (mode === 'harian') {
    const attRes = await api.get('/attendance', { date, per_page: 500 });
    const raw = attRes.data?.data ?? [];
    raw.forEach(att => {
      if (att.bus_id) operatingBusIds.add(att.bus_id);
    });
  } else {
    // Mode: mingguan. Get attendance from the 7 days ending at selected date
    const baseDate = new Date(date);
    const weekStart = new Date(baseDate);
    weekStart.setDate(baseDate.getDate() - 6);
    
    const days = Array.from({length:7}, (_,i) => { 
      const d = new Date(weekStart); 
      d.setDate(weekStart.getDate()+i); 
      return d.toISOString().split('T')[0]; 
    });
    
    const attResults = await Promise.all(days.map(ds => api.get('/attendance', { date: ds, per_page: 200 })));
    attResults.forEach(res => {
      const raw = res.data?.data ?? [];
      raw.forEach(att => {
        if (att.bus_id) operatingBusIds.add(att.bus_id);
      });
    });
  }

  if (!buses.length) {
    document.getElementById('armada-list').innerHTML = `<div class="empty-state" style="padding:20px"><span class="material-icons">directions_bus</span><p>Tidak ada data bus</p></div>`;
    return;
  }

  // 3. Render list with accurate status and photos
  const html = buses.map(bus => {
    let statusText = 'Tidak Beroperasi';
    let badgeClass = 'background:#E0E0E0;color:#666'; // Gray
    
    if (bus.status === 'maintenance') {
      statusText = 'Maintenance';
      badgeClass = 'background:#E8F5E9;color:#2E7D32'; // Green variant
    } else if (bus.status === 'tidak_aktif' || bus.status === 'non_aktif') {
      statusText = 'Tidak Beroperasi';
      badgeClass = 'background:#FFE5E5;color:#D32F2F'; // Red
    } else {
      // Bus status is active ('aktif') in database
      const isOperating = operatingBusIds.has(bus.id);
      const isGpsOn = gpsStatusMap[bus.id] === 'on';
      
      if (isOperating && isGpsOn) {
        statusText = 'Beroperasi';
        badgeClass = 'background:#E8F5E9;color:#2E7D32'; // Green
      } else if (isOperating && !isGpsOn) {
        statusText = 'GPS Mati';
        badgeClass = 'background:#FFF8E1;color:#8E6C3A'; // Muted amber warning
      } else {
        statusText = 'Tidak Beroperasi';
        badgeClass = 'background:#E0E0E0;color:#666'; // Gray
      }
    }

    return `
      <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--c-divider)">
        <div style="width:40px;height:40px;border-radius:6px;overflow:hidden;flex-shrink:0;background:#f0f0f0;display:flex;align-items:center;justify-content:center;border:1px solid var(--c-divider)">
          ${bus.photo_url ? `<img src="${proxyImgUrl(bus.photo_url)}" style="width:100%;height:100%;object-fit:cover" alt="${bus.kode_bus}">` : `<span class="material-icons" style="color:#ccc;font-size:20px">directions_bus</span>`}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:13px">${bus.nama_bus ?? bus.kode_bus ?? '-'}</div>
          <div style="font-size:11px;color:var(--c-text-grey)">${bus.plat_nomor ?? '-'}</div>
        </div>
        <div style="${badgeClass};padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap">
          ${statusText}
        </div>
      </div>
    `;
  }).join('');
  
  document.getElementById('armada-list').innerHTML = html;
}

async function loadDriverList() {
  const res = await api.get('/drivers');
  const drivers = res.data?.data ?? [];
  
  if (!drivers.length) {
    document.getElementById('driver-list').innerHTML = `<div class="empty-state" style="padding:20px"><span class="material-icons">badge</span><p>Tidak ada data driver</p></div>`;
    return;
  }

  const html = drivers.slice(0, 10).map(driver => {
    const driverName = driver.user?.name ?? driver.name ?? '-';
    const isSuspended = driver.user?.is_suspended ?? false;
    
    // Ambil bus yang aktif (tanggal_selesai null atau >= hari ini)
    const today = new Date().toISOString().split('T')[0];
    const activeBus = driver.buses?.find(b => !b.pivot?.tanggal_selesai || b.pivot.tanggal_selesai >= today);
    const busDisplay = activeBus ? `${activeBus.nama_bus || activeBus.kode_bus || '-'} · ${activeBus.plat_nomor || '-'}` : '-';
    
    return `
      <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--c-divider)">
        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f0f4f2; border:1px solid #dde6e0; flex-shrink:0;">
          <img src="${driver.user?.photo_url ? proxyImgUrl(driver.user.photo_url) : '/images/driver/default.svg'}" 
               alt="" 
               style="width:100%; height:100%; object-fit:cover;"
               onerror="this.src='/images/driver/default.svg'">
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:13px">${driverName}</div>
          <div style="font-size:11px;color:var(--c-text-grey)">${busDisplay}</div>
        </div>
        <div style="${!isSuspended ? 'background:var(--c-primary-light);color:var(--c-primary)' : 'background:#FFE5E5;color:#D32F2F'};padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap">
          ${!isSuspended ? 'Aktif' : 'Tidak Aktif'}
        </div>
      </div>`;
  }).join('');
  
  document.getElementById('driver-list').innerHTML = html;
}

async function loadBusStats() {}

let weekStartDate = null;

async function changeWeek(offset) {
  if (!weekStartDate) weekStartDate = new Date(dp.value);
  weekStartDate.setDate(weekStartDate.getDate() + (offset * 7));
  dp.value = weekStartDate.toISOString().split('T')[0];
  await loadWeekly();
}

async function loadWeekly() {
  const baseDate = new Date(dp.value);
  const weekEnd = new Date(baseDate);
  const weekStart = new Date(baseDate);
  weekStart.setDate(baseDate.getDate() - 6);
  weekStartDate = new Date(weekStart);
  
  // Format tanggal range
  const startStr = weekStart.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short'});
  const endStr = weekEnd.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short', year:'numeric'});
  document.getElementById('week-range-display').textContent = `${startStr} – ${endStr}`;
  
  const days = Array.from({length:7}, (_,i) => { const d = new Date(weekStart); d.setDate(weekStart.getDate()+i); return d; });
  const results = await Promise.all(days.map(async d => {
    const ds = d.toISOString().split('T')[0];
    const [a, r] = await Promise.all([api.get('/attendance', {date:ds, per_page:200}), api.get('/reports/admin', {tanggal:ds})]);
    const raw = a.data?.data ?? [];
    const rep = r.data?.data ?? r.data ?? {};
    return { 
      date: ds, 
      label: d.toLocaleDateString('id-ID',{weekday:'short',day:'numeric',month:'short'}), 
      absensi_count: raw.length, 
      absensi_data: raw, 
      laporan_count: rep.total_reports ?? (rep.reports?.length ?? 0),
      laporan_data: rep.reports ?? []
    };
  }));
  
  // Calculate weekly summary
  const totalAbsensi = results.reduce((sum, r) => sum + r.absensi_count, 0);
  const totalLaporan = results.reduce((sum, r) => sum + r.laporan_count, 0);
  const busAktif = new Set(results.flatMap(r => r.absensi_data).filter(att => att.bus_id).map(att => att.bus_id)).size;
  
  document.getElementById('w-penumpang').textContent = totalAbsensi;
  document.getElementById('w-laporan').textContent = totalLaporan;
  document.getElementById('w-bus-aktif').textContent = busAktif;
  
  // Render bar chart dynamically - normalize height to max value
  const maxAbsensi = Math.max(...results.map(r => r.absensi_count), 1);
  const chartHtml = `
    <div style="display:flex;align-items:flex-end;justify-content:space-around;width:100%;max-width:400px;height:180px;gap:8px">
      ${results.map((r, i) => {
        const height = (r.absensi_count / maxAbsensi) * 140; // max height 140px
        const dayLabel = new Date(r.date + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'short' });
        return `
          <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
            <div style="background:#81C784;height:${height}px;width:32px;border-radius:4px" title="${r.absensi_count} penumpang"></div>
            <div style="font-size:11px;color:var(--c-text-grey);text-transform:capitalize">${dayLabel}</div>
          </div>
        `;
      }).join('')}
    </div>
  `;
  document.getElementById('weekly-chart').innerHTML = chartHtml;
  
  // ── UPDATE RINGKASAN CARDS UNTUK MINGGUAN ──

  // 1. Absensi Pekan Ini
  const allWeeklyAttendance = results.flatMap(r => r.absensi_data);
  const totalWeeklyCheckin = allWeeklyAttendance.length;
  const totalWeeklyCheckout = allWeeklyAttendance.filter(att => att.waktu_turun).length;
  const checkoutPct = totalWeeklyCheckin > 0 ? Math.round((totalWeeklyCheckout / totalWeeklyCheckin) * 100) : 0;
  
  const attendanceCard = document.getElementById('attendance-card');
  if (attendanceCard) {
    attendanceCard.innerHTML = `
      <div style="display:flex;gap:12px;margin-bottom:14px">
        <div style="flex:1;text-align:center;padding:14px;background:var(--c-primary-light);border-radius:12px">
          <span class="material-icons" style="font-size:32px;color:var(--c-primary);display:block;margin-bottom:6px">directions_bus</span>
          <div style="font-size:20px;font-weight:800;color:var(--c-primary)">${totalWeeklyCheckin}</div>
          <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Naik Bus (Pekan Ini)</div>
        </div>
        <div style="flex:1;text-align:center;padding:14px;background:#E0F7FA;border-radius:12px">
          <span class="material-icons" style="font-size:32px;color:#00BCD4;display:block;margin-bottom:6px">check_circle</span>
          <div style="font-size:20px;font-weight:800;color:#00BCD4">${totalWeeklyCheckout}</div>
          <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px">Sudah Turun (Pekan Ini)</div>
        </div>
      </div>
      <div style="padding:12px;background:var(--c-background);border-radius:10px;text-align:center">
        <div style="font-size:12px;color:var(--c-text-grey);margin-bottom:8px">Rate Checkout Pekan Ini</div>
        <div style="font-size:18px;font-weight:700;color:var(--c-primary);margin-bottom:8px">${checkoutPct}%</div>
        <div style="width:100%;height:6px;background:#E0E0E0;border-radius:3px;overflow:hidden">
          <div style="height:100%;background:var(--c-primary);width:${checkoutPct}%;border-radius:3px;transition:width 0.3s ease"></div>
        </div>
      </div>`;
  }

  // 2. Laporan Driver Pekan Ini
  const allWeeklyReports = results.flatMap(r => r.laporan_data);
  const reportCard = document.getElementById('report-card');
  if (reportCard) {
    if (!allWeeklyReports.length) {
      reportCard.innerHTML = `<div class="empty-state" style="padding:20px;text-align:center"><span class="material-icons" style="font-size:48px;color:var(--c-text-grey);display:block;margin-bottom:12px">description</span><p>Belum ada laporan driver pekan ini</p></div>`;
    } else {
      reportCard.innerHTML = `<div class="table-wrap"><table style="width:100%">
        <thead>
          <tr style="background:var(--c-background);border-bottom:2px solid var(--c-divider)">
            <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Tanggal</th>
            <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Bus</th>
            <th style="text-align:center;padding:12px 16px;font-weight:600;font-size:12px">Penumpang</th>
            <th style="text-align:left;padding:12px 16px;font-weight:600;font-size:12px">Catatan</th>
          </tr>
        </thead>
        <tbody>
          ${allWeeklyReports.map((r, i) => {
            const dateObj = new Date(r.tanggal + 'T00:00:00');
            const displayDate = dateObj.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short'});
            return `<tr style="border-bottom:1px solid var(--c-divider);${i % 2 === 0 ? 'background:var(--c-background)' : ''}">
              <td style="padding:12px 16px;font-size:13px;white-space:nowrap">${displayDate}</td>
              <td style="padding:12px 16px;font-size:13px">${r.bus?.kode_bus ?? '-'} (${r.bus?.plat_nomor ?? '-'})</td>
              <td style="text-align:center;padding:12px 16px"><b style="color:var(--c-primary);font-size:14px">${r.total_penumpang ?? 0}</b></td>
              <td style="padding:12px 16px;font-size:12px;color:var(--c-text-grey);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.catatan_driver ?? '-'}</td>
            </tr>`;
          }).join('')}
        </tbody>
      </table></div>`;
    }
  }

  // 3 & 4. Aktivitas Sistem Pekan Ini & Pengguna Paling Aktif Pekan Ini
  const weekStartStr = weekStart.toISOString().split('T')[0];
  const weekEndStr = weekEnd.toISOString().split('T')[0];
  const actRes = await api.get('/activity/dashboard', {
    start_date: weekStartStr,
    end_date: weekEndStr
  });
  const actData = actRes.data?.data ?? {};
  const loginBerhasil = actData.summary?.recent_logins_24h ?? 0;
  const loginGagal = actData.summary?.failed_logins_24h ?? 0;
  const suspendedAccountsCount = actData.summary?.suspended_accounts ?? 0;
  
  // Update pengguna tab stats
  document.getElementById('act-login').textContent = loginBerhasil;
  document.getElementById('act-failed').textContent = loginGagal;
  
  const suspendCard = document.getElementById('p-suspend');
  const suspendSub = document.getElementById('p-suspend-sub');
  if (suspendCard) suspendCard.textContent = suspendedAccountsCount;
  if (suspendSub) suspendSub.textContent = 'Akun nonaktif';
  
  const activityCard = document.getElementById('activity-card');
  if (activityCard) {
    activityCard.innerHTML = `
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:var(--c-primary-light);border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:var(--c-primary);display:block;margin-bottom:8px">done</span>
        <div style="font-size:22px;font-weight:800;color:var(--c-primary)">${loginBerhasil}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Login Berhasil</div>
      </div>
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:#FFE5E5;border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:#D32F2F;display:block;margin-bottom:8px">close</span>
        <div style="font-size:22px;font-weight:800;color:#D32F2F">${loginGagal}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Login Gagal</div>
      </div>
      <div style="flex:1;min-width:110px;text-align:center;padding:16px;background:#FFF3CD;border-radius:12px">
        <span class="material-icons" style="font-size:32px;color:#F57C00;display:block;margin-bottom:8px">warning</span>
        <div style="font-size:22px;font-weight:800;color:#F57C00">${suspendedAccountsCount}</div>
        <div style="font-size:11px;color:var(--c-text-grey);margin-top:4px">Akun Suspend</div>
      </div>`;
  }

  const topUsers = actData.top_active_users ?? [];
  const usersList = document.getElementById('active-users-list');
  if (usersList) {
    if (!topUsers || topUsers.length === 0) {
      usersList.innerHTML = `<div style="text-align:center;color:var(--c-text-grey);padding:20px">Tidak ada data pengguna aktif pekan ini</div>`;
    } else {
      const colors = ['#1B5E37', '#2E7D52', '#4CAF50', '#66BB6A', '#81C784', '#A5D6A7', '#C8E6C9', '#9CCC65', '#43A047', '#2E7D32'];
      const html = topUsers.map((u, i) => {
        const initial = (u.user?.name?.charAt(0) ?? 'U').toUpperCase();
        const role = u.user?.role ?? 'user';
        const count = u.activity_count ?? 0;
        const bgColor = colors[i % colors.length];
        return `
          <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--c-divider);${i === topUsers.length - 1 ? 'border-bottom:none' : ''}">
            <div style="width:44px;height:44px;border-radius:50%;background:${bgColor};display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:16px;flex-shrink:0">${initial}</div>
            <div style="flex:1;min-width:0">
              <div style="font-weight:600;font-size:13px;color:var(--c-text-dark)">${u.user?.name ?? 'Pengguna'}</div>
              <div style="font-size:12px;color:var(--c-text-grey);text-transform:capitalize;margin-top:2px">${role}</div>
            </div>
            <div style="font-size:13px;font-weight:700;color:var(--c-primary);flex-shrink:0">${count} aksi</div>
          </div>`;
      }).join('');
      usersList.innerHTML = html;
    }
  }

  // Render summary list
  const summaryHtml = results.map((r, i) => {
    const hasData = r.absensi_count > 0 || r.laporan_count > 0;
    const fullDate = results[i].date;
    const dateObj = new Date(fullDate + 'T00:00:00');
    const displayDate = dateObj.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long'});
    
    if (!hasData) {
      return `
        <div class="card" style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;opacity:0.6">
          <div style="display:flex;align-items:center;gap:12px;flex:1">
            <div style="width:20px;height:20px;border:2px solid #ddd;border-radius:50%"></div>
            <div style="font-size:13px;color:var(--c-text-grey)">${displayDate}</div>
          </div>
          <div style="font-size:12px;color:var(--c-text-grey)">tidak ada data</div>
        </div>`;
    }
    
    return `
      <div class="card" style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:12px;flex:1">
          <div style="width:20px;height:20px;border-radius:50%;background:#4CAF50;display:flex;align-items:center;justify-content:center">
            <span class="material-icons" style="color:white;font-size:12px">check</span>
          </div>
          <div style="font-size:13px;color:var(--c-text-dark);font-weight:500">${displayDate}</div>
        </div>
        <div style="display:flex;align-items:center;gap:16px">
          <div style="text-align:right">
            <div style="font-size:13px;font-weight:600;color:var(--c-primary)">${r.absensi_count} penumpang</div>
            <div style="font-size:11px;color:var(--c-text-grey)">${r.laporan_count} Laporan</div>
          </div>
        </div>
      </div>`;
  }).join('');
  
  document.getElementById('weekly-summary-list').innerHTML = summaryHtml;

  // ── UPDATE ARMADA CARDS UNTUK MINGGUAN ──
  const busRes = await api.get('/buses');
  const buses = busRes.data?.data ?? [];
  const totalBuses = buses.length;
  const maintCount = buses.filter(b => b.status === 'maintenance').length;

  const weeklyBusIds = new Set(allWeeklyAttendance.filter(a => a.bus_id).map(a => a.bus_id));
  const weeklyBusAktif = weeklyBusIds.size;
  const weeklyGpsOn = weeklyBusAktif; // Since they operated, GPS was active

  const weeklyCheckoutCount = allWeeklyAttendance.filter(a => a.waktu_turun).length;

  // Ringkasan stats weekly update
  document.getElementById('s-bus').textContent = weeklyBusAktif;
  document.getElementById('s-bus-sub').textContent = `${totalBuses} total armada`;

  document.getElementById('a-aktif').textContent = weeklyBusAktif;
  document.getElementById('a-aktif-sub').textContent = `${totalBuses} total armada`;
  document.getElementById('a-gps').textContent = weeklyGpsOn;
  document.getElementById('a-gps-sub').textContent = `${totalBuses - weeklyGpsOn} GPS mati`;
  document.getElementById('a-maint').textContent = maintCount;
  document.getElementById('a-maint-sub').textContent = 'Bus dalam servis';
  document.getElementById('a-penumpang').textContent = weeklyCheckoutCount;
  document.getElementById('a-penumpang-label').textContent = 'PENUMPANG PEKAN INI';
  document.getElementById('a-penumpang-sub').textContent = `${allWeeklyAttendance.length} total naik`;
}

loadAll();
</script>
@endpush
