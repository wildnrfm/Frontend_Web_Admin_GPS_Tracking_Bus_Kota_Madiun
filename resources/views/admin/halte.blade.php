@extends('admin.layouts.app')
@section('title','Manajemen Halte')
@section('page-title','Halte')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<style>
/* ╔══════════════════════════════════════════════════════════════╗ */
/* ║              HALTE PAGE REDESIGN STYLES                       ║ */
/* ╚══════════════════════════════════════════════════════════════╝ */

/* Hero Card - Green Gradient (Halte Theme) */
.halte-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px;
  color: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.24);
  margin-bottom: 24px;
}
.halte-hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  pointer-events: none;
}
.halte-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.04);
  pointer-events: none;
}
.halte-hero-top {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 2;
}
.halte-hero-icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
  color: #fff;
}
.halte-hero-text h2 {
  margin: 0; font-size: 24px; font-weight: 700; color: #fff;
  letter-spacing: -0.3px;
}
.halte-hero-text p {
  margin: 4px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.85);
}

.modal-overlay {
  z-index: 1050 !important;
}
</style>

{{-- Hero Card --}}
<div class="halte-hero">
  <div class="halte-hero-top">
    <div class="halte-hero-icon"><span class="material-icons">place</span></div>
    <div class="halte-hero-text">
      <h2>Manajemen Halte</h2>
      <p>Kelola titik penjemputan, perhentian bus, dan lokasi halte di peta</p>
    </div>
  </div>
</div>

{{-- Search Bar --}}
<div style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
  <div class="search-box" style="flex:1; min-width:220px;">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama halte..." oninput="debounce(loadHalte,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadHalte()"><span class="material-icons">refresh</span></button>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start">
  <div class="card" style="padding:0">
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Nama Halte</th><th>Latitude</th><th>Longitude</th><th>Alamat</th><th>Aksi</th></tr></thead>
        <tbody id="halte-tbody">
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-text-grey)">
            <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...
          </td></tr>
        </tbody>
      </table>
    </div>
    <div id="halte-pagination" style="padding:12px 14px"></div>
  </div>

  {{-- Map untuk overview semua halte --}}
  <div class="card" style="padding:12px">
    <div style="font-size:13px;font-weight:600;margin-bottom:8px;color:var(--c-text-grey)">
      <span class="material-icons" style="font-size:14px;vertical-align:middle">place</span> Peta Halte
    </div>
    <div id="halte-map" style="height:320px;border-radius:var(--radius-md)"></div>
  </div>
</div>

{{-- Modal Tambah/Edit Halte --}}
<div class="modal-overlay" id="halte-modal">
  <div class="modal" style="max-width:760px; border-radius:16px; overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border-bottom:none; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,0.18); color:#fff; display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">place</span>
        </div>
        <div>
          <div class="modal-title" id="halte-modal-title" style="font-weight:700; font-size:16px; color:#000; margin:0;">Tambah Halte</div>
          <div style="font-size:11px; color:rgba(0,0,0,0.65); margin-top:2px;">Tentukan koordinat halte pada peta di sebelah kanan</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('halte-modal')" style="color:#000"><span class="material-icons">close</span></button>
    </div>
    
    <div class="modal-body" style="padding:24px;">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">
        <!-- Left Column: Input Form -->
        <form id="halte-form" style="display:flex; flex-direction:column; gap:14px; margin:0;">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-weight:600;">Nama Halte</label>
            <input class="form-control" name="nama_halte" placeholder="Nama halte (contoh: Halte Banjarsari)" required style="border-radius:10px;">
          </div>
          
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" style="font-weight:600;">Alamat Halte <span style="color:#999;font-weight:400">(opsional)</span></label>
            <input class="form-control" name="alamat" placeholder="Alamat lokasi halte" style="border-radius:10px;">
          </div>
          
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600;">Latitude</label>
              <input class="form-control" id="lat-input" name="latitude" type="number" step="any" placeholder="-7.xxxx" required readonly style="border-radius:10px; background:#f5f5f5; cursor:not-allowed;">
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600;">Longitude</label>
              <input class="form-control" id="lng-input" name="longitude" type="number" step="any" placeholder="111.xxxx" required readonly style="border-radius:10px; background:#f5f5f5; cursor:not-allowed;">
            </div>
          </div>
          
          <div class="info-bar" style="margin:4px 0 0 0; font-size:11px; border-radius:8px; padding:10px 12px; background:#E8F5ED; color:var(--c-primary); border:none;">
            <span class="material-icons" style="font-size:16px">info</span>
            <span>Geser pin hijau di peta atau klik area peta untuk merubah lokasi koordinat secara presisi.</span>
          </div>
        </form>
        
        <!-- Right Column: Interactive Map Picker -->
        <div style="display:flex; flex-direction:column; gap:8px;">
          <label class="form-label" style="font-weight:600; color:var(--c-text-dark); margin:0;">Peta Lokasi Halte</label>
          <div id="modal-halte-map" style="height:275px; border-radius:12px; border:1px solid var(--c-border); overflow:hidden; z-index:1;"></div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer" style="background:#f8faf9; border-top:1px solid #eef2f0; padding:16px 24px; display:flex; justify-content:flex-end; gap:12px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('halte-modal')" style="border-radius:8px;">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveHalte()" style="border-radius:8px; background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border:none;">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1, halteMap, modalMap, modalMarker, halteMarkers = [];
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

// Init peta halte
document.addEventListener('DOMContentLoaded', () => {
  // Map utama di list page
  halteMap = L.map('halte-map', { attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(halteMap);
  halteMap.setView([-7.6298, 111.5233], 13);
  
  // Map di modal picker
  modalMap = L.map('modal-halte-map', { attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
  modalMap.setView([-7.6298, 111.5233], 13);

  modalMap.on('click', e => {
    updateModalMarker(e.latlng);
  });
});

function updateModalMarker(latlng) {
  const lat = parseFloat(latlng.lat).toFixed(6);
  const lng = parseFloat(latlng.lng).toFixed(6);
  
  document.getElementById('lat-input').value = lat;
  document.getElementById('lng-input').value = lng;
  
  if (modalMarker) {
    modalMarker.setLatLng(latlng);
  } else {
    // Gunakan custom icon hijau / marker Leaflet biasa
    modalMarker = L.marker(latlng, { draggable: true }).addTo(modalMap);
    modalMarker.on('dragend', ev => {
      updateModalMarker(ev.target.getLatLng());
    });
  }
}

async function loadHalte(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/haltes', { search: q, page, per_page: 15 });
  const rows = res.data?.data ?? [];  
  const meta = res.data?.pagination;
  const tbody = document.getElementById('halte-tbody');
  
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><span class="material-icons">place</span><p>Tidak ada halte</p></div></td></tr>`;
    document.getElementById('halte-pagination').innerHTML = ''; 
    return;
  }
  
  // Re-render map markers di peta overview utama
  halteMarkers.forEach(m => halteMap.removeLayer(m)); 
  halteMarkers = [];
  rows.forEach(h => {
    const m = L.marker([h.latitude, h.longitude])
      .addTo(halteMap).bindPopup(`<b>${h.nama_halte}</b>`);
    halteMarkers.push(m);
  });
  
  if (halteMarkers.length) {
    const grp = L.featureGroup(halteMarkers); 
    halteMap.fitBounds(grp.getBounds(), { padding: [20, 20] });
  }
  
  tbody.innerHTML = rows.map((h, i) => `
    <tr>
      <td>${(page-1)*15+i+1}</td>
      <td><div style="font-weight:600">${h.nama_halte}</div></td>
      <td style="font-size:12px">${parseFloat(h.latitude).toFixed(5)}</td>
      <td style="font-size:12px">${parseFloat(h.longitude).toFixed(5)}</td>
      <td style="font-size:12px;color:var(--c-text-grey)">${h.alamat ?? '-'}</td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-xs btn-outline" onclick="editHalte(${h.id})">Edit</button>
          <button class="btn btn-xs btn-icon" onclick="deleteHalte(${h.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  document.getElementById('halte-pagination').innerHTML = meta ? renderPagination(meta, p => loadHalte(p)) : '';
}

function openAddModal() {
  editId = null;
  document.getElementById('halte-modal-title').textContent = 'Tambah Halte';
  document.getElementById('halte-form').reset();
  
  const defaultLatLng = L.latLng(-7.6298, 111.5233);
  document.getElementById('lat-input').value = defaultLatLng.lat.toFixed(6);
  document.getElementById('lng-input').value = defaultLatLng.lng.toFixed(6);
  
  if (modalMarker) {
    modalMap.removeLayer(modalMarker);
    modalMarker = null;
  }
  
  openModal('halte-modal');
  
  // Refresh layout peta modal
  setTimeout(() => {
    modalMap.invalidateSize();
    modalMap.setView(defaultLatLng, 15);
    updateModalMarker(defaultLatLng);
  }, 250);
}

async function editHalte(id) {
  editId = id;
  document.getElementById('halte-modal-title').textContent = 'Edit Halte';
  const res = await api.get('/haltes/' + id);
  const h = res.data?.data;
  const f = document.getElementById('halte-form');
  f.nama_halte.value = h.nama_halte ?? '';
  f.alamat.value = h.alamat ?? '';
  
  const latVal = parseFloat(h.latitude) || -7.6298;
  const lngVal = parseFloat(h.longitude) || 111.5233;
  const latlng = L.latLng(latVal, lngVal);
  
  document.getElementById('lat-input').value = latVal.toFixed(6);
  document.getElementById('lng-input').value = lngVal.toFixed(6);
  
  if (modalMarker) {
    modalMap.removeLayer(modalMarker);
    modalMarker = null;
  }
  
  openModal('halte-modal');
  
  // Refresh layout peta modal
  setTimeout(() => {
    modalMap.invalidateSize();
    modalMap.setView(latlng, 15);
    updateModalMarker(latlng);
  }, 250);
}

async function saveHalte() {
  const f = document.getElementById('halte-form');
  const body = { 
    nama_halte: f.nama_halte.value, 
    latitude: document.getElementById('lat-input').value, 
    longitude: document.getElementById('lng-input').value
  };
  // Hanya kirim alamat jika tidak kosong (untuk edit, jika kosong akan null/not included)
  if (f.alamat.value.trim()) {
    body.alamat = f.alamat.value;
  }
  const res = editId ? await api.put('/haltes/' + editId, body) : await api.post('/haltes', body);
  res.ok ? (toast('Halte berhasil disimpan'), closeModal('halte-modal'), loadHalte(currentPage)) : toast(res.data?.message ?? 'Gagal', 'error');
}

async function deleteHalte(id) {
  confirmDialog('Hapus halte ini?', async () => {
    const r = await api.delete('/haltes/' + id);
    r.ok ? (toast('Halte dihapus', 'warn'), loadHalte(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

loadHalte();
</script>
@endpush
