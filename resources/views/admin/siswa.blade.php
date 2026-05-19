@extends('admin.layouts.app')
@section('title','Manajemen Siswa')
@section('page-title','Siswa')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama, email, NIS..." oninput="debounce(loadSiswa, 400)()">
  </div>
  <button class="btn btn-icon" onclick="loadSiswa()" title="Refresh"><span class="material-icons">refresh</span></button>
</div>

<div style="padding:12px 14px; display:flex; gap:8px; flex-wrap:wrap; border-bottom:1px solid var(--c-border)">
  <button class="filter-btn active" data-filter="all" onclick="setFilter('all', this)">Semua</button>
  <button class="filter-btn" data-filter="active" onclick="setFilter('active', this)">Aktif</button>
  <button class="filter-btn" data-filter="no-bus" onclick="setFilter('no-bus', this)">Belum ada Bus</button>
  <button class="filter-btn" data-filter="inactive" onclick="setFilter('inactive', this)">Nonaktif</button>
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
.filter-btn.active.no-bus-filter {
  background: var(--c-orange);
  border-color: var(--c-orange);
}
</style>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Nama</th><th>Email</th><th>NIS</th><th>Sekolah</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody id="siswa-tbody">
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="siswa-pagination" style="padding:12px 14px"></div>
</div>

{{-- Modal Tambah/Edit --}}
<div class="modal-overlay" id="siswa-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Tambah Siswa</div>
      <button class="modal-close" onclick="closeModal('siswa-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <form id="siswa-form">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input class="form-control" name="name" placeholder="Nama lengkap" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" placeholder="email@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">NIS</label>
            <input class="form-control" name="nis" placeholder="NIS siswa" required>
          </div>
          <div class="form-group">
            <label class="form-label">No HP</label>
            <input class="form-control" name="no_hp" placeholder="08xxxxxxxx">
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Sekolah</label>
            <input class="form-control" name="sekolah" placeholder="Nama sekolah" required>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap"></textarea>
          </div>
          <div class="form-group" id="pw-group">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" placeholder="Min. 8 karakter">
          </div>
          <div class="form-group" id="pwc-group">
            <label class="form-label">Konfirmasi Password</label>
            <input class="form-control" name="password_confirmation" type="password" placeholder="Ulangi password">
          </div>
          <div class="form-group" id="change-pw-group" style="grid-column:1/-1; display:none">
            <label class="form-checkbox">
              <input type="checkbox" id="change-password-check" onchange="togglePasswordFields()">
              <span>Ganti Password Siswa</span>
            </label>
          </div>
          <div id="edit-password-fields" style="display:none; grid-column:1/-1">
            <div class="form-group">
              <label class="form-label">Password Baru</label>
              <input class="form-control" name="new_password" type="password" placeholder="Min. 8 karakter">
            </div>
            <div class="form-group">
              <label class="form-label">Konfirmasi Password</label>
              <input class="form-control" name="new_password_confirmation" type="password" placeholder="Ulangi password">
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveSiswa()" id="save-btn">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Ganti Bus --}}
<div class="modal-overlay" id="bus-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tetapkan Bus untuk <span id="bus-modal-siswa-name"></span></div>
      <button class="modal-close" onclick="closeModal('bus-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Pilih Bus</label>
        <select class="form-control" id="bus-select" onchange="loadHaltesForBus()">
          <option value="">-- Pilih Bus --</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Pilih Halte</label>
        <select class="form-control" id="halte-select">
          <option value="">-- Pilih Halte --</option>
        </select>
      </div>
      <div style="padding:12px; background:#F5F5F5; border-radius:4px; font-size:12px; color:var(--c-text-grey)">
        <span class="material-icons" style="font-size:14px;vertical-align:middle">info</span>
        Halte yang tersedia adalah halte di rute bus yang dipilih.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('bus-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveBusAssignment()">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1, currentFilter = 'all', busesList = [], currentStudentId = null;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

// Load buses on page load
async function loadBuses() {
  try {
    const res = await api.get('/buses', { per_page: 1000 });
    busesList = res.data?.data ?? [];
    populateBusSelect();
  } catch (e) {
    console.error('Failed to load buses:', e);
  }
}

function populateBusSelect() {
  const select = document.getElementById('bus-select');
  select.innerHTML = '<option value="">-- Pilih Bus --</option>' +
    busesList.map(b => `<option value="${b.id}">${b.kode_bus} (${b.plat_nomor})</option>`).join('');
}

function togglePasswordFields() {
  const check = document.getElementById('change-password-check')?.checked;
  document.getElementById('edit-password-fields').style.display = check ? '' : 'none';
}

async function openBusModal(siswaId, siswaName) {
  currentStudentId = siswaId;
  document.getElementById('bus-modal-siswa-name').textContent = siswaName;
  document.getElementById('bus-select').value = '';
  document.getElementById('halte-select').innerHTML = '<option value="">-- Pilih Halte --</option>';
  openModal('bus-modal');
}

async function loadHaltesForBus() {
  const busId = document.getElementById('bus-select').value;
  if (!busId) {
    document.getElementById('halte-select').innerHTML = '<option value="">-- Pilih Halte --</option>';
    return;
  }
  
  try {
    const res = await api.get('/buses/' + busId, {});
    const bus = res.data?.data;
    const routes = bus?.routes ?? [];
    let haltes = [];
    
    // Collect haltes from all routes
    for (const route of routes) {
      const routeHaltes = route.haltes ?? [];
      haltes = haltes.concat(routeHaltes);
    }
    
    // Remove duplicates by id
    haltes = haltes.filter((h, i, arr) => arr.findIndex(x => x.id === h.id) === i);
    
    const select = document.getElementById('halte-select');
    select.innerHTML = '<option value="">-- Pilih Halte --</option>' +
      haltes.map(h => `<option value="${h.id}">${h.nama_halte}</option>`).join('');
  } catch (e) {
    toast('Gagal memuat halte', 'error');
    console.error(e);
  }
}

async function saveBusAssignment() {
  const busId = document.getElementById('bus-select').value;
  const halteId = document.getElementById('halte-select').value;
  
  if (!busId || !halteId) {
    toast('Bus dan Halte harus dipilih', 'error');
    return;
  }
  
  try {
    const res = await api.post(`/buses/${busId}/students`, {
      student_id: currentStudentId,
      halte_id: halteId
    });
    
    if (res.ok) {
      toast('Bus berhasil ditetapkan');
      closeModal('bus-modal');
      loadSiswa(currentPage);
    } else {
      toast(res.data?.message ?? 'Gagal menetapkan bus', 'error');
    }
  } catch (e) {
    toast('Gagal menetapkan bus', 'error');
    console.error(e);
  }
}

function setFilter(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadSiswa(1);
}

function applyClientFilter(rows) {
  if (currentFilter === 'all') return rows;
  
  return rows.filter(s => {
    const isSuspended = s.is_suspended ?? false;
    const hasBus = (s.bus_id ?? 0) > 0;
    
    if (currentFilter === 'active') {
      return !isSuspended && hasBus;
    }
    if (currentFilter === 'no-bus') {
      return !isSuspended && !hasBus;
    }
    if (currentFilter === 'inactive') {
      return isSuspended;
    }
    return true;
  });
}

async function loadSiswa(page = 1) {
  currentPage = page;
  const q   = document.getElementById('search').value;
  const res = await api.get('/students', { search: q, per_page: 1000 });
  
  let rows = res.data?.data ?? [];
  rows = applyClientFilter(rows);
  
  const tbody = document.getElementById('siswa-tbody');
  
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><span class="material-icons">school</span><p>Tidak ada data siswa</p></div></td></tr>`;
    document.getElementById('siswa-pagination').innerHTML = '';
    return;
  }

  const perPage = 15;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);
  
  tbody.innerHTML = paginatedRows.map((s, i) => `
    <tr>
      <td>${start + i + 1}</td>
      <td><div style="font-weight:600">${s.user?.name ?? 'N/A'}</div></td>
      <td style="color:var(--c-text-grey);font-size:12px">${s.user?.email ?? 'N/A'}</td>
      <td>${s.nis ?? '-'}</td>
      <td style="font-size:12px">${s.sekolah ?? '-'}</td>
      <td>${statusBadge(s.approval_status, s.is_suspended)}</td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          ${s.approval_status === 'pending' ? `
            <button class="btn btn-xs btn-primary" onclick="approve(${s.id})">Setujui</button>
            <button class="btn btn-xs" style="background:#FDECEA;color:var(--c-red)" onclick="reject(${s.id})">Tolak</button>
          ` : ''}
          ${s.approval_status === 'approved' && !s.is_suspended ? `
            <button class="btn btn-xs" style="background:#E8F5E9;color:#2E7D32;cursor:pointer" onclick="openBusModal(${s.id}, '${s.user?.name}')">
              <span class="material-icons" style="font-size:14px">directions_bus</span>
            </button>
            <button class="btn btn-xs btn-outline" onclick="editSiswa(${s.id})">Edit</button>
            <button class="btn btn-xs" style="background:#FFF3CD;color:var(--c-amber)" onclick="suspend(${s.id})">Nonaktif</button>
            <button class="btn btn-xs btn-icon" onclick="deleteSiswa(${s.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
          ` : ''}
          ${s.is_suspended ? `
            <button class="btn btn-xs btn-primary" onclick="unsuspend(${s.id})">Aktifkan</button>
            <button class="btn btn-xs btn-icon" onclick="deleteSiswa(${s.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
          ` : ''}
        </div>
      </td>
    </tr>`).join('');

  const totalPages = Math.ceil(rows.length / perPage);
  const paginationHtml = totalPages > 1 ? `
    <div style="display:flex;justify-content:center;gap:4px;padding:8px">
      ${page > 1 ? `<button class="btn btn-sm btn-outline" onclick="loadSiswa(${page - 1})">← Sebelumnya</button>` : ''}
      <span style="padding:8px 12px">Halaman ${page} dari ${totalPages}</span>
      ${page < totalPages ? `<button class="btn btn-sm btn-outline" onclick="loadSiswa(${page + 1})">Berikutnya →</button>` : ''}
    </div>
  ` : '';
  document.getElementById('siswa-pagination').innerHTML = paginationHtml;
}

function openAddModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Tambah Siswa';
  document.getElementById('siswa-form').reset();
  document.getElementById('pw-group').style.display = '';
  document.getElementById('pwc-group').style.display = '';
  document.getElementById('change-pw-group').style.display = 'none';
  openModal('siswa-modal');
}

async function editSiswa(id) {
  editId = id;
  document.getElementById('modal-title').textContent = 'Edit Siswa';
  document.getElementById('pw-group').style.display = 'none';
  document.getElementById('pwc-group').style.display = 'none';
  document.getElementById('change-pw-group').style.display = '';
  document.getElementById('change-password-check').checked = false;
  document.getElementById('edit-password-fields').style.display = 'none';
  
  const res = await api.get('/students/' + id);
  const s = res.data?.data;
  const f = document.getElementById('siswa-form');
  f.name.value = s.user?.name ?? '';
  f.email.value = s.user?.email ?? '';
  if (f.nis) f.nis.value = s.nis ?? '';
  if (f.no_hp) f.no_hp.value = s.no_hp ?? '';
  if (f.sekolah) f.sekolah.value = s.sekolah ?? '';
  if (f.alamat) f.alamat.value = s.alamat ?? '';
  openModal('siswa-modal');
}

async function saveSiswa() {
  const f = document.getElementById('siswa-form');
  const body = { 
    name: f.name.value, 
    email: f.email.value, 
    nis: f.nis?.value, 
    no_hp: f.no_hp?.value, 
    sekolah: f.sekolah?.value, 
    alamat: f.alamat?.value 
  };
  
  if (!editId) { 
    body.password = f.password.value; 
    body.password_confirmation = f.password_confirmation.value; 
  } else if (document.getElementById('change-password-check')?.checked) {
    body.password = f.new_password?.value;
    body.password_confirmation = f.new_password_confirmation?.value;
  }
  
  const res = editId ? await api.put('/students/' + editId, body) : await api.post('/students', body);
  if (res.ok) { 
    toast('Data berhasil disimpan'); 
    closeModal('siswa-modal'); 
    loadSiswa(currentPage); 
  } else {
    toast(res.data?.message ?? 'Gagal menyimpan', 'error');
  }
}

async function approve(id) {
  confirmDialog('Setujui akun siswa ini?', async () => {
    const r = await api.post('/students/' + id + '/approve');
    r.ok ? (toast('Akun disetujui'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function reject(id) {
  confirmDialog('Tolak akun siswa ini?', async () => {
    const r = await api.post('/students/' + id + '/reject');
    r.ok ? (toast('Akun ditolak', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function suspend(id) {
  confirmDialog('Nonaktifkan akun siswa ini?', async () => {
    const r = await api.post('/students/' + id + '/suspend');
    r.ok ? (toast('Akun dinonaktifkan', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function unsuspend(id) {
  confirmDialog('Aktifkan kembali akun ini?', async () => {
    const r = await api.post('/students/' + id + '/unsuspend');
    r.ok ? (toast('Akun diaktifkan', 'success'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function deleteSiswa(id) {
  confirmDialog('Hapus data siswa ini secara permanen?', async () => {
    const r = await api.delete('/students/' + id);
    r.ok ? (toast('Siswa dihapus', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal hapus', 'error');
  });
}

document.addEventListener('DOMContentLoaded', () => {
  loadBuses();
  loadSiswa(1);
});
</script>
@endpush
