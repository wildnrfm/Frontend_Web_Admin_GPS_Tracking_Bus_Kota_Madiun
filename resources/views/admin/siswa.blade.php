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
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveSiswa()" id="save-btn">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1, currentFilter = 'all';
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

function setFilter(filter, btn) {
  currentFilter = filter;
  // Update button styles
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadSiswa(1);
}

function applyClientFilter(rows) {
  if (currentFilter === 'all') return rows;
  
  return rows.filter(s => {
    const userStatus = s.user?.status ?? 'active'; // default to active if not specified
    const hasBus = (s.bus_id ?? 0) > 0;
    
    if (currentFilter === 'active') {
      return userStatus === 'active' && hasBus;
    }
    if (currentFilter === 'no-bus') {
      return userStatus === 'active' && !hasBus;
    }
    if (currentFilter === 'inactive') {
      return userStatus !== 'active';
    }
    return true;
  });
}

async function loadSiswa(page = 1) {
  currentPage = page;
  const q   = document.getElementById('search').value;
  const res = await api.get('/students', { search: q, per_page: 1000 }); // Get all to filter client-side
  
  // Response structure: { ok, status, data: { success, message, data: [...], pagination: {...} } }
  let rows = res.data?.data ?? [];
  
  // Apply client-side filtering
  rows = applyClientFilter(rows);
  
  const tbody = document.getElementById('siswa-tbody');
  
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><span class="material-icons">school</span><p>Tidak ada data siswa</p></div></td></tr>`;
    document.getElementById('siswa-pagination').innerHTML = '';
    return;
  }

  // Paginate locally
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
      <td>${statusBadge(s.approval_status)}</td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          ${s.approval_status === 'pending' ? `
            <button class="btn btn-xs btn-primary" onclick="approve(${s.id})">Setujui</button>
            <button class="btn btn-xs" style="background:#FDECEA;color:var(--c-red)" onclick="reject(${s.id})">Tolak</button>
          ` : ''}
          ${s.approval_status === 'approved' ? `
            <button class="btn btn-xs btn-outline" onclick="editSiswa(${s.id})">Edit</button>
            <button class="btn btn-xs" style="background:#FFF3CD;color:var(--c-amber)" onclick="suspend(${s.id})">Suspend</button>
          ` : ''}
          ${s.approval_status === 'suspended' ? `
            <button class="btn btn-xs btn-primary" onclick="unsuspend(${s.id})">Aktifkan</button>
          ` : ''}
          <button class="btn btn-xs btn-icon" onclick="deleteSiswa(${s.id})" title="Hapus"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');

  // Render pagination
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
  openModal('siswa-modal');
}

async function editSiswa(id) {
  editId = id;
  document.getElementById('modal-title').textContent = 'Edit Siswa';
  document.getElementById('pw-group').style.display = 'none';
  document.getElementById('pwc-group').style.display = 'none';
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
  const body = { name: f.name.value, email: f.email.value, nis: f.nis?.value, no_hp: f.no_hp?.value, sekolah: f.sekolah?.value, alamat: f.alamat?.value };
  if (!editId) { body.password = f.password.value; body.password_confirmation = f.password_confirmation.value; }
  const res = editId ? await api.put('/students/' + editId, body) : await api.post('/students', body);
  if (res.ok) { toast('Data berhasil disimpan'); closeModal('siswa-modal'); loadSiswa(currentPage); }
  else toast(res.data?.message ?? 'Gagal menyimpan', 'error');
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
  confirmDialog('Suspend akun siswa ini?', async () => {
    const r = await api.post('/students/' + id + '/suspend');
    r.ok ? (toast('Akun disuspend', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
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

try {
  debugLog('[DEBUG] Calling loadSiswa()...');
  loadSiswa();
} catch (e) {
  debugLog(`[ERROR] Error in loadSiswa: ${e.message}` );
}

// Create visual debug panel
const debugPanel = document.createElement('div');
debugPanel.id = 'debug-panel';
debugPanel.style.cssText = 'position:fixed;bottom:10px;left:10px;background:#000;color:#0f0;padding:10px;font-family:monospace;font-size:11px;max-width:500px;max-height:250px;overflow-y:auto;z-index:9999;border:2px solid #0f0';
document.body.appendChild(debugPanel);

// Assign to global window so debugLog can update it
window.debugPanel = debugPanel;

// Show token info
const tokenMeta = document.querySelector('meta[name="admin-token"]')?.content;
const div1 = document.createElement('div');
div1.textContent = `Token: ${tokenMeta ? tokenMeta.substring(0,25) + '...' : 'EMPTY'}`;
debugPanel.appendChild(div1);

const div2 = document.createElement('div');
div2.textContent = `API: localhost:8000`;
debugPanel.appendChild(div2);

const div3 = document.createElement('div');
div3.textContent = `---`;
debugPanel.appendChild(div3);

debugLog('[PAGE] siswa.blade.php loaded');
</script>
@endpush
