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
  <select class="filter-select" id="filter-status" onchange="loadBus()">
    <option value="">Semua Status</option>
    <option value="aktif">Aktif</option>
    <option value="maintenance">Perawatan</option>
    <option value="non_aktif">Non-aktif</option>
  </select>
  <button class="btn btn-icon" onclick="loadBus()"><span class="material-icons">refresh</span></button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Kode Bus</th><th>Plat Nomor</th><th>Nama</th><th>Kapasitas</th><th>Status</th><th>GPS</th><th>Aksi</th></tr></thead>
      <tbody id="bus-tbody">
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--c-text-grey)">
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
          <div class="form-group" style="grid-column:1/-1"><label class="form-label">Nama Bus</label>
            <input class="form-control" name="nama" placeholder="Nama bus" required></div>
          <div class="form-group"><label class="form-label">Kapasitas</label>
            <input class="form-control" name="kapasitas" type="number" placeholder="30"></div>
          <div class="form-group"><label class="form-label">Status</label>
            <select class="form-control" name="status">
              <option value="aktif">Aktif</option>
              <option value="maintenance">Perawatan</option>
              <option value="non_aktif">Non-aktif</option>
            </select>
          </div>
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

@endsection
@push('scripts')
<script>
let editId = null, assignBusId = null, currentPage = 1;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

async function loadBus(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const status = document.getElementById('filter-status').value;
  const res = await api.get('/buses', { search: q, status, page, per_page: 15 });
  const rows = res.data?.data ?? [];
  const meta = res.data?.meta;
  const tbody = document.getElementById('bus-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><span class="material-icons">directions_bus</span><p>Tidak ada bus</p></div></td></tr>`;
    document.getElementById('bus-pagination').innerHTML = ''; return;
  }
  tbody.innerHTML = rows.map((b, i) => `
    <tr>
      <td>${(page-1)*15+i+1}</td>
      <td><span style="font-weight:700;color:var(--c-primary)">${b.kode_bus}</span></td>
      <td>${b.plat_nomor}</td>
      <td>${b.nama ?? '-'}</td>
      <td style="text-align:center">${b.kapasitas ?? '-'}</td>
      <td>${statusBadge(b.status)}</td>
      <td><span class="badge ${b.gps_active ? 'badge-green':'badge-grey'}">${b.gps_active ? 'ON':'OFF'}</span></td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          <button class="btn btn-xs btn-outline" onclick="editBus(${b.id})">Edit</button>
          <button class="btn btn-xs" style="background:#E3F0FB;color:var(--c-blue)" onclick="openAssign(${b.id})">Driver</button>
          <button class="btn btn-xs btn-icon" onclick="deleteBus(${b.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  document.getElementById('bus-pagination').innerHTML = meta ? renderPagination(meta, p => loadBus(p)) : '';
}

function openAddModal() {
  editId = null;
  document.getElementById('bus-modal-title').textContent = 'Tambah Bus';
  document.getElementById('bus-form').reset();
  openModal('bus-modal');
}

async function editBus(id) {
  editId = id;
  document.getElementById('bus-modal-title').textContent = 'Edit Bus';
  const res = await api.get('/buses/' + id);
  const b = res.data?.data ?? res.data;
  const f = document.getElementById('bus-form');
  f.kode_bus.value = b.kode_bus ?? '';
  f.plat_nomor.value = b.plat_nomor ?? '';
  f.nama.value = b.nama ?? '';
  f.kapasitas.value = b.kapasitas ?? '';
  f.status.value = b.status ?? 'aktif';
  openModal('bus-modal');
}

async function saveBus() {
  const f = document.getElementById('bus-form');
  const body = { kode_bus: f.kode_bus.value, plat_nomor: f.plat_nomor.value, nama: f.nama.value, kapasitas: f.kapasitas.value, status: f.status.value };
  const res = editId ? await api.put('/buses/' + editId, body) : await api.post('/buses', body);
  res.ok ? (toast('Bus berhasil disimpan'), closeModal('bus-modal'), loadBus(currentPage)) : toast(res.data?.message ?? 'Gagal', 'error');
}

async function openAssign(busId) {
  assignBusId = busId;
  const drRes = await api.get('/drivers', { per_page: 100 });
  const drivers = drRes.data?.data ?? [];
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

loadBus();
</script>
@endpush
