@extends('admin.layouts.app')
@section('title','Manajemen Driver')
@section('page-title','Driver')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama, email..." oninput="debounce(loadDriver,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadDriver()"><span class="material-icons">refresh</span></button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>No HP</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody id="driver-tbody">
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="driver-pagination" style="padding:12px 14px"></div>
</div>

<div class="modal-overlay" id="driver-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="d-modal-title">Tambah Driver</div>
      <button class="modal-close" onclick="closeModal('driver-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <form id="driver-form">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px">
          <div class="form-group"><label class="form-label">Nama</label>
            <input class="form-control" name="name" placeholder="Nama lengkap" required></div>
          <div class="form-group"><label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" placeholder="email@example.com" required></div>
          <div class="form-group"><label class="form-label">No HP</label>
            <input class="form-control" name="no_hp" placeholder="08xxxxxxxx"></div>
          <div class="form-group"><label class="form-label">Alamat</label>
            <input class="form-control" name="alamat" placeholder="Alamat"></div>
          <div class="form-group" id="d-pw-group"><label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" placeholder="Min. 8 karakter"></div>
          <div class="form-group" id="d-pwc-group"><label class="form-label">Konfirmasi Password</label>
            <input class="form-control" name="password_confirmation" type="password"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('driver-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveDriver()">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

async function loadDriver(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/drivers', { search: q, page, per_page: 15 });
  const rows = res.data ?? [];
  const meta = res.pagination;
  const tbody = document.getElementById('driver-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><span class="material-icons">badge</span><p>Tidak ada driver</p></div></td></tr>`;
    document.getElementById('driver-pagination').innerHTML = '';
    return;
  }
  tbody.innerHTML = rows.map((d, i) => `
    <tr>
      <td>${(page-1)*15+i+1}</td>
      <td><div style="font-weight:600">${d.user?.name ?? d.name ?? '-'}</div></td>
      <td style="color:var(--c-text-grey);font-size:12px">${d.user?.email ?? d.email ?? '-'}</td>
      <td>${d.no_hp ?? '-'}</td>
      <td>${statusBadge(d.user?.status ?? 'active')}</td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-xs btn-outline" onclick="editDriver(${d.id})">Edit</button>
          <button class="btn btn-xs btn-icon" onclick="deleteDriver(${d.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  document.getElementById('driver-pagination').innerHTML = meta ? renderPagination(meta, p => loadDriver(p)) : '';
}

function openAddModal() {
  editId = null;
  document.getElementById('d-modal-title').textContent = 'Tambah Driver';
  document.getElementById('driver-form').reset();
  document.getElementById('d-pw-group').style.display = '';
  document.getElementById('d-pwc-group').style.display = '';
  openModal('driver-modal');
}

async function editDriver(id) {
  editId = id;
  document.getElementById('d-modal-title').textContent = 'Edit Driver';
  document.getElementById('d-pw-group').style.display = 'none';
  document.getElementById('d-pwc-group').style.display = 'none';
  const res = await api.get('/drivers/' + id);
  const d = res.data;
  const f = document.getElementById('driver-form');
  f.name.value = d.user?.name ?? d.name ?? '';
  f.email.value = d.user?.email ?? d.email ?? '';
  f.no_hp.value = d.no_hp ?? '';
  f.alamat.value = d.user?.alamat ?? '';
  openModal('driver-modal');
}

async function saveDriver() {
  const f = document.getElementById('driver-form');
  const body = { name: f.name.value, email: f.email.value, no_hp: f.no_hp.value, alamat: f.alamat.value };
  if (!editId) { body.password = f.password.value; body.password_confirmation = f.password_confirmation.value; }
  const res = editId ? await api.put('/drivers/' + editId, body) : await api.post('/drivers', body);
  res.ok ? (toast('Data berhasil disimpan'), closeModal('driver-modal'), loadDriver(currentPage))
         : toast(res.data?.message ?? 'Gagal menyimpan', 'error');
}

async function deleteDriver(id) {
  confirmDialog('Hapus driver ini?', async () => {
    const r = await api.delete('/drivers/' + id);
    r.ok ? (toast('Driver dihapus', 'warn'), loadDriver(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

loadDriver();
</script>
@endpush
