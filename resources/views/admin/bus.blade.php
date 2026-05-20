@extends('admin.layouts.app')
@section('title','Manajemen Bus')
@section('page-title','Bus')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari kode bus, plat nomor..." oninput="debounce(loadBus,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadBus()"><span class="material-icons">refresh</span></button>
</div>

<div style="padding:12px 14px; display:flex; gap:8px; flex-wrap:wrap; border-bottom:1px solid var(--c-border)">
  <button class="filter-btn active" data-filter="all" onclick="setBusFilter('all', this)">Semua</button>
  <button class="filter-btn" data-filter="aktif" onclick="setBusFilter('aktif', this)">Aktif</button>
  <button class="filter-btn" data-filter="maintenance" onclick="setBusFilter('maintenance', this)">Perawatan</button>
  <button class="filter-btn" data-filter="non_aktif" onclick="setBusFilter('non_aktif', this)">Nonaktif</button>
</div>

<style>
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
  background: var(--c-primary);
  border-color: var(--c-primary);
  color: white;
}
.filter-btn:hover {
  border-color: var(--c-primary);
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
</style>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Foto</th><th>Kode Bus</th><th>Plat Nomor</th><th>Status</th><th>GPS</th><th>Aksi</th></tr></thead>
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
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="bus-modal-title">Tambah Bus</div>
      <button class="modal-close" onclick="closeModal('bus-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <form id="bus-form">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px">
          <div class="form-group"><label class="form-label">Kode Bus</label>
            <input class="form-control" name="kode_bus" placeholder="BUS-01" required></div>
          <div class="form-group"><label class="form-label">Plat Nomor</label>
            <input class="form-control" name="plat_nomor" placeholder="AE 1234 XX" required></div>
          <div class="form-group"><label class="form-label">Status</label>
            <select class="form-control" name="status">
              <option value="aktif">Aktif</option>
              <option value="maintenance">Perawatan</option>
              <option value="non_aktif">Non-aktif</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Foto Bus</label>
            <input class="form-control" type="file" name="photo" accept="image/*"></div>
        </div>
        <div id="bus-photo-preview" style="margin-top:12px;display:none">
          <label class="form-label">Preview Foto</label>
          <img id="bus-photo-img" src="" style="width:100%;max-width:200px;height:auto;border-radius:8px;object-fit:cover">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('bus-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveBus()">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Assign Driver --}}
<div class="modal-overlay" id="assign-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <div class="modal-title">Assign Driver</div>
      <button class="modal-close" onclick="closeModal('assign-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Pilih Driver</label>
        <select class="form-control" id="assign-driver-select"><option value="">Pilih driver...</option></select>
      </div>
      <div class="form-group"><label class="form-label">Tanggal Mulai</label>
        <input class="form-control" id="assign-start" type="date"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('assign-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveAssign()">Assign</button>
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
        style="background:rgba(255,255,255,.15);border:none;cursor:pointer;width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;transition:background .2s"
        onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
        <span class="material-icons" style="font-size:20px">arrow_back</span>
      </button>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:16px;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" id="edit-route-title">Ubah Rute</div>
        <div style="font-size:12px;color:rgba(255,255,255,.7)" id="edit-route-subtitle">Atur halte dan urutan rute</div>
      </div>
      {{-- Route info badge --}}
      <div id="edit-route-info-badge" style="display:none;align-items:center;gap:6px;background:rgba(255,255,255,.15);border-radius:8px;padding:6px 12px">
        <span class="material-icons" style="font-size:15px;color:rgba(255,255,255,.8)">straighten</span>
        <span id="edit-route-distance" style="font-size:12px;color:white;font-weight:600"></span>
      </div>
      <button onclick="saveEditRoute()" id="save-route-btn"
        style="background:white;color:#1B5E37;border:none;border-radius:10px;padding:9px 22px;font-weight:700;font-size:13px;cursor:not-allowed;opacity:.5;display:flex;align-items:center;gap:6px;transition:all .2s;font-family:inherit" disabled>
        <span class="material-icons" style="font-size:16px">check_circle</span>
        <span id="save-route-btn-text">Pilih minimal 2 halte</span>
      </button>
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
          <button onclick="saveEditRoute()" id="save-route-btn-bottom"
            style="width:100%;padding:13px;border-radius:10px;border:none;font-weight:700;font-size:14px;cursor:not-allowed;background:#ccc;color:#888;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;font-family:inherit" disabled>
            <span class="material-icons" style="font-size:18px">check_circle</span>
            <span id="save-route-btn-text-2">Pilih minimal 2 halte</span>
          </button>
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
      <div style="margin-bottom:12px">
        <button class="btn btn-primary btn-sm" onclick="openAddSiswaForm()">+ Tambah Siswa</button>
      </div>
      <div id="siswa-content" style="max-height:400px;overflow-y:auto"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')">Tutup</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, assignBusId = null, currentPage = 1, currentFilter = 'all';
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

function setBusFilter(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadBus(1);
}

async function loadBus(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/buses', { search: q, per_page: 1000 });
  
  let rows = res.data?.data ?? [];
  
  // Apply filter
  if (currentFilter !== 'all') {
    rows = rows.filter(b => b.status === currentFilter);
  }
  
  const tbody = document.getElementById('bus-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><span class="material-icons">directions_bus</span><p>Tidak ada bus</p></div></td></tr>`;
    document.getElementById('bus-pagination').innerHTML = ''; return;
  }
  
  const perPage = 15;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);
  
  tbody.innerHTML = paginatedRows.map((b, i) => `
    <tr>
      <td style="width:80px">
        ${b.photo_url ? `<img src="${b.photo_url}?t=${Date.now()}" style="width:60px;height:60px;object-fit:cover;border-radius:6px" alt="${b.kode_bus}">` : `<div style="width:60px;height:60px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center"><span class="material-icons" style="color:#ccc">image</span></div>`}
      </td>
      <td><span style="font-weight:700;color:var(--c-primary)">${b.kode_bus}</span></td>
      <td>${b.plat_nomor}</td>
      <td>${statusBadge(b.status)}</td>
      <td><span class="badge ${b.gps_status === 'on' ? 'badge-green':'badge-grey'}">${b.gps_status === 'on' ? 'ON':'OFF'}</span></td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          <button class="btn btn-xs btn-outline" onclick="editBus(${b.id})">Edit</button>
          <button class="btn btn-xs" style="background:var(--c-primary);color:white" onclick="openRouteHalte(${b.id},'${b.kode_bus}')">Rute & Halte</button>
          <button class="btn btn-xs" style="background:#E3F0FB;color:var(--c-primary)" onclick="openSiswa(${b.id},'${b.kode_bus}')">Siswa</button>
          <button class="btn btn-xs" style="background:#E3F0FB;color:var(--c-blue)" onclick="openAssign(${b.id})">Driver</button>
          <button class="btn btn-xs btn-icon" onclick="deleteBus(${b.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  
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

function openAddModal() {
  editId = null;
  document.getElementById('bus-modal-title').textContent = 'Tambah Bus';
  document.getElementById('bus-form').reset();
  document.getElementById('bus-photo-preview').style.display = 'none';
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
    document.getElementById('bus-photo-img').src = b.photo_url + '?t=' + Date.now();
    document.getElementById('bus-photo-preview').style.display = 'block';
  } else {
    document.getElementById('bus-photo-preview').style.display = 'none';
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

async function openAssign(busId) {
  assignBusId = busId;
  const drRes = await api.get('/drivers', { per_page: 100 });
  const drivers = drRes.data ?? [];
  const sel = document.getElementById('assign-driver-select');
  sel.innerHTML = `<option value="">Pilih driver...</option>` + drivers.map(d => `<option value="${d.id}">${d.user?.name ?? d.name}</option>`).join('');
  document.getElementById('assign-start').value = new Date().toISOString().split('T')[0];
  openModal('assign-modal');
}

async function saveAssign() {
  const driverId = document.getElementById('assign-driver-select').value;
  const startDate = document.getElementById('assign-start').value;
  if (!driverId) { toast('Pilih driver terlebih dahulu', 'warn'); return; }
  const res = await api.post('/buses/' + assignBusId + '/drivers', { driver_id: driverId, tanggal_mulai: startDate });
  res.ok ? (toast('Driver berhasil di-assign'), closeModal('assign-modal'), loadBus(currentPage)) : toast(res.data?.message ?? 'Gagal', 'error');
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
  
  // Get route for this bus
  const res = await api.get(`/buses/${busId}/route`);
  const route = res.data?.data;
  
  if (!route) {
    document.getElementById('route-title-text').textContent = 'Rute & Halte - ' + busName;
    document.getElementById('route-polyline-count').textContent = 'Belum ada rute untuk bus ini';
    document.getElementById('halte-list-content').innerHTML = '<p style="color:var(--c-text-grey);text-align:center;padding:20px">Belum ada halte</p>';
    document.getElementById('halte-count-badge').textContent = '0 halte';
    document.getElementById('route-map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--c-text-grey)">Belum ada data rute</div>';
    openModal('route-halte-modal');
    return;
  }
  
  currentRouteData = route;
  
  // Set title
  document.getElementById('route-title-text').textContent = route.nama_rute || 'Rute & Halte - ' + busName;
  document.getElementById('route-polyline-count').textContent = (route.polyline?.length || 0) + ' titik polyline';
  
  // Set halte count
  const halteCount = route.haltes?.length || 0;
  document.getElementById('halte-count-badge').textContent = halteCount + ' halte';
  
  // Render halte list
  let halteHtml = '';
  if (halteCount > 0) {
    for (let i = 0; i < route.haltes.length; i++) {
      const halte = route.haltes[i];
      const halteData = halte.halte;
      const colors = ['#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4'];
      const color = colors[i % colors.length];
      
      halteHtml += `
        <div style="display:flex;gap:12px;padding:12px;border:1px solid var(--c-border);border-radius:6px">
          <div style="display:flex;align-items:center;justify-content:center;min-width:36px;width:36px;height:36px;background:${color};color:white;border-radius:50%;font-weight:600">${i + 1}</div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px">${halteData?.nama_halte || 'N/A'}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${halteData?.alamat || 'Alamat tidak tersedia'}</div>
          </div>
        </div>
      `;
    }
  } else {
    halteHtml = '<p style="color:var(--c-text-grey);text-align:center;padding:20px">Belum ada halte dalam rute ini</p>';
  }
  document.getElementById('halte-list-content').innerHTML = halteHtml;
  
  // Render map
  setTimeout(() => {
    if (routeMapInstance) {
      routeMapInstance.remove();
    }
    
    const mapContainer = document.getElementById('route-map');
    routeMapInstance = L.map(mapContainer).setView([-7.6288, 111.5305], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(routeMapInstance);
    
    // Plot polyline
    if (route.polyline && route.polyline.length > 0) {
      const polylineCoords = route.polyline.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);
      L.polyline(polylineCoords, { color: '#2196F3', weight: 3, opacity: 0.8 }).addTo(routeMapInstance);
      routeMapInstance.fitBounds(L.latLngBounds(polylineCoords));
    }
    
    // Plot halte markers
    if (route.haltes && route.haltes.length > 0) {
      const colors = ['#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4'];
      route.haltes.forEach((rh, idx) => {
        const halteData = rh.halte;
        if (halteData?.latitude && halteData?.longitude) {
          const color = colors[idx % colors.length];
          const marker = L.circleMarker(
            [parseFloat(halteData.latitude), parseFloat(halteData.longitude)],
            { radius: 24, fillColor: color, color: color, weight: 2, opacity: 1, fillOpacity: 0.8 }
          ).addTo(routeMapInstance);
          
          marker.bindPopup(`<div style="font-weight:600">${halteData.nama_halte}</div><div style="font-size:12px">${halteData.alamat}</div>`);
          
          L.marker([parseFloat(halteData.latitude), parseFloat(halteData.longitude)], {
            icon: L.divIcon({
              html: `<div style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;background:${color};color:white;border-radius:50%;font-weight:600;font-size:14px">${idx + 1}</div>`,
              iconSize: [30, 30],
              className: 'custom-marker'
            })
          }).addTo(routeMapInstance);
        }
      });
    }
  }, 100);
  
  openModal('route-halte-modal');
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

  document.getElementById('edit-route-title').textContent = 'Ubah Rute - ' + editingBusName;
  document.getElementById('edit-route-subtitle').textContent = currentRouteData ? (currentRouteData.nama_rute || 'Edit rute') : 'Rute baru';

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
  let res;
  if (currentRouteData) {
    res = await api.put('/routes/' + currentRouteData.id, { haltes: halteIds });
  } else {
    res = await api.post('/routes', { bus_id: currentBusId, haltes: halteIds });
  }
  if (res.ok) {
    toast('Rute berhasil disimpan');
    closeEditRouteModal();
    openRouteHalte(currentBusId, editingBusName); // refresh route-halte modal
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
  document.getElementById('siswa-bus-name').textContent = busName;
  
  // Get students for this bus (API returns paginated data)
  const res = await api.get(`/buses/${busId}/students`);
  // Handle both direct array and paginated response structure
  const students = res.data?.data?.data ?? res.data?.data ?? [];
  
  if (!students || students.length === 0) {
    document.getElementById('siswa-content').innerHTML = `
      <div class="empty-state">
        <p style="color:var(--c-text-grey)">Belum ada siswa untuk bus ini</p>
      </div>
    `;
  } else {
    let html = `<div style="display:flex;flex-direction:column;gap:8px">`;
    for (const siswa of students) {
      const namaSiswa = siswa.user?.name || siswa.name || 'N/A';
      const emailSiswa = siswa.user?.email || siswa.email || 'N/A';
      const siswaId = siswa.id || siswa.student_id;
      
      html += `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;border:1px solid var(--c-border);border-radius:6px;background:white">
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px">${namaSiswa}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${emailSiswa}</div>
            ${siswa.halte_tujuan ? `<div style="font-size:11px;color:#1976d2;background:#e3f2fd;padding:2px 6px;border-radius:3px;display:inline-block;margin-top:4px">📍 ${siswa.halte_tujuan}</div>` : ''}
          </div>
          <button class="btn btn-xs btn-icon" onclick="removeSiswaFromBus(${siswaId})" style="background:#ffebee;border:none"><span class="material-icons" style="font-size:14px;color:#d32f2f">delete</span></button>
        </div>
      `;
    }
    html += `</div>`;
    document.getElementById('siswa-content').innerHTML = html;
  }
  
  openModal('siswa-modal');
}

async function openAddSiswaForm() {
  toast('Fitur tambah siswa sedang dikembangkan', 'info');
}

async function removeSiswaFromBus(siswaId) {
  confirmDialog('Hapus siswa dari bus ini?', async () => {
    const r = await api.delete(`/buses/${currentBusId}/students/${siswaId}`);
    r.ok ? (toast('Siswa dihapus dari bus'), openSiswa(currentBusId, 'Bus')) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

// Photo preview handler
document.addEventListener('DOMContentLoaded', function() {
  const photoInput = document.querySelector('input[name="photo"]');
  if (photoInput) {
    photoInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('bus-photo-img').src = event.target.result;
          document.getElementById('bus-photo-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

loadBus();
</script>
@endpush
