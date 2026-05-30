@extends('admin.layouts.app')
@section('title','Manajemen Bus')
@section('page-title','Bus')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<style>
/* ╔══════════════════════════════════════════════════════════════╗ */
/* ║              BUS PAGE REDESIGN STYLES                         ║ */
/* ╚══════════════════════════════════════════════════════════════╝ */

/* Hero Card - Green Gradient (Bus Theme) */
.bus-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px;
  color: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.24);
  margin-bottom: 24px;
}
.bus-hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  pointer-events: none;
}
.bus-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.04);
  pointer-events: none;
}
.bus-hero-top {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 2;
}
.bus-hero-icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
}
.bus-hero-text h2 {
  margin: 0; font-size: 24px; font-weight: 700; color: #fff;
  letter-spacing: -0.3px;
}
.bus-hero-text p {
  margin: 4px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.75);
}

/* Filter Bar Improvements */
.bus-filter-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding: 12px 0;
  flex-wrap: wrap;
}
.bus-filter-bar .search-box {
  flex: 1;
  min-width: 220px;
}

.filter-btn {
  padding: 8px 16px;
  border: 1px solid var(--c-border);
  background: white;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  color: var(--c-text-grey);
  transition: all 200ms;
}
.filter-btn.active {
  background: linear-gradient(135deg, #0F3D22 0%, #2E7D52 100%);
  border-color: transparent;
  color: white;
  box-shadow: 0 4px 12px rgba(15, 61, 34, 0.25);
}
.filter-btn:hover:not(.active) {
  border-color: #1B5E37;
  background: rgba(30, 90, 56, 0.08);
}

/* ── Modal Fullscreen (Ubah Rute) ─── */
.modal-fullscreen {
  padding: 0 !important;
  align-items: stretch !important;
  justify-content: center !important;
}
.modal-fullscreen > div {
  animation: slideInRight .25s cubic-bezier(.4,0,.2,1);
  box-shadow: -4px 0 24px rgba(0,0,0,.15);
}
@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to   { transform: translateX(0);    opacity: 1; }
}
.urutan-item:active { opacity: 0.7; cursor: grabbing; }
.urutan-item[draggable]:hover { box-shadow: 0 2px 8px rgba(0,0,0,.12); }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Premium Photo Upload & Preview UI ── */
.photo-upload-zone {
  border: 2.5px dashed #abb8b0;
  border-radius: 12px;
  padding: 20px 16px;
  text-align: center;
  cursor: pointer;
  background: #f9fafb;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 194px;
  box-sizing: border-box;
}
.photo-upload-zone:hover {
  border-color: rgba(46, 125, 82, 0.8);
  background: rgba(222, 244, 230, 0.96);
}
.photo-upload-zone .material-icons {
  font-size: 32px;
  color: #1B5E37;
  transition: transform 0.2s ease;
}
.photo-upload-zone:hover .material-icons {
  transform: translateY(-3px);
}
.photo-upload-zone p {
  margin: 0;
  font-size: 13px;
  font-weight: 600;
  color: var(--c-text-dark);
}
.photo-upload-zone span {
  font-size: 10.5px;
  color: var(--c-text-grey);
  line-height: 1.3;
}
.preview-container {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border: 1px solid var(--c-border);
  height: 194px;
  background: #f9fafb;
  box-sizing: border-box;
}
.preview-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.preview-container .remove-preview-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(211, 47, 47, 0.9);
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.preview-container .remove-preview-btn:hover {
  background: rgba(211, 47, 47, 1);
  transform: scale(1.1);
}
.siswa-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px;
  border: 1px solid var(--c-border);
  border-radius: 14px;
  background: #fff;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.siswa-list-item:hover {
  transform: translateY(-1px);
  border-color: rgba(25, 118, 210, 0.25);
  box-shadow: 0 8px 20px rgba(17, 82, 147, 0.08);
}
.siswa-list-item button {
  flex-shrink: 0;
}
</style>

{{-- Hero Card --}}
<div class="bus-hero">
  <div class="bus-hero-top">
    <div class="bus-hero-icon"><span class="material-icons">directions_bus</span></div>
    <div class="bus-hero-text">
      <h2>Manajemen Bus</h2>
      <p>Kelola armada bus, rute, halte, dan status operasional</p>
    </div>
  </div>
</div>

    {{-- Modal Replace Driver (select replacement) --}}
    <div class="modal-overlay" id="replace-driver-modal" style="z-index:100120">
      <div class="modal" style="max-width:520px">
        <div class="modal-header">
          <div class="modal-title">Ganti Driver</div>
          <button class="modal-close" onclick="closeModal('replace-driver-modal')"><span class="material-icons">close</span></button>
        </div>
        <div class="modal-body">
          <div style="font-weight:700;margin-bottom:12px" id="replace-driver-status">Pilih driver pengganti</div>
          <div class="form-group" style="margin-bottom:12px">
            <div style="position:relative;">
              <span class="material-icons" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6b7280;font-size:20px">search</span>
              <input id="replace-driver-search-input" type="text" class="form-control" placeholder="Cari nama driver atau NIK" style="padding-left:40px;border-radius:10px;width:100%;" oninput="debounce(filterReplaceDriverSearch,300)()" />
            </div>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <div id="replace-driver-search-results" style="border:1px solid #e4e7eb;border-radius:12px;max-height:320px;overflow-y:auto;background:#fff"></div>
            <div id="replace-driver-selected-label" style="margin-top:10px;font-size:13px;color:#6b7280">Belum ada driver dipilih</div>
          </div>
          <input type="hidden" id="replace-driver-id" value="">
          <div class="form-group"><label class="form-label">Tanggal Mulai</label>
            <input class="form-control" id="replace-start" type="date"></div>
          <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-top:10px">
            <label class="form-checkbox" style="margin:0">
              <input type="checkbox" id="replace-finish-checkbox" onchange="toggleReplaceFinishDate()">
              <span>Tambahkan tanggal selesai</span>
            </label>
          </div>
          <div class="form-group" id="replace-finish-group" style="display:none;margin-top:10px">
            <label class="form-label">Tanggal Selesai</label>
            <input class="form-control" id="replace-finish" type="date">
          </div>
        </div>
        <div class="modal-footer" style="justify-content:flex-end;gap:10px;">
          <button class="btn btn-outline btn-sm" onclick="closeModal('replace-driver-modal')">Batal</button>
          <button class="btn btn-primary btn-sm" id="replace-submit-button" onclick="saveReplaceAssign()">Simpan</button>
        </div>
      </div>
    </div>

{{-- Filter & Search Bar --}}
<div class="bus-filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari kode bus, plat nomor..." oninput="debounce(loadBus,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadBus()"><span class="material-icons">refresh</span></button>
  <div style="flex: 1;"></div>
  <button class="filter-btn active" data-filter="all" onclick="setBusFilter('all', this)">Semua</button>
  <button class="filter-btn" data-filter="aktif" onclick="setBusFilter('aktif', this)">Aktif</button>
  <button class="filter-btn" data-filter="maintenance" onclick="setBusFilter('maintenance', this)">Perawatan</button>
  <button class="filter-btn" data-filter="nonaktif" onclick="setBusFilter('nonaktif', this)">Nonaktif</button>
  <button class="filter-btn" data-filter="no-driver" onclick="setBusFilter('no-driver', this)">No-Driver</button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Foto</th><th>Kode Bus</th><th>Plat Nomor</th><th>Status</th><th>GPS</th><th>Driver</th><th>Aksi</th></tr></thead>
      <tbody id="bus-tbody">
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="bus-pagination" style="padding:12px 14px"></div>
</div>

{{-- Modal Bus --}}
<div class="modal-overlay" id="bus-modal">
  <div class="modal" style="max-width:580px; border-radius:16px; overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border-bottom:none; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,0.18); color:#fff; display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">directions_bus</span>
        </div>
        <div>
          <div class="modal-title" id="bus-modal-title" style="font-weight:700; font-size:16px; color:#fff; margin:0;">Tambah Bus</div>
          <div style="font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px;">Lengkapi informasi detail armada bus sekolah</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('bus-modal')" style="color:#fff"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body" style="padding:24px;">
      <form id="bus-form">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:start;">
          
          <!-- Kiri: Inputs -->
          <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Kode Bus</label>
              <input class="form-control" name="kode_bus" placeholder="Contoh: BUS-01" required style="border-radius:10px;">
            </div>
            
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Plat Nomor</label>
              <input class="form-control" name="plat_nomor" placeholder="Contoh: AE 1234 XX" required style="border-radius:10px;">
            </div>
            
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Status Operasional</label>
              <select class="form-control" name="status" style="border-radius:10px; cursor:pointer;">
                <option value="aktif">🟢 Aktif</option>
                <option value="maintenance">🟡 Perawatan</option>
                <option value="nonaktif">🔴 Non-aktif</option>
              </select>
            </div>
          </div>
          
          <!-- Kanan: Upload Foto -->
          <div style="display:flex; flex-direction:column; gap:8px;">
            <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Foto Bus</label>
            
            <div class="photo-upload-zone" id="bus-photo-upload-zone" onclick="document.getElementById('bus-photo-input').click()">
              <span class="material-icons">add_a_photo</span>
              <p>Pilih Foto Bus</p>
              <span>Format JPG/PNG, maks 2MB</span>
            </div>
            <input type="file" id="bus-photo-input" name="photo" accept="image/*" style="display:none">
            
            <div id="bus-photo-preview" style="display:none">
              <div class="preview-container">
                <img id="bus-photo-img" src="" alt="Preview">
                <button type="button" class="remove-preview-btn" onclick="removePhotoSelection(event)" title="Hapus foto">
                  <span class="material-icons" style="font-size:16px">delete</span>
                </button>
              </div>
            </div>
          </div>
          
        </div>
      </form>
    </div>
    <div class="modal-footer" style="background:#f8faf9; border-top:1px solid #eef2f0; padding:16px 24px; display:flex; justify-content:flex-end; gap:12px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('bus-modal')" style="border-radius:8px;">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveBus()" style="border-radius:8px; background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border:none;">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Assign Driver --}}
<div class="modal-overlay" id="assign-modal" style="z-index:100110">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <div class="modal-title">Assign Driver</div>
      <button class="modal-close" onclick="closeModal('assign-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div id="assign-driver-status" style="margin-bottom:16px;font-weight:700;color:#111">Memuat driver...</div>
      <div id="assign-driver-detail" style="display:none;padding:14px;border-radius:12px;background:#f3f6fb;color:#111;margin-bottom:16px"></div>
      <div class="form-group" id="assign-driver-search-toggle" style="margin-bottom:14px;display:none">
        <button type="button" class="btn btn-outline btn-sm" style="width:100%;border-radius:12px;justify-content:center;display:flex;align-items:center;gap:8px;" onclick="showAssignDriverSearchPanel()">
          <span class="material-icons" style="font-size:18px">search</span>
          Cari Driver Pengganti
        </button>
      </div>
      <div class="form-group" id="assign-driver-search-panel" style="margin-bottom:14px;display:none">
        <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Cari Driver</label>
        <div style="position:relative;">
          <span class="material-icons" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6b7280;font-size:20px">search</span>
          <input id="assign-driver-search-input" type="text" class="form-control" placeholder="Cari nama driver atau NIK" style="padding-left:40px;border-radius:10px;width:100%;" oninput="debounce(filterAssignDriverSearch,300)()" />
        </div>
      </div>
      <div class="form-group" id="assign-driver-search-results-panel" style="margin-bottom:12px;display:none">
        <div id="assign-driver-search-results" style="border:1px solid #e4e7eb;border-radius:12px;max-height:220px;overflow-y:auto;background:#fff"></div>
        <div id="assign-driver-selected-label" style="margin-top:10px;font-size:13px;color:#6b7280">Belum ada driver dipilih</div>
      </div>
      <input type="hidden" id="assign-driver-id" value="">
      <div class="form-group"><label class="form-label">Tanggal Mulai</label>
        <input class="form-control" id="assign-start" type="date"></div>
      <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-top:10px">
        <label class="form-checkbox" style="margin:0">
          <input type="checkbox" id="assign-finish-checkbox" onchange="toggleAssignFinishDate()">
          <span>Tambahkan tanggal selesai</span>
        </label>
      </div>
      <div class="form-group" id="assign-finish-group" style="display:none;margin-top:10px">
        <label class="form-label">Tanggal Selesai</label>
        <input class="form-control" id="assign-finish" type="date">
      </div>
    </div>
    <div class="modal-footer" style="justify-content:flex-end;gap:10px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('assign-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" id="assign-submit-button" onclick="saveAssign()">Simpan</button>
      <button class="btn btn-outline btn-sm" id="assign-replace-button" onclick="openReplaceDriverModal()" style="display:none">Ganti Driver</button>
    </div>
  </div>
</div>

{{-- Modal Route & Halte --}}
<div class="modal-overlay" id="route-halte-modal">
  <div class="modal" style="max-width:650px;max-height:90vh;overflow-y:auto">
    <div class="modal-header" style="position:sticky;top:0;z-index:10">
      <div>
        <div class="modal-title" id="route-title-text">Rute & Halte</div>
        <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px" id="route-polyline-count"></div>
      </div>
      <button class="modal-close" onclick="closeModal('route-halte-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <!-- Peta -->
      <div id="route-map" style="width:100%;height:320px;border-radius:8px;margin-bottom:20px;background:#f0f0f0"></div>
      
      <!-- Urutan Halte -->
      <div style="margin-bottom:20px">
        <div style="font-weight:600;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
          <span>📍 Urutan Halte</span>
          <span id="halte-count-badge" style="background:var(--c-primary);color:white;padding:2px 8px;border-radius:12px;font-size:12px">0 halte</span>
        </div>
        <div id="halte-list-content" style="display:flex;flex-direction:column;gap:12px"></div>
      </div>
      
      <!-- Info Guides -->
      <div style="background:#E8F5E9;border-left:4px solid #2E7D32;padding:12px;border-radius:4px;font-size:12px;margin-bottom:16px">
        <div style="font-weight:600;color:#1B5E20;margin-bottom:8px">Cara membuat rute</div>
        <ol style="margin:0;padding-left:16px">
          <li>Pastikan halte sudah terdaftar di menu Halte</li>
          <li>Tap "Ubah Rute" untuk setting rute baru (atau ubah rute lama)</li>
          <li>Pilih halte-halte yang dilalui bus, atur urutannya</li>
          <li>Jalan di peta otomatis terekam ikuti jalan nyata</li>
        </ol>
      </div>
    </div>
    <div class="modal-footer" style="position:sticky;bottom:0;z-index:10">
      <button class="btn btn-sm btn-outline" onclick="closeModal('route-halte-modal')">Tutup</button>
      <button class="btn btn-sm" style="background:#2E7D32;color:white;border:none" id="edit-route-btn" onclick="editRoute()">Ubah Rute</button>
      <button class="btn btn-sm" style="background:#D32F2F;color:white;border:none" id="delete-route-btn" onclick="deleteRoute()">Hapus</button>
    </div>
  </div>
</div>

{{-- Modal Ubah Rute — Full Desktop Layout --}}
<div class="modal-overlay" id="edit-route-modal" data-no-close
  style="inset:0;padding:0;background:#1B5E37;align-items:stretch;justify-content:stretch;z-index:500">
  <div style="display:flex;flex-direction:column;width:100%;height:100vh;overflow:hidden">

    {{-- ── TOP HEADER BAR ── --}}
    <div style="display:flex;align-items:center;gap:16px;padding:0 24px;height:60px;background:#1B5E37;flex-shrink:0;z-index:10">
      <button onclick="closeEditRouteModal()"
        style="background:rgba(255,255,255,.15);border:none;cursor:pointer;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;transition:background .2s;padding:0 12px;font-weight:700;gap:6px"
        onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
        <span class="material-icons" style="font-size:20px">arrow_back</span>
        <span style="font-size:14px;letter-spacing:.2px">Kembali</span>
      </button>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:16px;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" id="edit-route-title">Ubah Rute</div>
      </div>
      {{-- Route info badge --}}
      <div id="edit-route-info-badge" style="display:none;align-items:center;gap:6px;background:rgba(255,255,255,.15);border-radius:8px;padding:6px 12px">
        <span class="material-icons" style="font-size:15px;color:rgba(255,255,255,.8)">straighten</span>
        <span id="edit-route-distance" style="font-size:12px;color:white;font-weight:600"></span>
      </div>
    </div>

    {{-- ── MAIN BODY: Map Left + Panel Right ── --}}
    <div style="display:flex;flex:1;overflow:hidden">

      {{-- ── LEFT: PETA ── --}}
      <div style="flex:1;position:relative;min-width:0">
        <div id="edit-route-map" style="width:100%;height:100%;background:#e8e8e8"></div>

        {{-- Map overlay controls --}}
        <div style="position:absolute;top:12px;right:12px;display:flex;flex-direction:column;gap:8px;z-index:400">
          <button onclick="recenterEditMap()"
            style="width:40px;height:40px;background:white;border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.2);cursor:pointer;display:flex;align-items:center;justify-content:center"
            title="Pusatkan peta">
            <span class="material-icons" style="font-size:20px;color:#1B5E37">my_location</span>
          </button>
        </div>

        {{-- Loading routing indicator --}}
        <div id="routing-loading" style="display:none;position:absolute;bottom:16px;left:50%;transform:translateX(-50%);background:rgba(27,94,55,.9);color:white;padding:8px 16px;border-radius:20px;font-size:12px;font-weight:600;gap:8px;align-items:center;z-index:400">
          <div style="width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:white;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0"></div>
          Menghitung rute...
        </div>
      </div>

      {{-- ── RIGHT: PANEL ── --}}
      <div style="width:380px;flex-shrink:0;display:flex;flex-direction:column;background:#f5f7f6;border-left:1px solid #dde6e0;overflow:hidden">

        {{-- Tabs --}}
        <div style="display:flex;background:white;border-bottom:1px solid #e8e8e8;flex-shrink:0">
          <button id="tab-urutan" onclick="switchEditTab('urutan')"
            style="flex:1;padding:14px 8px;border:none;border-bottom:3px solid #1B5E37;background:none;font-weight:700;font-size:13px;color:#1B5E37;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-family:inherit">
            <span class="material-icons" style="font-size:16px">format_list_numbered</span> Urutan Halte
          </button>
          <button id="tab-pilih" onclick="switchEditTab('pilih')"
            style="flex:1;padding:14px 8px;border:none;border-bottom:3px solid transparent;background:none;font-weight:500;font-size:13px;color:#888;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-family:inherit">
            <span class="material-icons" style="font-size:16px">add_location_alt</span> Pilih Halte
          </button>
        </div>

        {{-- Panel: Urutan Halte --}}
        <div id="panel-urutan" style="flex:1;overflow-y:auto;padding:14px">
          <div id="urutan-halte-list" style="display:flex;flex-direction:column;gap:8px"></div>
          <div id="urutan-empty" style="display:none;text-align:center;padding:60px 20px;color:#aaa">
            <span class="material-icons" style="font-size:56px;color:#ddd">alt_route</span>
            <div style="font-weight:600;margin:10px 0 6px;font-size:15px;color:#888">Belum ada halte dipilih</div>
            <div style="font-size:13px;line-height:1.6">Buka tab <strong>Pilih Halte</strong><br>untuk menambahkan halte ke rute</div>
          </div>
        </div>

        {{-- Panel: Pilih Halte --}}
        <div id="panel-pilih" style="display:none;flex-direction:column;flex:1;overflow:hidden">
          {{-- Search bar --}}
          <div style="padding:12px 14px;background:white;border-bottom:1px solid #eee;flex-shrink:0">
            <div style="position:relative">
              <span class="material-icons" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#aaa;font-size:18px">search</span>
              <input id="edit-halte-search" type="text" placeholder="Cari nama halte..." oninput="filterEditHalteList()"
                style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #dde6e0;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;background:#f9fafb;font-family:inherit">
            </div>
          </div>
          {{-- Halte list --}}
          <div id="all-halte-list" style="flex:1;overflow-y:auto;padding:12px 14px;display:flex;flex-direction:column;gap:8px"></div>
        </div>

        {{-- Save footer --}}
        <div style="padding:14px;background:white;border-top:1px solid #e8e8e8;flex-shrink:0">
          <div style="display:flex;gap:10px;align-items:center">
          <button onclick="closeEditRouteModal()" type="button"
            style="flex:1;padding:13px;border-radius:10px;border:1px solid #ccc;background:white;color:#333;font-weight:700;font-size:14px;cursor:pointer;transition:all .2s;font-family:inherit">
            Batal
          </button>
          <button onclick="saveEditRoute()" id="save-route-btn-bottom"
            style="flex:1;padding:13px;border-radius:10px;border:none;font-weight:700;font-size:14px;cursor:not-allowed;background:#ccc;color:#888;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;font-family:inherit" disabled>
            <span class="material-icons" style="font-size:18px">check_circle</span>
            <span id="save-route-btn-text-2">Pilih minimal 2 halte</span>
          </button>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Siswa --}}
<div class="modal-overlay" id="siswa-modal">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <div class="modal-title">Kelola Siswa - <span id="siswa-bus-name"></span></div>
      <button class="modal-close" onclick="closeModal('siswa-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap">
        <div style="flex:1;min-width:220px">
          <input id="siswa-search-input" type="text" class="form-control" placeholder="Cari nama siswa..." style="width:100%;border-radius:10px;padding:10px" oninput="filterSiswaList()" />
        </div>
        <button class="btn btn-primary btn-sm" onclick="openAddSiswaForm()">+ Tambah Siswa</button>
      </div>
      <div id="siswa-content" style="max-height:400px;overflow-y:auto"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')">Tutup</button>
    </div>
  </div>
</div>

{{-- Modal Tambah Siswa --}}
<div class="modal-overlay" id="add-siswa-modal">
  <div class="modal" style="max-width:500px">
    <div class="modal-header">
      <div class="modal-title">Tambah Siswa ke <span id="add-siswa-bus-name"></span></div>
      <button class="modal-close" onclick="closeModal('add-siswa-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label" style="font-weight:600;color:var(--c-text-dark)">Cari Siswa</label>
        <input id="add-siswa-search-input" type="text" class="form-control" placeholder="Cari nama atau email siswa" style="width:100%;border-radius:10px;padding:10px" oninput="filterAddSiswaSelect()" />
      </div>
      <div class="form-group" style="margin-bottom:14px">
        <div id="add-siswa-search-results" style="border:1px solid #e4e7eb;border-radius:12px;max-height:220px;overflow-y:auto;background:#fff"></div>
        <div id="add-siswa-selected-label" style="margin-top:8px;font-size:13px;color:#333"></div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label" style="font-weight:600;color:var(--c-text-dark)">Pilih Halte</label>
        <select class="form-control" id="select-halte-to-add" style="width:100%;border-radius:10px;padding:10px"></select>
      </div>
    </div>
    <div class="modal-footer" style="justify-content:flex-end;gap:10px">
      <button class="btn btn-outline btn-sm" onclick="closeModal('add-siswa-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="addSiswaToBus()">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Student Detail --}}
<div class="modal-overlay" id="student-detail-modal">
  <div class="modal" style="max-width:520px">
    <div class="modal-header">
      <div class="modal-title">Detail Siswa</div>
      <button class="modal-close" onclick="closeModal('student-detail-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div style="display:flex;gap:14px;align-items:center;margin-bottom:18px;flex-wrap:wrap">
        <div style="width:72px;height:72px;border-radius:18px;overflow:hidden;background:#f0f0f0;flex-shrink:0;">
          <img id="student-detail-photo" src="/images/siswa/default.svg" alt="Foto Siswa" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div style="flex:1;min-width:0;">
          <div id="student-detail-name" style="font-weight:700;font-size:16px;line-height:1.2">Nama Siswa</div>
          <div id="student-detail-email" style="font-size:13px;color:var(--c-text-grey);margin-top:4px;word-break:break-all">Email siswa</div>
        </div>
      </div>
      <div id="student-detail-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:16px">
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">NIS</div>
          <div id="student-detail-nis" style="font-weight:600;color:#111">-</div>
        </div>
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">Kelas</div>
          <div id="student-detail-kelas" style="font-weight:600;color:#111">-</div>
        </div>
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">Sekolah</div>
          <div id="student-detail-sekolah" style="font-weight:600;color:#111">-</div>
        </div>
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">Status</div>
          <div id="student-detail-status" style="font-weight:600;color:#111">-</div>
        </div>
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">Bus</div>
          <div id="student-detail-bus" style="font-weight:600;color:#111">-</div>
        </div>
        <div style="background:#f8f9fa;padding:12px;border-radius:12px">
          <div style="font-size:11px;color:#666;margin-bottom:4px">Halte</div>
          <div id="student-detail-halte" style="font-weight:600;color:#111">-</div>
        </div>
      </div>
      <div style="padding:14px;background:#f8f9fa;border-radius:14px;">
        <div style="font-size:12px;color:#666;margin-bottom:8px;font-weight:600">Alamat</div>
        <div id="student-detail-alamat" style="font-size:13px;color:#222;white-space:pre-line;">-</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-danger btn-sm" onclick="removeStudentFromCurrentBus()">Hapus siswa dari bus</button>
      <button class="btn btn-outline btn-sm" onclick="closeModal('student-detail-modal')">Tutup</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, assignBusId = null, currentPage = 1, currentFilter = 'all';
let currentBusStudents = [];
let currentBusName = '';
let currentStudentDetailId = null;
let addSiswaAvailableStudents = [];
let addSiswaSelectedId = null;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
function jsEscapedString(value) {
  if (value === undefined || value === null) return "''";
  return `'${String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '\\r')}'`;
}
function renderAddSiswaSelect(students) {
  const resultsContainer = document.getElementById('add-siswa-search-results');
  const selectedLabel = document.getElementById('add-siswa-selected-label');
  const searchInput = document.getElementById('add-siswa-search-input');

  searchInput.disabled = false;
  addSiswaSelectedId = null;
  selectedLabel.textContent = 'Belum ada siswa dipilih';
  selectedLabel.style.color = '#6b7280';

  if (!students || students.length === 0) {
    resultsContainer.innerHTML = '<div style="padding:12px;color:#6b7280">Tidak ada siswa tersedia</div>';
    return;
  }

  resultsContainer.innerHTML = '';
  students.forEach(siswa => {
    const item = document.createElement('button');
    item.type = 'button';
    item.style.width = '100%';
    item.style.textAlign = 'left';
    item.style.border = 'none';
    item.style.background = 'transparent';
    item.style.padding = '12px';
    item.style.cursor = 'pointer';
    item.style.display = 'block';
    item.style.borderBottom = '1px solid #f0f0f0';
    item.onmouseover = () => item.style.background = '#f8fafc';
    item.onmouseout = () => item.style.background = 'transparent';
    item.onclick = () => {
      addSiswaSelectedId = siswa.id;
      selectedLabel.textContent = `Siswa dipilih: ${siswa.user?.name || siswa.name || 'N/A'}${siswa.user?.email ? ' — ' + siswa.user.email : ''}`;
      selectedLabel.style.color = '#111827';
      Array.from(resultsContainer.children).forEach(child => child.style.background = 'transparent');
      item.style.background = '#eef2ff';
    };

    const name = siswa.user?.name || siswa.name || 'N/A';
    const email = siswa.user?.email || siswa.email || '';
    item.textContent = email ? `${name} — ${email}` : name;
    resultsContainer.appendChild(item);
  });
}
function filterAddSiswaSelect() {
  const query = document.getElementById('add-siswa-search-input').value.trim().toLowerCase();
  const filtered = addSiswaAvailableStudents.filter(siswa => {
    const name = (siswa.user?.name || siswa.name || '').toLowerCase();
    const email = (siswa.user?.email || siswa.email || '').toLowerCase();
    return name.includes(query) || email.includes(query);
  });
  renderAddSiswaSelect(filtered);
}

function setBusFilter(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadBus(1);
}

async function loadBus(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/buses', { search: q, per_page: 1000, _t: Date.now() });
  
  let rows = res.data?.data ?? [];
  
  // Apply filter
  if (currentFilter !== 'all') {
    if (currentFilter === 'no-driver') {
      const today = new Date().toISOString().split('T')[0];
      rows = rows.filter(b => !(b.drivers ?? []).some(d => !d.pivot?.tanggal_selesai || d.pivot?.tanggal_selesai >= today));
    } else {
      rows = rows.filter(b => b.status === currentFilter);
    }
  }
  
  const tbody = document.getElementById('bus-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><span class="material-icons">directions_bus</span><p>Tidak ada bus</p></div></td></tr>`;
    document.getElementById('bus-pagination').innerHTML = ''; return;
  }
  
  const perPage = 15;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);
  
  tbody.innerHTML = paginatedRows.map((b, i) => {
    const activeDriver = (b.drivers ?? []).find(d => !d.pivot?.tanggal_selesai || d.pivot?.tanggal_selesai >= new Date().toISOString().split('T')[0]);
    const driverLabel = activeDriver ? `<span style="font-size:12px;padding:4px 10px;border-radius:999px;background:#e7f5e6;color:#1b5e20">${activeDriver.user?.name ?? activeDriver.name ?? 'Driver Aktif'}</span>` : `<span style="font-size:12px;padding:4px 10px;border-radius:999px;background:#fff4e5;color:#b15f00">No Driver</span>`;
    return `
    <tr>
      <td style="width:80px">
        ${b.photo_url ? `<img src="${proxyImgUrl(b.photo_url)}" style="width:60px;height:60px;object-fit:cover;border-radius:6px" alt="${b.kode_bus}">` : `<div style="width:60px;height:60px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center"><span class="material-icons" style="color:#ccc">image</span></div>`}
      </td>
      <td><span style="font-weight:700;color:var(--c-primary)">${b.kode_bus}</span></td>
      <td>${b.plat_nomor}</td>
      <td>${statusBadge(b.status)}</td>
      <td><span class="badge ${b.gps_status === 'on' ? 'badge-green':'badge-grey'}">${b.gps_status === 'on' ? 'ON':'OFF'}</span></td>
      <td>${driverLabel}</td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap;align-items:center">
          <button type="button" class="btn btn-xs btn-outline" onclick="editBus(${b.id})">Edit</button>
          <button type="button" class="btn btn-xs" style="background:var(--c-primary);color:white" onclick="openRouteHalte(${b.id}, ${jsEscapedString(b.kode_bus)})">Rute</button>
          <button type="button" class="btn btn-xs" style="background:#E3F0FB;color:var(--c-primary)" onclick="openSiswa(${b.id}, ${jsEscapedString(b.kode_bus)})">Siswa</button>
          <button type="button" class="btn btn-xs" style="background:#E3F0FB;color:var(--c-blue)" onclick="openAssign(${b.id})">Driver</button>
          <button type="button" class="btn btn-xs btn-icon" onclick="deleteBus(${b.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`;
  }).join('');
  
  const totalPages = Math.ceil(rows.length / perPage);
  const paginationHtml = totalPages > 1 ? `
    <div style="display:flex;justify-content:center;gap:4px;padding:8px">
      ${page > 1 ? `<button class="btn btn-sm btn-outline" onclick="loadBus(${page - 1})">← Sebelumnya</button>` : ''}
      <span style="padding:8px 12px">Halaman ${page} dari ${totalPages}</span>
      ${page < totalPages ? `<button class="btn btn-sm btn-outline" onclick="loadBus(${page + 1})">Berikutnya →</button>` : ''}
    </div>
  ` : '';
  document.getElementById('bus-pagination').innerHTML = paginationHtml;
}

function removePhotoSelection(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById('bus-photo-input');
  if (input) input.value = '';
  document.getElementById('bus-photo-img').src = '';
  document.getElementById('bus-photo-preview').style.display = 'none';
  document.getElementById('bus-photo-upload-zone').style.display = 'flex';
}

function openAddModal() {
  editId = null;
  document.getElementById('bus-modal-title').textContent = 'Tambah Bus';
  document.getElementById('bus-form').reset();
  removePhotoSelection();
  openModal('bus-modal');
}

async function editBus(id) {
  editId = id;
  document.getElementById('bus-modal-title').textContent = 'Edit Bus';
  const res = await api.get('/buses/' + id);
  const b = res.data?.data;
  const f = document.getElementById('bus-form');
  f.kode_bus.value = b.kode_bus ?? '';
  f.plat_nomor.value = b.plat_nomor ?? '';
  f.status.value = b.status ?? 'aktif';
  
  // Show photo preview if exists
  if (b.photo_url) {
    document.getElementById('bus-photo-img').src = proxyImgUrl(b.photo_url);
    document.getElementById('bus-photo-preview').style.display = 'block';
    document.getElementById('bus-photo-upload-zone').style.display = 'none';
  } else {
    removePhotoSelection();
  }
  openModal('bus-modal');
}

async function saveBus() {
  const f = document.getElementById('bus-form');
  
  // Prepare FormData with all fields including photo
  const formData = new FormData();
  formData.append('kode_bus', f.kode_bus.value);
  formData.append('plat_nomor', f.plat_nomor.value);
  formData.append('status', f.status.value);
  
  // Add photo file if selected
  if (f.photo.files.length > 0) {
    formData.append('photo', f.photo.files[0]);
  }
  
  // Untuk update (edit): gunakan POST + _method=PUT (Laravel method spoofing)
  // Ini diperlukan karena PHP tidak bisa baca file upload dari PUT request
  // (PHP hanya populate $_FILES untuk POST) — sehingga $request->hasFile('photo')
  // selalu false kalau pakai putForm/HTTP PUT murni.
  if (editId) {
    formData.append('_method', 'PUT');
  }
  const res = editId ?
    await api.postForm('/buses/' + editId, formData) :
    await api.postForm('/buses', formData);
    
  if (!res.ok) {
    toast(res.data?.message ?? 'Gagal menyimpan bus', 'error');
    return;
  }
  
  toast('Bus berhasil disimpan');
  closeModal('bus-modal');
  loadBus(currentPage);
}

let currentBusDriverAssignmentId = null;

async function openAssign(busId) {
  assignBusId = busId;
  currentBusDriverAssignmentId = null;
  const statusEl = document.getElementById('assign-driver-status');
  const detailEl = document.getElementById('assign-driver-detail');
  const searchInput = document.getElementById('assign-driver-search-input');
  const resultsContainer = document.getElementById('assign-driver-search-results');
  const selectedLabel = document.getElementById('assign-driver-selected-label');
  const hiddenDriverId = document.getElementById('assign-driver-id');
  const submitBtn = document.getElementById('assign-submit-button');
  const replaceBtn = document.getElementById('assign-replace-button');
  const startInput = document.getElementById('assign-start');
  const finishCheckbox = document.getElementById('assign-finish-checkbox');
  const finishGroup = document.getElementById('assign-finish-group');
  const finishInput = document.getElementById('assign-finish');
  const searchPanel = document.getElementById('assign-driver-search-panel');
  const resultsPanel = document.getElementById('assign-driver-search-results-panel');

  statusEl.textContent = 'Memuat data driver...';
  detailEl.style.display = 'none';
  detailEl.innerHTML = '';
  selectedLabel.textContent = 'Belum ada driver dipilih';
  selectedLabel.style.color = '#6b7280';
  hiddenDriverId.value = '';
  searchInput.value = '';
  resultsContainer.innerHTML = '<div style="padding:12px;color:#6b7280">Memuat daftar driver...</div>';
  startInput.value = new Date().toISOString().split('T')[0];
  finishCheckbox.checked = false;
  finishGroup.style.display = 'none';
  finishInput.value = '';
  submitBtn.textContent = 'Simpan';
  submitBtn.onclick = saveAssign;
  replaceBtn.style.display = 'none';
  searchPanel.style.display = 'none';
  resultsPanel.style.display = 'none';

    try {
    // cache-bust to avoid stale responses and add debug logs for investigation
    const cacheBust = { _t: Date.now() };
    const [driverRes, drRes] = await Promise.all([
      api.get(`/buses/${busId}/driver`, cacheBust, true),
      api.get('/drivers', Object.assign({ per_page: 100 }, cacheBust), true)
    ]);
    console.debug('[openAssign] driverRes:', driverRes, 'driversRes:', drRes);

    const assignedDriver = driverRes.ok ? driverRes.data?.data ?? driverRes.data : null;
    const driverData = drRes.data?.data?.data ?? drRes.data?.data ?? drRes.data;
    const drivers = Array.isArray(driverData) ? driverData : [];
    const today = new Date().toISOString().split('T')[0];
    const availableDrivers = drivers.filter(d => {
      const hasActiveBus = (d.buses ?? []).some(b => {
        const end = b.pivot?.tanggal_selesai;
        return !end || end >= today;
      });
      return !hasActiveBus;
    });

    window.assignDriverCandidates = availableDrivers;
    renderAssignDriverSearchResults(availableDrivers);

    if (assignedDriver) {
      currentBusDriverAssignmentId = assignedDriver.pivot?.id ?? null;
      const currentName = assignedDriver.user?.name ?? assignedDriver.name ?? 'Driver aktif';
      const currentNik = assignedDriver.nik ?? assignedDriver.user?.nik ?? '-';
      const phone = assignedDriver.no_hp ?? assignedDriver.user?.no_hp ?? '-';
      const email = assignedDriver.user?.email ?? assignedDriver.email ?? '-';
      const startDate = assignedDriver.pivot?.tanggal_mulai ?? assignedDriver.tanggal_mulai ?? today;
      const endDate = assignedDriver.pivot?.tanggal_selesai ?? '';
      const status = assignedDriver.pivot?.gps_status ?? '-';

      statusEl.textContent = `Driver aktif: ${currentName}`;
      detailEl.style.display = 'block';
      detailEl.innerHTML = `
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:14px">
          <div style="width:64px;height:64px;border-radius:16px;overflow:hidden;background:#f0f0f0;flex-shrink:0">
            <img src="${assignedDriver.user?.photo_url ? proxyImgUrl(assignedDriver.user.photo_url) : '/images/driver/default.svg'}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='/images/driver/default.svg'" />
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:15px;font-weight:700">${currentName}</div>
            <div style="font-size:13px;color:#4b5563;">${email}</div>
            <div style="font-size:13px;color:#4b5563;">${phone}</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;color:#334155;margin-bottom:14px">
          <div><strong>NIK</strong><div>${currentNik}</div></div>
          <div><strong>Status GPS</strong><div>${status}</div></div>
          <div><strong>Tanggal Mulai</strong><div>${startDate}</div></div>
          <div><strong>Tanggal Selesai</strong><div>${endDate || '-'}</div></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <a href="https://wa.me/${formatWhatsAppNumber(phone)}" target="_blank" class="btn btn-primary btn-sm" style="background:#25D366;border-color:#25D366;color:white;display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;">
            <span class="material-icons" style="font-size:18px">chat</span> WhatsApp
          </a>
          <button type="button" class="btn btn-outline btn-sm" onclick="copyAssignDriverNumber('${phone}')" style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;">
            <span class="material-icons" style="font-size:18px">content_copy</span> Salin Nomor
          </button>
        </div>
      `;
      document.getElementById('assign-driver-search-toggle').style.display = 'none';
      replaceBtn.style.display = 'inline-flex';
      startInput.value = startDate;
      if (endDate) {
        finishCheckbox.checked = true;
        finishGroup.style.display = 'block';
        finishInput.value = endDate;
      } else {
        finishCheckbox.checked = false;
        finishGroup.style.display = 'none';
        finishInput.value = '';
      }
    } else {
      statusEl.textContent = 'Bus ini belum ada driver. Tetapkan driver:';
      detailEl.style.display = 'none';
      submitBtn.textContent = 'Simpan';
      submitBtn.onclick = saveAssign;
      replaceBtn.style.display = 'none';
      document.getElementById('assign-driver-search-toggle').style.display = 'block';
      startInput.value = today;
      finishCheckbox.checked = false;
      finishGroup.style.display = 'none';
      finishInput.value = '';
    }

    openModal('assign-modal');
  } catch (e) {
    console.error(e);
    statusEl.textContent = 'Gagal memuat data driver';
    resultsContainer.innerHTML = '<div style="padding:12px;color:#b91c1c">Gagal mencari driver</div>';
    openModal('assign-modal');
  }
}

function renderAssignDriverSearchResults(drivers) {
  const container = document.getElementById('assign-driver-search-results');
  const selectedLabel = document.getElementById('assign-driver-selected-label');
  const hiddenDriverId = document.getElementById('assign-driver-id');
  container.innerHTML = '';
  if (!drivers.length) {
    container.innerHTML = '<div style="padding:12px;color:#6b7280">Tidak ada driver tersedia untuk ditugaskan</div>';
    hiddenDriverId.value = '';
    selectedLabel.textContent = 'Belum ada driver dipilih';
    selectedLabel.style.color = '#6b7280';
    return;
  }

  drivers.forEach(driver => {
    const item = document.createElement('button');
    item.type = 'button';
    item.style.width = '100%';
    item.style.textAlign = 'left';
    item.style.border = 'none';
    item.style.background = 'transparent';
    item.style.padding = '14px';
    item.style.cursor = 'pointer';
    item.style.display = 'block';
    item.style.borderBottom = '1px solid #f0f0f0';
    item.onmouseover = () => item.style.background = '#f8fafc';
    item.onmouseout = () => item.style.background = 'transparent';
    item.onclick = () => {
      hiddenDriverId.value = driver.id;
      selectedLabel.textContent = `Driver dipilih: ${driver.user?.name ?? driver.name ?? 'N/A'}${driver.no_hp ? ' — ' + driver.no_hp : ''}`;
      selectedLabel.style.color = '#111827';
      Array.from(container.children).forEach(child => child.style.background = 'transparent');
      item.style.background = '#eef2ff';
    };

    const name = driver.user?.name ?? driver.name ?? '-';
    const email = driver.user?.email ?? driver.email ?? '-';
    const phone = driver.no_hp ?? '-';
    item.innerHTML = `
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <div style="min-width:0;">
          <div style="font-weight:700;color:#111;margin-bottom:4px">${name}</div>
          <div style="font-size:12px;color:#6b7280;">${email}</div>
          <div style="font-size:12px;color:#6b7280;">${phone}</div>
        </div>
        <span style="font-size:12px;padding:4px 10px;border-radius:999px;background:#eef2ff;color:#0f3d22;">Pilih</span>
      </div>
    `;
    container.appendChild(item);
  });
}

  function showAssignDriverSearchPanel() {
    document.getElementById('assign-driver-search-toggle').style.display = 'none';
    document.getElementById('assign-driver-search-panel').style.display = 'block';
    document.getElementById('assign-driver-search-results-panel').style.display = 'block';
    document.getElementById('assign-driver-search-input').focus();
  }

function filterAssignDriverSearch() {
  const query = document.getElementById('assign-driver-search-input').value.trim().toLowerCase();
  const filtered = (window.assignDriverCandidates ?? []).filter(d => {
    const name = (d.user?.name ?? d.name ?? '').toLowerCase();
    const email = (d.user?.email ?? d.email ?? '').toLowerCase();
    const noHp = (d.no_hp ?? '').toLowerCase();
    const nik = (d.nik ?? d.user?.nik ?? '').toLowerCase();
    return name.includes(query) || email.includes(query) || noHp.includes(query) || nik.includes(query);
  });
  renderAssignDriverSearchResults(filtered);
}

function formatWhatsAppNumber(phone) {
  if (!phone) return '';
  let cleaned = phone.replace(/[^0-9]/g, '');
  if (cleaned.startsWith('0')) {
    cleaned = '62' + cleaned.substring(1);
  }
  return cleaned;
}

function copyAssignDriverNumber(phone) {
  if (!phone) {
    toast('Nomor telepon tidak tersedia', 'error');
    return;
  }
  navigator.clipboard.writeText(phone).then(() => {
    toast('Nomor telepon berhasil disalin');
  }).catch(() => {
    toast('Gagal menyalin nomor telepon', 'error');
  });
}

function renderReplaceDriverSearchResults(drivers) {
  const container = document.getElementById('replace-driver-search-results');
  const selectedLabel = document.getElementById('replace-driver-selected-label');
  const hiddenDriverId = document.getElementById('replace-driver-id');
  container.innerHTML = '';
  if (!drivers.length) {
    container.innerHTML = '<div style="padding:12px;color:#6b7280">Tidak ada driver tersedia untuk ditugaskan</div>';
    hiddenDriverId.value = '';
    selectedLabel.textContent = 'Belum ada driver dipilih';
    selectedLabel.style.color = '#6b7280';
    return;
  }

  drivers.forEach(driver => {
    const item = document.createElement('button');
    item.type = 'button';
    item.style.width = '100%';
    item.style.textAlign = 'left';
    item.style.border = 'none';
    item.style.background = 'transparent';
    item.style.padding = '14px';
    item.style.cursor = 'pointer';
    item.style.display = 'block';
    item.style.borderBottom = '1px solid #f0f0f0';
    item.onmouseover = () => item.style.background = '#f8fafc';
    item.onmouseout = () => item.style.background = 'transparent';
    item.onclick = () => {
      hiddenDriverId.value = driver.id;
      selectedLabel.textContent = `Driver dipilih: ${driver.user?.name ?? driver.name ?? 'N/A'}${driver.no_hp ? ' — ' + driver.no_hp : ''}`;
      selectedLabel.style.color = '#111827';
      Array.from(container.children).forEach(child => child.style.background = 'transparent');
      item.style.background = '#eef2ff';
    };

    const name = driver.user?.name ?? driver.name ?? '-';
    const email = driver.user?.email ?? driver.email ?? '-';
    const phone = driver.no_hp ?? '-';
    item.innerHTML = `
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <div style="min-width:0;">
          <div style="font-weight:700;color:#111;margin-bottom:4px">${name}</div>
          <div style="font-size:12px;color:#6b7280;">${email}</div>
          <div style="font-size:12px;color:#6b7280;">${phone}</div>
        </div>
        <span style="font-size:12px;padding:4px 10px;border-radius:999px;background:#eef2ff;color:#0f3d22;">Pilih</span>
      </div>
    `;
    container.appendChild(item);
  });
}

function filterReplaceDriverSearch() {
  const query = document.getElementById('replace-driver-search-input').value.trim().toLowerCase();
  const filtered = (window.assignDriverCandidates ?? []).filter(d => {
    const name = (d.user?.name ?? d.name ?? '').toLowerCase();
    const email = (d.user?.email ?? d.email ?? '').toLowerCase();
    const noHp = (d.no_hp ?? '').toLowerCase();
    const nik = (d.nik ?? d.user?.nik ?? '').toLowerCase();
    return name.includes(query) || email.includes(query) || noHp.includes(query) || nik.includes(query);
  });
  renderReplaceDriverSearchResults(filtered);
}

function openReplaceDriverModal() {
  // assumes assignDriverCandidates already set by openAssign
  document.getElementById('replace-driver-search-input').value = '';
  document.getElementById('replace-driver-search-results').innerHTML = '';
  renderReplaceDriverSearchResults(window.assignDriverCandidates ?? []);
  document.getElementById('replace-driver-selected-label').textContent = 'Belum ada driver dipilih';
  document.getElementById('replace-driver-id').value = '';
  document.getElementById('replace-start').value = new Date().toISOString().split('T')[0];
  document.getElementById('replace-finish-checkbox').checked = false;
  document.getElementById('replace-finish-group').style.display = 'none';
  openModal('replace-driver-modal');
}

function toggleReplaceFinishDate() {
  const finishGroup = document.getElementById('replace-finish-group');
  const checked = document.getElementById('replace-finish-checkbox').checked;
  finishGroup.style.display = checked ? 'block' : 'none';
}

async function saveReplaceAssign() {
  const driverId = document.getElementById('replace-driver-id').value;
  const startDate = document.getElementById('replace-start').value;
  const finishCheck = document.getElementById('replace-finish-checkbox').checked;
  const finishDate = document.getElementById('replace-finish').value;

  if (!driverId) { toast('Pilih driver terlebih dahulu', 'warn'); return; }
  if (!startDate) { toast('Tanggal mulai wajib diisi', 'warn'); return; }
  if (finishCheck && !finishDate) { toast('Silakan pilih tanggal selesai', 'warn'); return; }

  const payload = { driver_id: driverId, tanggal_mulai: startDate };
  payload.tanggal_selesai = finishCheck ? finishDate : null;

  const res = await api.post('/buses/' + assignBusId + '/drivers', payload);
    if (res.ok) {
      console.debug('[saveReplaceAssign] success response:', res);
      toast('Driver berhasil diganti');
      closeModal('replace-driver-modal');

      // Ensure driver is not active on other buses (1 driver ↔ 1 bus)
      try {
        const newDriverId = document.getElementById('replace-driver-id').value || res.data?.data?.id || res.data?.data?.driver_id || null;
        if (newDriverId) {
          const today = new Date().toISOString().split('T')[0];
          const allBusesRes = await api.get('/buses', { per_page: 1000, _t: Date.now() });
          const allBuses = allBusesRes.data?.data ?? allBusesRes.data ?? [];
          const promises = [];
          allBuses.forEach(b => {
            if (b.id === assignBusId) return;
            (b.drivers ?? []).forEach(d => {
              const did = d.id ?? d.user?.id ?? d.driver_id ?? null;
              const pivotId = d.pivot?.id ?? d.pivot_id ?? null;
              const end = d.pivot?.tanggal_selesai;
              const active = !end || end >= today;
              if (pivotId && active && (did == newDriverId || d.id == newDriverId)) {
                // set tanggal_selesai ke hari ini untuk menonaktifkan
                promises.push(api.put('/bus-driver/' + pivotId, { tanggal_selesai: today }));
              }
            });
          });
          await Promise.all(promises);
        }
      } catch (err) { console.debug('[saveReplaceAssign] cleanup error', err); }

      // re-open assign modal and refresh list
      await openAssign(assignBusId);
      loadBus(currentPage);
  } else {
    console.debug('[saveReplaceAssign] error response:', res);
    toast(res.data?.message ?? 'Gagal mengganti driver', 'error');
  }
}

async function saveAssign() {
  const driverId = document.getElementById('assign-driver-id').value;
  const startDate = document.getElementById('assign-start').value;
  const finishCheck = document.getElementById('assign-finish-checkbox').checked;
  const finishDate = document.getElementById('assign-finish').value;

  if (!startDate) { toast('Tanggal mulai wajib diisi', 'warn'); return; }
  if (finishCheck && !finishDate) { toast('Silakan pilih tanggal selesai', 'warn'); return; }

  const payload = {
    tanggal_mulai: startDate,
    tanggal_selesai: finishCheck ? finishDate : null,
  };

  let res;
  if (currentBusDriverAssignmentId) {
    res = await api.put('/bus-driver/' + currentBusDriverAssignmentId, payload);
    if (res.ok) {
      toast('Data driver berhasil disimpan');
      closeModal('assign-modal');
      loadBus(currentPage);
      return;
    }
    toast(res.data?.message ?? 'Gagal memperbarui data driver', 'error');
    return;
  }

  if (!driverId) { toast('Pilih driver terlebih dahulu', 'warn'); return; }
  payload.driver_id = driverId;
  res = await api.post('/buses/' + assignBusId + '/drivers', payload);
  if (res.ok) {
    console.debug('[saveAssign] success response:', res);
    toast('Driver berhasil di-assign');
    closeModal('assign-modal');

    // Ensure driver is not active on other buses (1 driver ↔ 1 bus)
    try {
      const newDriverId = payload.driver_id || res.data?.data?.id || res.data?.data?.driver_id || null;
      if (newDriverId) {
        const today = new Date().toISOString().split('T')[0];
        const allBusesRes = await api.get('/buses', { per_page: 1000, _t: Date.now() });
        const allBuses = allBusesRes.data?.data ?? allBusesRes.data ?? [];
        const promises = [];
        allBuses.forEach(b => {
          if (b.id === assignBusId) return;
          (b.drivers ?? []).forEach(d => {
            const did = d.id ?? d.user?.id ?? d.driver_id ?? null;
            const pivotId = d.pivot?.id ?? d.pivot_id ?? null;
            const end = d.pivot?.tanggal_selesai;
            const active = !end || end >= today;
            if (pivotId && active && (did == newDriverId || d.id == newDriverId)) {
              promises.push(api.put('/bus-driver/' + pivotId, { tanggal_selesai: today }));
            }
          });
        });
        await Promise.all(promises);
      }
    } catch (err) { console.debug('[saveAssign] cleanup error', err); }

    // reload modal & list with fresh data
    await openAssign(assignBusId);
    loadBus(currentPage);
  } else {
    console.debug('[saveAssign] error response:', res);
    toast(res.data?.message ?? 'Gagal', 'error');
  }
}

function toggleAssignFinishDate() {
  const finishGroup = document.getElementById('assign-finish-group');
  const checked = document.getElementById('assign-finish-checkbox').checked;
  finishGroup.style.display = checked ? 'block' : 'none';
}

async function deleteBus(id) {
  confirmDialog('Hapus bus ini?', async () => {
    const r = await api.delete('/buses/' + id);
    r.ok ? (toast('Bus dihapus', 'warn'), loadBus(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

// ──── Route & Halte ────
let currentBusId = null;
let routeMapInstance = null;
let currentRouteData = null;

async function openRouteHalte(busId, busName) {
  currentBusId = busId;
  editingBusName = busName;

  const res = await api.get(`/buses/${busId}/route`);
  currentRouteData = res.data?.data ?? null;

  // Open edit route view directly
  allHaltesCache = [];
  openEditRouteModal();
}

// ──── Edit Route Modal ────
let editRouteSelectedHaltes = []; // [{id, nama_halte, alamat/deskripsi, latitude, longitude, urutan}]
let allHaltesCache = [];
let editRouteMapInstance = null;
let editingBusName = '';
let dragSrcIndex = null;

function openEditRouteModal() {
  // Reset state
  editRouteSelectedHaltes = [];
  
  // Pre-fill from existing route
  if (currentRouteData && currentRouteData.haltes && currentRouteData.haltes.length > 0) {
    editRouteSelectedHaltes = currentRouteData.haltes
      .sort((a, b) => a.urutan - b.urutan)
      .map(rh => ({
        id: rh.halte?.id ?? rh.halte_id,
        nama_halte: rh.halte?.nama_halte ?? 'N/A',
        alamat: rh.halte?.deskripsi ?? rh.halte?.alamat ?? '',
        latitude: rh.halte?.latitude,
        longitude: rh.halte?.longitude,
      }));
  }

  document.getElementById('edit-route-title').textContent = 'Ubah Rute [' + editingBusName + ']';

  openModal('edit-route-modal');
  switchEditTab('urutan');
  renderUrutanHalte();
  updateSaveBtn();

  // Load all haltes
  loadAllHaltesForEdit();

  // Init map
  setTimeout(() => {
    if (editRouteMapInstance) { editRouteMapInstance.remove(); editRouteMapInstance = null; }
    const el = document.getElementById('edit-route-map');
    editRouteMapInstance = L.map(el, { attributionControl: false, zoomControl: false }).setView([-7.6288, 111.5305], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(editRouteMapInstance);
    L.control.zoom({ position: 'topleft' }).addTo(editRouteMapInstance);
    refreshEditRouteMap();
  }, 150);
}

function closeEditRouteModal() {
  closeModal('edit-route-modal');
  if (editRouteMapInstance) { editRouteMapInstance.remove(); editRouteMapInstance = null; }
}

async function editRoute() {
  // Ambil nama bus dari title modal (strip prefix jika ada)
  const titleEl = document.getElementById('route-title-text');
  editingBusName = (titleEl?.textContent ?? '')
    .replace('Rute & Halte - ', '')
    .replace('Rute & Halte', '')
    .trim() || 'Bus';

  // Tutup modal sebelumnya dulu agar tidak bertumpuk
  closeModal('route-halte-modal');

  // Cache haltes reset agar selalu fresh saat buka
  allHaltesCache = [];

  openEditRouteModal();
}

function switchEditTab(tab) {
  const isUrutan = tab === 'urutan';
  document.getElementById('panel-urutan').style.display = isUrutan ? 'block' : 'none';
  document.getElementById('panel-pilih').style.display = isUrutan ? 'none' : 'flex';
  document.getElementById('panel-pilih').style.flexDirection = isUrutan ? '' : 'column';

  const tu = document.getElementById('tab-urutan');
  const tp = document.getElementById('tab-pilih');
  tu.style.borderBottomColor = isUrutan ? '#2d6a4f' : 'transparent';
  tu.style.color = isUrutan ? '#2d6a4f' : '#888';
  tu.style.fontWeight = isUrutan ? '600' : '500';
  tp.style.borderBottomColor = isUrutan ? 'transparent' : '#2d6a4f';
  tp.style.color = isUrutan ? '#888' : '#2d6a4f';
  tp.style.fontWeight = isUrutan ? '500' : '600';

  if (!isUrutan) filterEditHalteList();
}

function renderUrutanHalte() {
  const list = document.getElementById('urutan-halte-list');
  const empty = document.getElementById('urutan-empty');
  if (editRouteSelectedHaltes.length === 0) {
    list.innerHTML = '';
    empty.style.display = 'block';
  } else {
    empty.style.display = 'none';
    list.innerHTML = editRouteSelectedHaltes.map((h, i) => `
      <div class="urutan-item" draggable="true" data-index="${i}"
        ondragstart="onDragStart(event,${i})" ondragover="onDragOver(event)" ondrop="onDrop(event,${i})" ondragend="onDragEnd()"
        style="display:flex;align-items:center;gap:10px;padding:12px;background:#f9f9f9;border:1px solid #e8e8e8;border-radius:10px;cursor:grab">
        <div style="width:32px;height:32px;border-radius:50%;background:#2d6a4f;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0">${i+1}</div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${h.nama_halte}</div>
          <div style="font-size:12px;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${h.alamat || 'Tidak ada deskripsi'}</div>
        </div>
        <div style="display:flex;align-items:center;gap:4px">
          <button onclick="removeHalteFromRoute(${i})" style="background:none;border:none;cursor:pointer;color:#d32f2f;padding:4px;display:flex">
            <span class="material-icons" style="font-size:18px">remove_circle_outline</span>
          </button>
          <span class="material-icons" style="font-size:20px;color:#bbb">drag_indicator</span>
        </div>
      </div>`).join('');
  }
  refreshEditRouteMap();
  updateSaveBtn();
}

function onDragStart(e, i) { dragSrcIndex = i; e.dataTransfer.effectAllowed = 'move'; }
function onDragOver(e) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; }
function onDrop(e, targetIndex) {
  e.preventDefault();
  if (dragSrcIndex === null || dragSrcIndex === targetIndex) return;
  const moved = editRouteSelectedHaltes.splice(dragSrcIndex, 1)[0];
  editRouteSelectedHaltes.splice(targetIndex, 0, moved);
  dragSrcIndex = null;
  renderUrutanHalte();
}
function onDragEnd() { dragSrcIndex = null; }

function removeHalteFromRoute(index) {
  editRouteSelectedHaltes.splice(index, 1);
  renderUrutanHalte();
  filterEditHalteList(); // refresh checkmarks
}

function addHalteToRoute(halte) {
  const already = editRouteSelectedHaltes.find(h => h.id === halte.id);
  if (already) {
    removeHalteFromRoute(editRouteSelectedHaltes.indexOf(already));
    return;
  }
  editRouteSelectedHaltes.push({
    id: halte.id,
    nama_halte: halte.nama_halte,
    alamat: halte.deskripsi ?? halte.alamat ?? '',
    latitude: halte.latitude,
    longitude: halte.longitude,
  });
  renderUrutanHalte();
  filterEditHalteList();
}

async function loadAllHaltesForEdit() {
  if (allHaltesCache.length === 0) {
    const res = await api.get('/haltes', { per_page: 1000 });
    allHaltesCache = res.data?.data ?? [];
  }
  filterEditHalteList();
}

function filterEditHalteList() {
  const q = (document.getElementById('edit-halte-search')?.value ?? '').toLowerCase();
  const filtered = allHaltesCache.filter(h => h.nama_halte.toLowerCase().includes(q) || (h.deskripsi ?? '').toLowerCase().includes(q));
  const container = document.getElementById('all-halte-list');
  if (!container) return;
  if (!filtered.length) {
    container.innerHTML = `<div style="text-align:center;padding:24px;color:#888;font-size:13px">Tidak ada halte ditemukan</div>`;
    return;
  }
  container.innerHTML = filtered.map(h => {
    const selected = editRouteSelectedHaltes.some(s => s.id === h.id);
    return `
      <div onclick="addHalteToRoute(${JSON.stringify(h).replace(/"/g,'&quot;')})"
        style="display:flex;align-items:center;gap:12px;padding:12px;background:#f9f9f9;border:1px solid ${selected ? '#2d6a4f' : '#e8e8e8'};border-radius:10px;cursor:pointer;transition:border-color .2s">
        <div style="width:28px;height:28px;border-radius:50%;border:2px solid ${selected ? '#2d6a4f' : '#ccc'};display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;background:${selected ? '#2d6a4f' : 'transparent'}">
          ${selected ? '<span class="material-icons" style="font-size:14px;color:white">check</span>' : '<span class="material-icons" style="font-size:14px;color:#ccc">add</span>'}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:14px">${h.nama_halte}</div>
          <div style="font-size:12px;color:#888">${h.deskripsi ?? '-'}</div>
        </div>
        <button onclick="event.stopPropagation();locateHalteOnMap(${h.latitude},${h.longitude})" 
          style="background:none;border:none;cursor:pointer;color:#888;padding:4px;display:flex">
          <span class="material-icons" style="font-size:18px">my_location</span>
        </button>
      </div>`;
  }).join('');
}

function locateHalteOnMap(lat, lng) {
  if (!editRouteMapInstance) return;
  editRouteMapInstance.setView([parseFloat(lat), parseFloat(lng)], 16);
  switchEditTab('urutan');
}

function recenterEditMap() {
  if (!editRouteMapInstance) return;
  if (editRouteSelectedHaltes.length > 0) {
    const coords = editRouteSelectedHaltes.filter(h => h.latitude && h.longitude).map(h => [parseFloat(h.latitude), parseFloat(h.longitude)]);
    if (coords.length) editRouteMapInstance.fitBounds(L.latLngBounds(coords), { padding: [30, 30] });
  } else {
    editRouteMapInstance.setView([-7.6288, 111.5305], 13);
  }
}

let _routePolylineLayer = null;

async function refreshEditRouteMap() {
  if (!editRouteMapInstance) return;

  // Clear all non-tile layers
  editRouteMapInstance.eachLayer(layer => {
    if (!(layer instanceof L.TileLayer)) editRouteMapInstance.removeLayer(layer);
  });
  _routePolylineLayer = null;

  const haltes = editRouteSelectedHaltes.filter(h => h.latitude && h.longitude);
  if (haltes.length === 0) return;

  const colors = ['#4CAF50','#F44336','#2196F3','#FF9800','#9C27B0','#00BCD4','#795548','#607D8B'];

  // Plot halte markers
  const coords = haltes.map((h, i) => {
    const latlng = [parseFloat(h.latitude), parseFloat(h.longitude)];
    // Outer circle
    L.circleMarker(latlng, {
      radius: 14, fillColor: colors[i % colors.length],
      color: '#fff', weight: 2.5, fillOpacity: 1
    }).addTo(editRouteMapInstance);
    // Number label
    L.marker(latlng, {
      icon: L.divIcon({
        html: `<div style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;font-family:Poppins,sans-serif;margin-top:-2px">${i+1}</div>`,
        iconSize: [20, 20], className: ''
      })
    }).addTo(editRouteMapInstance);
    return latlng;
  });

  // Fit bounds on markers first
  if (coords.length === 1) {
    editRouteMapInstance.setView(coords[0], 15);
  } else {
    editRouteMapInstance.fitBounds(L.latLngBounds(coords), { padding: [30, 30] });
  }

  // Draw road route via OSRM (only if 2+ haltes)
  if (haltes.length >= 2) {
    // Build OSRM waypoints string: "lon,lat;lon,lat;..."
    const waypoints = haltes.map(h => `${parseFloat(h.longitude)},${parseFloat(h.latitude)}`).join(';');
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;

    // Show loading indicator
    const loadingEl = document.getElementById('routing-loading');
    if (loadingEl) { loadingEl.style.display = 'flex'; }

    try {
      const resp = await fetch(osrmUrl);
      const data = await resp.json();
      if (data.code === 'Ok' && data.routes?.[0]?.geometry) {
        const route = data.routes[0];
        const geojsonCoords = route.geometry.coordinates.map(c => [c[1], c[0]]);
        _routePolylineLayer = L.polyline(geojsonCoords, {
          color: '#1B5E37', weight: 4.5, opacity: 0.9,
          lineJoin: 'round', lineCap: 'round'
        }).addTo(editRouteMapInstance);
        editRouteMapInstance.fitBounds(_routePolylineLayer.getBounds(), { padding: [40, 40] });

        // Show distance badge
        const distKm = (route.distance / 1000).toFixed(1);
        const badge = document.getElementById('edit-route-info-badge');
        const distEl = document.getElementById('edit-route-distance');
        if (badge && distEl) {
          distEl.textContent = `${distKm} km`;
          badge.style.display = 'flex';
        }
      } else {
        _routePolylineLayer = L.polyline(coords, { color: '#2d6a4f', weight: 3, opacity: 0.7, dashArray: '8,5' }).addTo(editRouteMapInstance);
      }
    } catch (err) {
      _routePolylineLayer = L.polyline(coords, { color: '#2d6a4f', weight: 3, opacity: 0.7, dashArray: '8,5' }).addTo(editRouteMapInstance);
    } finally {
      if (loadingEl) { loadingEl.style.display = 'none'; }
    }
  }
}

function updateSaveBtn() {
  const count = editRouteSelectedHaltes.length;
  const canSave = count >= 2;
  const btnTop = document.getElementById('save-route-btn');
  const btnBot = document.getElementById('save-route-btn-bottom');
  const btnTxt  = document.getElementById('save-route-btn-text');   // header btn
  const btnTxt2 = document.getElementById('save-route-btn-text-2'); // footer btn
  const label = canSave ? `Simpan Rute (${count} halte)` : `Pilih minimal 2 halte`;
  if (btnTop) {
    btnTop.disabled = !canSave;
    btnTop.style.opacity = canSave ? '1' : '0.5';
    btnTop.style.cursor  = canSave ? 'pointer' : 'not-allowed';
  }
  if (btnBot) {
    btnBot.disabled = !canSave;
    btnBot.style.background = canSave ? '#1B5E37' : '#ccc';
    btnBot.style.color  = canSave ? 'white' : '#888';
    btnBot.style.cursor = canSave ? 'pointer' : 'not-allowed';
  }
  if (btnTxt)  btnTxt.textContent  = label;
  if (btnTxt2) btnTxt2.textContent = label;
}

async function saveEditRoute() {
  if (editRouteSelectedHaltes.length < 2) { toast('Pilih minimal 2 halte', 'warn'); return; }
  const halteIds = editRouteSelectedHaltes.map((h, i) => ({ halte_id: h.id, urutan: i + 1 }));
  const polylinePoints = editRouteSelectedHaltes
    .filter(h => h.latitude !== undefined && h.latitude !== null && h.longitude !== undefined && h.longitude !== null)
    .map(h => ({ latitude: parseFloat(h.latitude), longitude: parseFloat(h.longitude) }));

  let res;
  if (currentRouteData) {
    res = await api.post('/routes/' + currentRouteData.id + '/sync', { polyline: polylinePoints, haltes: halteIds });
  } else {
    res = await api.post('/routes', { bus_id: currentBusId, haltes: halteIds });
  }

  if (res.ok) {
    toast('Rute berhasil disimpan');
    currentRouteData = res.data?.data ?? currentRouteData;
    closeEditRouteModal();
    if (typeof loadBus === 'function') {
      loadBus(currentPage);
    }
  } else {
    toast(res.data?.message ?? 'Gagal menyimpan rute', 'error');
  }
}

function openAddHalteQuick() {
  toast('Tambah halte baru melalui menu Halte di sidebar', 'info');
}

async function deleteRoute() {
  if (currentRouteData) {
    confirmDialog('Hapus rute ini?', async () => {
      const r = await api.delete('/routes/' + currentRouteData.id);
      if (r.ok) {
        toast('Rute dihapus');
        closeModal('route-halte-modal');
        loadBus(currentPage);
      } else {
        toast(r.data?.message ?? 'Gagal menghapus rute', 'error');
      }
    });
  }
}

// ──── Siswa ────
async function openSiswa(busId, busName) {
  currentBusId = busId;
  currentBusName = busName;
  document.getElementById('siswa-bus-name').textContent = busName || 'Bus';
  document.getElementById('siswa-search-input').value = '';
  document.getElementById('siswa-content').innerHTML = `
    <div class="empty-state">
      <p style="color:var(--c-text-grey)">Memuat siswa...</p>
    </div>
  `;
  openModal('siswa-modal');

  try {
    const res = await api.get(`/buses/${busId}/students`);
    const students = res.data?.data?.data ?? res.data?.data ?? [];
    currentBusStudents = students || [];
    renderSiswaList(currentBusStudents);
  } catch (err) {
    console.error('openSiswa error', err);
    currentBusStudents = [];
    document.getElementById('siswa-content').innerHTML = `
      <div class="empty-state">
        <p style="color:var(--c-text-grey)">Gagal memuat siswa. Coba lagi.</p>
      </div>
    `;
    toast('Gagal memuat data siswa', 'error');
  }
}

function filterSiswaList() {
  const query = document.getElementById('siswa-search-input').value.trim().toLowerCase();
  const filtered = currentBusStudents.filter(siswa => {
    const name = (siswa.user?.name || siswa.name || '').toLowerCase();
    const email = (siswa.user?.email || siswa.email || '').toLowerCase();
    return name.includes(query) || email.includes(query);
  });
  renderSiswaList(filtered);
}

function renderSiswaList(students) {
  if (!students || students.length === 0) {
    document.getElementById('siswa-content').innerHTML = `
      <div class="empty-state">
        <p style="color:var(--c-text-grey)">Belum ada siswa untuk bus ini</p>
      </div>
    `;
    return;
  }

  let html = `<div style="display:flex;flex-direction:column;gap:10px">`;
  for (const siswa of students) {
    const namaSiswa = siswa.user?.name || siswa.name || 'N/A';
    const emailSiswa = siswa.user?.email || siswa.email || 'N/A';
    const siswaId = siswa.id || siswa.student_id;
    const halteLabel = siswa.halte_tujuan ? `Halte: ${siswa.halte_tujuan}` : '';

    html += `
      <div class="siswa-list-item" onclick="openStudentDetail(${siswaId})">
        <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
          <div style="width:44px;height:44px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f0f0f0;flex-shrink:0;">
            <img src="${siswa.user?.photo_url ? proxyImgUrl(siswa.user.photo_url) : '/images/siswa/default.svg'}" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='/images/siswa/default.svg'">
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:14px;line-height:1.2">${namaSiswa}</div>
            <div style="font-size:12px;color:var(--c-text-grey);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${emailSiswa}</div>
            ${halteLabel ? `<div style="font-size:11px;color:#1976d2;margin-top:4px">${halteLabel}</div>` : ''}
          </div>
        </div>
      </div>
    `;
  }
  html += `</div>`;
  document.getElementById('siswa-content').innerHTML = html;
}

async function openStudentDetail(studentId) {
  try {
    const res = await api.get('/students/' + studentId);
    if (!res.ok) {
      toast(res.data?.message ?? 'Gagal memuat detail siswa', 'error');
      return;
    }

    currentStudentDetailId = studentId;
    const siswa = res.data?.data ?? {};
    const user = siswa.user ?? {};
    const busName = siswa.bus?.kode_bus ?? siswa.bus?.kode ?? siswa.bus?.nama_bus ?? '-';
    const halteName = siswa.halte?.nama_halte ?? siswa.halte_tujuan ?? siswa.halte?.nama ?? '-';
    const alamat = siswa.alamat || user.alamat || '-';
    const status = siswa.status || siswa.approval_status || '-';

    const photoUrl = user.photo_url ? proxyImgUrl(user.photo_url) : '/images/siswa/default.svg';
    const photoEl = document.getElementById('student-detail-photo');
    photoEl.src = photoUrl;
    photoEl.onerror = function() { this.src = '/images/siswa/default.svg'; };

    document.getElementById('student-detail-name').textContent = user.name || siswa.name || 'N/A';
    document.getElementById('student-detail-email').textContent = user.email || siswa.email || '-';
    document.getElementById('student-detail-nis').textContent = siswa.nis || siswa.student?.nis || '-';
    document.getElementById('student-detail-kelas').textContent = siswa.kelas || siswa.student?.kelas || '-';
    document.getElementById('student-detail-sekolah').textContent = siswa.sekolah || siswa.student?.sekolah || '-';
    document.getElementById('student-detail-status').textContent = status;
    document.getElementById('student-detail-bus').textContent = busName;
    document.getElementById('student-detail-halte').textContent = halteName;
    document.getElementById('student-detail-alamat').textContent = alamat;

    openModal('student-detail-modal');
  } catch (e) {
    console.error(e);
    toast('Gagal memuat detail siswa', 'error');
  }
}

async function openAddSiswaForm() {
  if (!currentBusId) {
    toast('Tidak ada bus yang dipilih', 'error');
    return;
  }

  const searchResults = document.getElementById('add-siswa-search-results');
  const selectedLabel = document.getElementById('add-siswa-selected-label');
  const halteSelect = document.getElementById('select-halte-to-add');
  document.getElementById('add-siswa-bus-name').textContent = document.getElementById('siswa-bus-name').textContent;

  searchResults.innerHTML = '<div style="padding:12px;color:#6b7280">Memuat siswa...</div>';
  selectedLabel.textContent = 'Belum ada siswa dipilih';
  selectedLabel.style.color = '#6b7280';
  halteSelect.innerHTML = '<option value="">-- Memuat halte... --</option>';

  try {
    const [studentsRes, busRes] = await Promise.all([
      api.get('/students', { per_page: 1000, approval_status: 'approved' }),
      api.get('/buses/' + currentBusId)
    ]);

    if (!studentsRes.ok) {
      throw new Error('Gagal memuat daftar siswa');
    }
    if (!busRes.ok) {
      throw new Error('Gagal memuat detail bus');
    }

    const students = studentsRes.data?.data?.data ?? studentsRes.data?.data ?? [];
    const bus = busRes.data?.data;
    const haltes = getBusHaltes(bus);

    if (haltes.length === 0) {
      toast('Bus belum memiliki halte. Tambahkan halte pada rute bus terlebih dahulu.', 'error');
      return;
    }

    const availableStudents = students.filter(s => Number(s.bus_id || 0) !== Number(currentBusId));
    addSiswaAvailableStudents = availableStudents;
    addSiswaSelectedId = null;
    document.getElementById('add-siswa-search-input').value = '';
    renderAddSiswaSelect(addSiswaAvailableStudents);

    halteSelect.innerHTML = '<option value="">-- Pilih halte --</option>';
    for (const halte of haltes) {
      const option = document.createElement('option');
      option.value = halte.id;
      option.textContent = halte.nama_halte || `${halte.id}`;
      halteSelect.appendChild(option);
    }

    openModal('add-siswa-modal');
  } catch (e) {
    console.error(e);
    toast(e.message || 'Gagal memuat form tambah siswa', 'error');
  }
}

function getBusHaltes(bus) {
  const haltes = [];
  if (!bus?.routes?.length) {
    return haltes;
  }

  for (const route of bus.routes) {
    for (const halte of route.haltes ?? []) {
      if (!haltes.some(h => h.id === halte.id)) {
        haltes.push(halte);
      }
    }
  }

  return haltes;
}

async function addSiswaToBus() {
  const siswaId = addSiswaSelectedId;
  const halteId = document.getElementById('select-halte-to-add').value;

  if (!siswaId) {
    toast('Silakan pilih siswa terlebih dahulu melalui search', 'error');
    return;
  }
  if (!halteId) {
    toast('Silakan pilih halte terlebih dahulu', 'error');
    return;
  }

  try {
    const res = await api.post(`/buses/${currentBusId}/students`, {
      student_id: siswaId,
      halte_id: halteId,
    });

    if (res.ok) {
      toast('Siswa berhasil ditambahkan ke bus');
      closeModal('add-siswa-modal');
      openSiswa(currentBusId, document.getElementById('siswa-bus-name').textContent);
    } else {
      toast(res.data?.message ?? 'Gagal menambahkan siswa', 'error');
    }
  } catch (e) {
    console.error(e);
    toast('Gagal menambahkan siswa', 'error');
  }
}

async function removeStudentFromCurrentBus() {
  if (!currentStudentDetailId) {
    toast('Tidak ada siswa terpilih', 'error');
    return;
  }
  confirmDialog('Hapus siswa dari bus ini?', async () => {
    const r = await api.delete(`/buses/${currentBusId}/students/${currentStudentDetailId}`);
    if (r.ok) {
      toast('Siswa dihapus dari bus');
      currentBusStudents = currentBusStudents.filter(s => (s.id || s.student_id) !== currentStudentDetailId);
      renderSiswaList(currentBusStudents);
      closeModal('student-detail-modal');
      openSiswa(currentBusId, currentBusName);
    } else {
      toast(r.data?.message ?? 'Gagal', 'error');
    }
  });
}

async function removeSiswaFromBus(siswaId) {
  confirmDialog('Hapus siswa dari bus ini?', async () => {
    const r = await api.delete(`/buses/${currentBusId}/students/${siswaId}`);
    if (r.ok) {
      toast('Siswa dihapus dari bus');
      renderSiswaList(currentBusStudents.filter(s => (s.id || s.student_id) !== siswaId));
      openSiswa(currentBusId, currentBusName);
    } else {
      toast(r.data?.message ?? 'Gagal', 'error');
    }
  });
}

// Photo preview handler
document.addEventListener('DOMContentLoaded', function() {
  const photoInput = document.getElementById('bus-photo-input');
  if (photoInput) {
    photoInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('bus-photo-img').src = event.target.result;
          document.getElementById('bus-photo-preview').style.display = 'block';
          document.getElementById('bus-photo-upload-zone').style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

loadBus();
</script>
@endpush
