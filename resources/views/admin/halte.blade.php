@extends('admin.layouts.app')
@section('title','Manajemen Halte')
@section('page-title','Halte')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama halte..." oninput="debounce(loadHalte,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadHalte()"><span class="material-icons">refresh</span></button>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start">
  <div class="card" style="padding:0">
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Nama Halte</th><th>Latitude</th><th>Longitude</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
        <tbody id="halte-tbody">
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-text-grey)">
            <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...
          </td></tr>
        </tbody>
      </table>
    </div>
    <div id="halte-pagination" style="padding:12px 14px"></div>
  </div>

  {{-- Map untuk pick lokasi halte --}}
  <div class="card" style="padding:12px">
    <div style="font-size:13px;font-weight:600;margin-bottom:8px;color:var(--c-text-grey)">
      <span class="material-icons" style="font-size:14px;vertical-align:middle">place</span> Peta Halte
    </div>
    <div id="halte-map" style="height:320px;border-radius:var(--radius-md)"></div>
  </div>
</div>

{{-- Modal --}}
<div class="modal-overlay" id="halte-modal">
  <div class="modal" style="max-width:440px">
    <div class="modal-header">
      <div class="modal-title" id="halte-modal-title">Tambah Halte</div>
      <button class="modal-close" onclick="closeModal('halte-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="info-bar" style="margin-bottom:14px;font-size:12px">
        <span class="material-icons" style="font-size:14px">info</span>
        Klik pada peta untuk mengisi koordinat secara otomatis
      </div>
      <form id="halte-form">
        <div class="form-group"><label class="form-label">Nama Halte</label>
          <input class="form-control" name="nama_halte" placeholder="Nama halte" required></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px">
          <div class="form-group"><label class="form-label">Latitude</label>
            <input class="form-control" name="latitude" id="lat-input" type="number" step="any" placeholder="-7.xxxx" required></div>
          <div class="form-group"><label class="form-label">Longitude</label>
            <input class="form-control" name="longitude" id="lng-input" type="number" step="any" placeholder="111.xxxx" required></div>
        </div>
        <div class="form-group"><label class="form-label">Deskripsi</label>
          <textarea class="form-control" name="deskripsi" rows="2" placeholder="Keterangan halte (opsional)"></textarea></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('halte-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveHalte()">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1, halteMap, pickMarker, halteMarkers = [];
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

// Init peta halte
document.addEventListener('DOMContentLoaded', () => {
  halteMap = L.map('halte-map', { attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(halteMap);
  halteMap.setView([-7.6298, 111.5233], 13);
  halteMap.on('click', e => {
    document.getElementById('lat-input').value = e.latlng.lat.toFixed(6);
    document.getElementById('lng-input').value = e.latlng.lng.toFixed(6);
    if (pickMarker) pickMarker.setLatLng(e.latlng);
    else pickMarker = L.marker(e.latlng, { draggable: true }).addTo(halteMap);
    pickMarker.on('dragend', ev => {
      document.getElementById('lat-input').value = ev.target.getLatLng().lat.toFixed(6);
      document.getElementById('lng-input').value = ev.target.getLatLng().lng.toFixed(6);
    });
  });
});

async function loadHalte(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/haltes', { search: q, page, per_page: 15 });
  const rows = res.data ?? [];
  const meta = res.pagination;
  const tbody = document.getElementById('halte-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><span class="material-icons">place</span><p>Tidak ada halte</p></div></td></tr>`;
    document.getElementById('halte-pagination').innerHTML = ''; return;
  }
  // Re-render map markers
  halteMarkers.forEach(m => halteMap.removeLayer(m)); halteMarkers = [];
  rows.forEach(h => {
    const m = L.marker([h.latitude, h.longitude])
      .addTo(halteMap).bindPopup(`<b>${h.nama_halte}</b>`);
    halteMarkers.push(m);
  });
  if (halteMarkers.length) {
    const grp = L.featureGroup(halteMarkers); halteMap.fitBounds(grp.getBounds(), { padding: [20, 20] });
  }
  tbody.innerHTML = rows.map((h, i) => `
    <tr>
      <td>${(page-1)*15+i+1}</td>
      <td><div style="font-weight:600">${h.nama_halte}</div></td>
      <td style="font-size:12px">${parseFloat(h.latitude).toFixed(5)}</td>
      <td style="font-size:12px">${parseFloat(h.longitude).toFixed(5)}</td>
      <td style="font-size:12px;color:var(--c-text-grey)">${h.deskripsi ?? '-'}</td>
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
  if (pickMarker) { halteMap.removeLayer(pickMarker); pickMarker = null; }
  openModal('halte-modal');
}

async function editHalte(id) {
  editId = id;
  document.getElementById('halte-modal-title').textContent = 'Edit Halte';
  const res = await api.get('/haltes/' + id);
  const h = res.data;
  const f = document.getElementById('halte-form');
  f.nama_halte.value = h.nama_halte ?? '';
  document.getElementById('lat-input').value = h.latitude ?? '';
  document.getElementById('lng-input').value = h.longitude ?? '';
  f.deskripsi.value = h.deskripsi ?? '';
  openModal('halte-modal');
}

async function saveHalte() {
  const f = document.getElementById('halte-form');
  const body = { nama_halte: f.nama_halte.value, latitude: document.getElementById('lat-input').value, longitude: document.getElementById('lng-input').value, deskripsi: f.deskripsi.value };
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
