@extends('admin.layouts.app')
@section('title','Profil Admin')
@section('page-title','Profil')
@section('content')

@php
  $user     = session('admin_user');
  $parts    = !empty($user['name']) ? explode(' ', trim($user['name'])) : ['AD'];
  $initials = count($parts) >= 2
    ? strtoupper(substr($parts[0],0,1).substr($parts[1],0,1))
    : strtoupper(substr($parts[0],0,2));
  
  $photoUrl = '/images/admin/default.svg';
  if (!empty($user['photo_url'])) {
      $path = parse_url($user['photo_url'], PHP_URL_PATH);
      $photoUrl = '/storage-proxy' . $path;
  } elseif (!empty($user['photo'])) {
      $photoUrl = '/storage-proxy/' . ltrim($user['photo'], '/');
  }
@endphp


<style>
/* ── Wrapper ── */
.profil-wrap {
  max-width: 920px;
  margin: 0 auto;
  padding: 0 0 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ── Hero Card ── */
.profil-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px 28px 0;
  color: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15,61,34,.28);
}
.profil-hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
  pointer-events: none;
}
.profil-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255,255,255,.04);
  pointer-events: none;
}

/* Top row: avatar + info */
.profil-hero-top {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  position: relative;
  z-index: 2;
  padding-bottom: 22px;
}

/* Avatar styling */
.profil-avatar {
  width: 88px; height: 88px;
  border-radius: 50%;
  border: 3px solid rgba(255,255,255,.35);
  background: var(--c-primary-light, #E8F5ED);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  position: relative;
  box-shadow: 0 4px 16px rgba(0,0,0,.22);
}
.profil-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  display: block;
}


.profil-info { flex: 1; min-width: 0; }
.profil-name {
  font-size: 21px; font-weight: 700; margin: 0 0 4px;
  letter-spacing: -.3px; color: #fff; line-height: 1.25;
}
.profil-email {
  font-size: 12.5px; color: rgba(255,255,255,.65);
  margin-bottom: 10px; word-break: break-all;
}
.profil-badges { display: flex; flex-wrap: wrap; gap: 7px; }
.badge-role {
  font-size: 11px; font-weight: 600;
  padding: 3px 10px; border-radius: 20px;
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.22);
  letter-spacing: .5px; text-transform: uppercase;
}
.badge-status {
  font-size: 11px; font-weight: 600;
  padding: 3px 10px; border-radius: 20px;
  background: rgba(74,222,128,.15);
  border: 1px solid rgba(74,222,128,.3);
  color: #86efac;
  display: flex; align-items: center; gap: 5px;
}
.badge-status-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #4ade80; box-shadow: 0 0 6px #4ade80;
  flex-shrink: 0;
}

/* Bottom strip: mini stats inside hero */
.profil-hero-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  border-top: 1px solid rgba(255,255,255,.12);
  position: relative; z-index: 2;
}
.hero-stat {
  padding: 14px 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  border-right: 1px solid rgba(255,255,255,.1);
  transition: background .2s;
}
.hero-stat:last-child { border-right: none; }
.hero-stat:hover { background: rgba(255,255,255,.05); }
.hero-stat-val {
  font-size: 22px; font-weight: 800; color: #fff; line-height: 1;
}
.hero-stat-lbl {
  font-size: 10px; font-weight: 600;
  color: rgba(255,255,255,.6);
  text-align: center; line-height: 1.3;
  text-transform: uppercase; letter-spacing: .4px;
}

/* ── Two-col body ── */
.profil-cols {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}
@media (min-width: 680px) {
  .profil-cols { grid-template-columns: 1fr 1fr; }
}

/* ── Section header ── */
.section-head {
  font-size: 11px; font-weight: 700;
  color: var(--c-text-grey, #6B7B73);
  text-transform: uppercase; letter-spacing: 1px;
  margin-bottom: 10px;
  display: flex; align-items: center; gap: 8px;
}
.section-head::after {
  content: ''; flex: 1; height: 1px;
  background: var(--c-divider, #EBF0ED);
}

/* ── Data pribadi card ── */
.data-card {
  background: var(--c-white, #fff);
  border: 1px solid var(--c-border, #DDE6E0);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.data-row {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 14px 18px;
  border-bottom: 1px solid var(--c-divider, #EBF0ED);
  transition: background .15s;
}
.data-row:last-child { border-bottom: none; }
.data-row:hover { background: rgba(27,94,55,.03); }
.data-icon-wrap {
  width: 36px; height: 36px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; margin-top: 1px;
}
.data-icon-wrap .material-icons { font-size: 18px; }
.data-row:nth-child(1) .data-icon-wrap { background: rgba(21,101,192,.1); color: #1565C0; }
.data-row:nth-child(2) .data-icon-wrap { background: rgba(27,94,55,.1);  color: #1B5E37; }
.data-row:nth-child(3) .data-icon-wrap { background: rgba(230,126,0,.1); color: #E67E00; }
.data-lbl {
  font-size: 10px; font-weight: 700;
  color: var(--c-text-grey, #6B7B73);
  text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px;
}
.data-val {
  font-size: 13.5px; color: var(--c-text-dark, #1a1a1a);
  font-weight: 500; line-height: 1.5; word-break: break-word;
}

/* ── Akun menu card ── */
.akun-card {
  background: var(--c-white, #fff);
  border: 1px solid var(--c-border, #DDE6E0);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.menu-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 18px;
  text-decoration: none; color: inherit;
  border-bottom: 1px solid var(--c-divider, #EBF0ED);
  transition: background .15s;
  cursor: pointer;
}
.menu-row:last-child { border-bottom: none; }
.menu-row:hover { background: rgba(27,94,55,.04); }
.menu-row-icon {
  width: 38px; height: 38px; border-radius: 10px;
  background: rgba(27,94,55,.1); color: #1B5E37;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.menu-row-icon .material-icons { font-size: 20px; }
.menu-row-body { flex: 1; min-width: 0; }
.menu-row-title {
  font-size: 13.5px; font-weight: 600;
  color: var(--c-text-dark, #1a1a1a); margin-bottom: 1px;
}
.menu-row-desc {
  font-size: 11px; color: var(--c-text-grey, #6B7B73);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.menu-row-arrow {
  color: var(--c-text-grey, #9ca3af);
  transition: transform .2s, color .2s;
  font-size: 20px;
}
.menu-row:hover .menu-row-arrow { transform: translateX(3px); color: #1B5E37; }

/* ── Logout ── */
.btn-logout {
  width: 100%; padding: 13px 20px;
  background: #D32F2F; color: #fff;
  border: none; border-radius: 14px;
  font-size: 14px; font-weight: 600;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
  box-shadow: 0 4px 14px rgba(211,47,47,.22);
  font-family: inherit;
}
.btn-logout:hover {
  background: #b71c1c;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(211,47,47,.32);
}
.btn-logout:active { transform: translateY(0); }

/* ── Mobile ── */
@media (max-width: 499px) {
  .profil-hero-top { flex-direction: column; align-items: center; text-align: center; }
  .profil-badges { justify-content: center; }
  .profil-hero-stats { grid-template-columns: repeat(2, 1fr); }
  .hero-stat:nth-child(2) { border-right: none; }
}
</style>

<div class="profil-wrap">

  {{-- ── Hero Card ── --}}
  <div class="profil-hero">
    <div class="profil-hero-top">

      {{-- Avatar: img tag with default fallback on error --}}
      <div class="profil-avatar" id="profil-avatar-el">
        <img src="{{ $photoUrl }}" 
             alt="" 
             id="profil-avatar-img"
             class="profil-avatar-img"
             onerror="this.src='/images/admin/default.svg'">
      </div>


      <div class="profil-info">
        <div class="profil-name">{{ $user['name'] ?? 'Administrator' }}</div>
        <div class="profil-email">{{ $user['email'] ?? '' }}</div>
        <div class="profil-badges">
          <span class="badge-role">Administrator</span>
          <span class="badge-status">
            <span class="badge-status-dot"></span>Akun Aktif
          </span>
        </div>
      </div>
    </div>

    {{-- Mini-stats strip di bawah hero --}}
    <div class="profil-hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-val" id="sys-bus">—</div>
        <div class="hero-stat-lbl">Bus</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val" id="sys-driver">—</div>
        <div class="hero-stat-lbl">Driver</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val" id="sys-siswa">—</div>
        <div class="hero-stat-lbl">Siswa Terdaftar</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val" id="sys-pending">—</div>
        <div class="hero-stat-lbl">Perlu Persetujuan</div>
      </div>
    </div>
  </div>

  {{-- ── Two-column body ── --}}
  <div class="profil-cols">

    {{-- Kiri: Data Pribadi --}}
    <div>
      <div class="section-head">Data Pribadi</div>
      <div class="data-card">
        <div class="data-row">
          <div class="data-icon-wrap"><span class="material-icons">email</span></div>
          <div>
            <div class="data-lbl">Email</div>
            <div class="data-val">{{ $user['email'] ?? '-' }}</div>
          </div>
        </div>
        <div class="data-row">
          <div class="data-icon-wrap"><span class="material-icons">phone</span></div>
          <div>
            <div class="data-lbl">No. HP</div>
            <div class="data-val">{{ $user['no_hp'] ?? '-' }}</div>
          </div>
        </div>
        <div class="data-row">
          <div class="data-icon-wrap"><span class="material-icons">location_on</span></div>
          <div>
            <div class="data-lbl">Alamat</div>
            <div class="data-val">{{ $user['alamat'] ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Kanan: Akun + Logout --}}
    <div style="display:flex;flex-direction:column;gap:16px">
      <div>
        <div class="section-head">Akun</div>
        <div class="akun-card">
          <a href="{{ route('admin.profil.edit') }}" class="menu-row">
            <div class="menu-row-icon"><span class="material-icons">edit</span></div>
            <div class="menu-row-body">
              <div class="menu-row-title">Edit Profil</div>
              <div class="menu-row-desc">Ubah nama, nomor HP, alamat, avatar, dan password</div>
            </div>
            <span class="material-icons menu-row-arrow">chevron_right</span>
          </a>
        </div>
      </div>

      <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
        @csrf
        <button type="button" class="btn-logout" onclick="confirmLogout()">
          <span class="material-icons">logout</span>
          Keluar dari Akun
        </button>
      </form>
    </div>

  </div>
</div>

@endsection
@push('scripts')
<script>
// ── Stats ──
async function loadSysStats() {
  try {
    const [busR, drvR, stuR, pendR] = await Promise.all([
      api.get('/buses'),
      api.get('/drivers'),
      api.get('/students', { per_page: 1, approval_status: 'approved' }),
      api.get('/students/pending'),
    ]);
    const get = (r, ...keys) => {
      for (const k of keys) {
        const v = k.split('.').reduce((o, p) => o?.[p], r.data);
        if (v !== undefined && v !== null) return v;
      }
      return '—';
    };
    document.getElementById('sys-bus').textContent    = get(busR,  'data.length', 'pagination.total');
    document.getElementById('sys-driver').textContent = get(drvR,  'data.length', 'pagination.total');
    document.getElementById('sys-siswa').textContent  = get(stuR,  'pagination.total', 'data.length');
    document.getElementById('sys-pending').textContent= get(pendR, 'pagination.total', 'data.length');
  } catch(e) { console.error('Stats error:', e); }
}
loadSysStats();

// ── Logout ──
function confirmLogout() {
  confirmDialog('Kamu yakin ingin keluar dari akun ini?', () => document.getElementById('logout-form').submit());
}


</script>
@endpush
