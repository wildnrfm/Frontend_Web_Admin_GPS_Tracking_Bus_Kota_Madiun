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
  <select class="filter-select" id="filter-status" onchange="loadSiswa()">
    <option value="">Semua Status</option>
    <option value="approved">Disetujui</option>
    <option value="pending">Pending</option>
    <option value="rejected">Ditolak</option>
    <option value="suspended">Suspend</option>
  </select>
  <button class="btn btn-icon" onclick="loadSiswa()" title="Refresh"><span class="material-icons">refresh</span></button>
</div>

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
let editId = null, currentPage = 1;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

async function loadSiswa(page = 1) {
  currentPage = page;
  const q      = document.getElementById('search').value;
  const status = document.getElementById('filter-status').value;
  const res    = await api.get('/students', { search: q, status, page, per_page: 15 });
  const rows   = res.data?.data ?? [];
  const meta   = res.data?.meta;
  const tbody  = document.getElementById('siswa-tbody');

  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><span class="material-icons">school</span><p>Tidak ada data siswa</p></div></td></tr>`;
    document.getElementById('siswa-pagination').innerHTML = '';
    return;
  }

  tbody.innerHTML = rows.map((s, i) => `
    <tr>
      <td>${(page-1)*15 + i + 1}</td>
      <td><div style="font-weight:600">${s.name}</div></td>
      <td style="color:var(--c-text-grey);font-size:12px">${s.email}</td>
      <td>${s.student?.nis ?? '-'}</td>
      <td style="font-size:12px">${s.student?.sekolah ?? '-'}</td>
      <td>${statusBadge(s.student?.approval_status ?? s.status)}</td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          ${s.student?.approval_status === 'pending' ? `
            <button class="btn btn-xs btn-primary" onclick="approve(${s.id})">Setujui</button>
            <button class="btn btn-xs" style="background:#FDECEA;color:var(--c-red)" onclick="reject(${s.id})">Tolak</button>
          ` : ''}
          ${s.student?.approval_status === 'approved' ? `
            <button class="btn btn-xs btn-outline" onclick="editSiswa(${s.id})">Edit</button>
            <button class="btn btn-xs" style="background:#FFF3CD;color:var(--c-amber)" onclick="suspend(${s.id}, '${s.student?.approval_status}')">Suspend</button>
          ` : ''}
          ${s.student?.approval_status === 'suspended' ? `
            <button class="btn btn-xs btn-primary" onclick="suspend(${s.id}, 'suspended')">Aktifkan</button>
          ` : ''}
          <button class="btn btn-xs btn-icon" onclick="deleteSiswa(${s.id})" title="Hapus"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');

  document.getElementById('siswa-pagination').innerHTML = meta ? renderPagination(meta, (p) => loadSiswa(p)) : '';
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
  const s = res.data?.data ?? res.data;
  const f = document.getElementById('siswa-form');
  f.name.value = s.name ?? '';
  f.email.value = s.email ?? '';
  if (f.nis) f.nis.value = s.student?.nis ?? '';
  if (f.no_hp) f.no_hp.value = s.no_hp ?? '';
  if (f.sekolah) f.sekolah.value = s.student?.sekolah ?? '';
  if (f.alamat) f.alamat.value = s.student?.alamat ?? '';
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

async function suspend(id, currentStatus) {
  const isSuspended = currentStatus === 'suspended';
  const msg = isSuspended ? 'Aktifkan kembali akun ini?' : 'Suspend akun siswa ini?';
  confirmDialog(msg, async () => {
    const r = isSuspended ? await api.post('/students/' + id + '/unsuspend') : await api.post('/students/' + id + '/suspend');
    r.ok ? (toast(isSuspended ? 'Akun diaktifkan' : 'Akun disuspend', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function deleteSiswa(id) {
  confirmDialog('Hapus data siswa ini secara permanen?', async () => {
    const r = await api.delete('/students/' + id);
    r.ok ? (toast('Siswa dihapus', 'warn'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal hapus', 'error');
  });
}

loadSiswa();
</script>
@endpush
