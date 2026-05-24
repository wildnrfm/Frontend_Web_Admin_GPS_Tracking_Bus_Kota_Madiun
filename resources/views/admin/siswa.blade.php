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
  border-color: var(--c-primary);
  background: var(--c-primary-light);
}
.photo-upload-zone .material-icons {
  font-size: 32px;
  color: var(--c-primary);
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
</style>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Foto</th><th>Nama</th><th>Email</th><th>NIS</th><th>Kelas</th><th>Sekolah</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody id="siswa-tbody">
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="siswa-pagination" style="padding:12px 14px"></div>
</div>

{{-- Modal Tambah/Edit --}}
<div class="modal-overlay" id="siswa-modal">
  <div class="modal" style="max-width:680px; border-radius:16px; overflow:hidden;">
    <div class="modal-header" style="background:#f8faf9; border-bottom:1px solid #eef2f0; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:var(--c-primary-light); color:var(--c-primary); display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">school</span>
        </div>
        <div>
          <div class="modal-title" id="modal-title" style="font-weight:700; font-size:16px; color:var(--c-text-dark); margin:0;">Tambah Siswa</div>
          <div style="font-size:11px; color:var(--c-text-grey); margin-top:2px;">Lengkapi informasi data diri siswa</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('siswa-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body" style="padding:24px;">
      <form id="siswa-form">
        <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items:start;">
          
          <!-- Kiri: Form Inputs -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 14px">
            <div class="form-group">
              <label class="form-label">Nama Lengkap</label>
              <input class="form-control" name="name" placeholder="Nama lengkap" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" placeholder="email@example.com" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">NIS</label>
              <input class="form-control" name="nis" placeholder="NIS siswa" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">No HP</label>
              <input class="form-control" name="no_hp" placeholder="08xxxxxxxx" style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">Kelas</label>
              <input class="form-control" name="kelas" placeholder="Contoh: XII IPA 1" style="border-radius:10px;">
            </div>
            <div class="form-group" style="grid-column:1/-1">
              <label class="form-label">Sekolah</label>
              <input class="form-control" name="sekolah" placeholder="Nama sekolah" required style="border-radius:10px;">
            </div>
            <div class="form-group" style="grid-column:1/-1">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap" style="border-radius:10px;"></textarea>
            </div>
            <div class="form-group" id="pw-group">
              <label class="form-label">Password</label>
              <input class="form-control" name="password" type="password" placeholder="Min. 8 karakter" style="border-radius:10px;">
            </div>
            <div class="form-group" id="pwc-group">
              <label class="form-label">Konfirmasi Password</label>
              <input class="form-control" name="password_confirmation" type="password" placeholder="Ulangi password" style="border-radius:10px;">
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
                <input class="form-control" name="new_password" type="password" placeholder="Min. 8 karakter" style="border-radius:10px;">
              </div>
              <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input class="form-control" name="new_password_confirmation" type="password" placeholder="Ulangi password" style="border-radius:10px;">
              </div>
            </div>
          </div>

          <!-- Kanan: Upload Foto -->
          <div style="display:flex; flex-direction:column; gap:8px;">
            <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Foto Profil</label>
            
            <div class="photo-upload-zone" id="siswa-photo-upload-zone" onclick="document.getElementById('siswa-photo-input').click()">
              <span class="material-icons">add_a_photo</span>
              <p>Pilih Foto</p>
              <span>Format JPG/PNG, maks 2MB</span>
            </div>
            <input type="file" id="siswa-photo-input" name="photo" accept="image/*" style="display:none">
            
            <div id="siswa-photo-preview" style="display:none">
              <div class="preview-container">
                <img id="siswa-photo-img" src="" alt="Preview">
                <button type="button" class="remove-preview-btn" onclick="removeSiswaPhotoSelection(event)" title="Hapus foto">
                  <span class="material-icons" style="font-size:16px">delete</span>
                </button>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
    <div class="modal-footer" style="background:#f8faf9; border-top:1px solid #eef2f0; padding:16px 24px; display:flex; justify-content:flex-end; gap:12px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')" style="border-radius:8px;">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveSiswa()" id="save-btn" style="border-radius:8px;">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Pilih Bus --}}
<div class="modal-overlay" id="pilih-bus-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tetapkan Bus untuk <span id="pilih-bus-siswa-name"></span></div>
      <button class="modal-close" onclick="closeModal('pilih-bus-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Pilih Bus</label>
        <select class="form-control" id="pilih-bus-select">
          <option value="">-- Pilih Bus --</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('pilih-bus-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveBusOnlyAssignment()">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Pilih Halte --}}
<div class="modal-overlay" id="pilih-halte-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tetapkan Halte untuk <span id="pilih-halte-siswa-name"></span></div>
      <button class="modal-close" onclick="closeModal('pilih-halte-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Pilih Halte</label>
        <select class="form-control" id="pilih-halte-select">
          <option value="">-- Pilih Halte --</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('pilih-halte-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveHalteOnlyAssignment()">Simpan</button>
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
  const select = document.getElementById('pilih-bus-select');
  if (select) {
    select.innerHTML = '<option value="">-- Pilih Bus --</option>' +
      busesList.map(b => `<option value="${b.id}">${b.kode_bus} (${b.plat_nomor})</option>`).join('');
  }
}

function togglePasswordFields() {
  const check = document.getElementById('change-password-check')?.checked;
  document.getElementById('edit-password-fields').style.display = check ? '' : 'none';
}

async function openBusModal(siswaId, siswaName, mode = 'bus') {
  currentStudentId = siswaId;
  
  try {
    const res = await api.get('/students/' + siswaId);
    const student = res.data?.data;
    const currentBusId = student?.bus_id || '';
    const currentHalteId = student?.halte_id || '';

    if (mode === 'bus') {
      document.getElementById('pilih-bus-siswa-name').textContent = siswaName;
      document.getElementById('pilih-bus-select').value = currentBusId;
      openModal('pilih-bus-modal');
    } else if (mode === 'halte') {
      if (!currentBusId) {
        toast('Siswa harus ditugaskan ke bus terlebih dahulu', 'error');
        return;
      }
      
      document.getElementById('pilih-halte-siswa-name').textContent = siswaName;
      document.getElementById('pilih-halte-select').innerHTML = '<option value="">-- Memuat Halte... --</option>';
      openModal('pilih-halte-modal');
      
      // Load haltes of current bus
      await loadHaltesForBusId(currentBusId);
      document.getElementById('pilih-halte-select').value = currentHalteId;
    }
  } catch (e) {
    toast('Gagal memuat detail data siswa', 'error');
    console.error(e);
  }
}

async function loadHaltesForBusId(busId) {
  try {
    const res = await api.get('/buses/' + busId, {});
    const bus = res.data?.data;
    const routes = bus?.routes ?? [];
    let haltes = [];
    
    for (const route of routes) {
      const routeHaltes = route.haltes ?? [];
      haltes = haltes.concat(routeHaltes);
    }
    
    haltes = haltes.filter((h, i, arr) => arr.findIndex(x => x.id === h.id) === i);
    
    const select = document.getElementById('pilih-halte-select');
    select.innerHTML = '<option value="">-- Pilih Halte --</option>' +
      haltes.map(h => `<option value="${h.id}">${h.nama_halte}</option>`).join('');
  } catch (e) {
    toast('Gagal memuat daftar halte', 'error');
    console.error(e);
  }
}

async function saveBusOnlyAssignment() {
  const busId = document.getElementById('pilih-bus-select').value;
  if (!busId) {
    toast('Silakan pilih bus terlebih dahulu', 'error');
    return;
  }

  try {
    // Ambil detail siswa untuk mengecek dan menggunakan halte_id saat ini
    const resStudent = await api.get('/students/' + currentStudentId);
    const student = resStudent.data?.data;
    let currentHalteId = student?.halte_id || '';

    // Ambil detail bus baru untuk mendapatkan daftar haltenya
    const resBus = await api.get('/buses/' + busId);
    const bus = resBus.data?.data;
    const routes = bus?.routes ?? [];
    let haltes = [];
    for (const route of routes) {
      haltes = haltes.concat(route.haltes ?? []);
    }
    
    // Pastikan ada halte di rute bus ini
    if (haltes.length === 0) {
      toast('Rute bus ini belum memiliki halte. Silakan atur rute & halte bus terlebih dahulu.', 'error');
      return;
    }

    // Cek apakah halte saat ini valid (ada di dalam rute bus baru)
    const isHalteValid = currentHalteId && haltes.some(h => h.id == currentHalteId);
    if (!isHalteValid) {
      // Jika tidak valid atau belum diset, gunakan halte pertama sebagai default
      currentHalteId = haltes[0].id;
    }

    const res = await api.post(`/buses/${busId}/students`, {
      student_id: currentStudentId,
      halte_id: currentHalteId
    });
    
    if (res.ok) {
      toast('Bus berhasil ditetapkan');
      closeModal('pilih-bus-modal');
      loadSiswa(currentPage);
    } else {
      toast(res.data?.message ?? 'Gagal menetapkan bus', 'error');
    }
  } catch (e) {
    toast('Gagal menetapkan bus', 'error');
    console.error(e);
  }
}

async function saveHalteOnlyAssignment() {
  const halteId = document.getElementById('pilih-halte-select').value;
  if (!halteId) {
    toast('Silakan pilih halte terlebih dahulu', 'error');
    return;
  }

  try {
    const resStudent = await api.get('/students/' + currentStudentId);
    const student = resStudent.data?.data;
    const busId = student?.bus_id;

    if (!busId) {
      toast('Siswa harus ditugaskan ke bus terlebih dahulu', 'error');
      return;
    }

    const res = await api.post(`/buses/${busId}/students`, {
      student_id: currentStudentId,
      halte_id: halteId
    });
    
    if (res.ok) {
      toast('Halte berhasil ditetapkan');
      closeModal('pilih-halte-modal');
      loadSiswa(currentPage);
    } else {
      toast(res.data?.message ?? 'Gagal menetapkan halte', 'error');
    }
  } catch (e) {
    toast('Gagal menetapkan halte', 'error');
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
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><span class="material-icons">school</span><p>Tidak ada data siswa</p></div></td></tr>`;
    document.getElementById('siswa-pagination').innerHTML = '';
    return;
  }

  const perPage = 15;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);
  
  tbody.innerHTML = paginatedRows.map((s, i) => `
    <tr>
      <td>
        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f0f4f2; border:1px solid #dde6e0;">
          <img src="${s.user?.photo_url ? proxyImgUrl(s.user.photo_url) : '/images/siswa/default.svg'}" 
               alt="" 
               style="width:100%; height:100%; object-fit:cover;"
               onerror="this.src='/images/siswa/default.svg'">
        </div>
      </td>
      <td><div style="font-weight:600">${s.user?.name ?? 'N/A'}</div></td>
      <td style="color:var(--c-text-grey);font-size:12px">${s.user?.email ?? 'N/A'}</td>
      <td>${s.nis ?? '-'}</td>
      <td>${s.kelas ?? '-'}</td>
      <td style="font-size:12px">${s.sekolah ?? '-'}</td>
      <td>${statusBadge(s.approval_status, s.is_suspended)}</td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          ${s.approval_status === 'pending' ? `
            <button class="btn btn-xs btn-primary" onclick="approve(${s.user_id})">Setujui</button>
            <button class="btn btn-xs" style="background:#FDECEA;color:var(--c-red)" onclick="reject(${s.user_id})">Tolak</button>
          ` : ''}
          ${s.approval_status === 'approved' && !s.is_suspended ? `
            <button class="btn btn-xs" style="background:#E8F5E9;color:#2E7D32;cursor:pointer" onclick="openBusModal(${s.id}, '${s.user?.name}', 'bus')" title="Tetapkan Bus">
              <span class="material-icons" style="font-size:14px">directions_bus</span>
            </button>
            <button class="btn btn-xs" style="background:#E3F2FD;color:#1565C0;cursor:pointer" onclick="openBusModal(${s.id}, '${s.user?.name}', 'halte')" title="Tetapkan Halte">
              <span class="material-icons" style="font-size:14px">place</span>
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

function removeSiswaPhotoSelection(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById('siswa-photo-input');
  if (input) input.value = '';
  document.getElementById('siswa-photo-img').src = '';
  document.getElementById('siswa-photo-preview').style.display = 'none';
  document.getElementById('siswa-photo-upload-zone').style.display = 'flex';
}

function openAddModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Tambah Siswa';
  document.getElementById('siswa-form').reset();
  removeSiswaPhotoSelection();
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
  removeSiswaPhotoSelection();
  
  const res = await api.get('/students/' + id);
  const s = res.data?.data;
  const f = document.getElementById('siswa-form');
  f.name.value = s.user?.name ?? '';
  f.email.value = s.user?.email ?? '';
  if (f.nis) f.nis.value = s.nis ?? '';
  if (f.no_hp) f.no_hp.value = s.no_hp ?? '';
  if (f.kelas) f.kelas.value = s.kelas ?? '';
  if (f.sekolah) f.sekolah.value = s.sekolah ?? '';
  if (f.alamat) f.alamat.value = s.alamat ?? '';
  
  // Show photo preview if exists
  if (s.user?.photo_url) {
    document.getElementById('siswa-photo-img').src = proxyImgUrl(s.user.photo_url);
    document.getElementById('siswa-photo-preview').style.display = 'block';
    document.getElementById('siswa-photo-upload-zone').style.display = 'none';
  }
  
  openModal('siswa-modal');
}

async function saveSiswa() {
  const f = document.getElementById('siswa-form');
  const formData = new FormData();
  formData.append('name', f.name.value);
  formData.append('email', f.email.value);
  if (f.nis?.value) formData.append('nis', f.nis.value);
  if (f.no_hp?.value) formData.append('no_hp', f.no_hp.value);
  if (f.kelas?.value) formData.append('kelas', f.kelas.value);
  if (f.sekolah?.value) formData.append('sekolah', f.sekolah.value);
  if (f.alamat?.value) formData.append('alamat', f.alamat.value);
  
  if (f.photo.files.length > 0) {
    formData.append('photo', f.photo.files[0]);
  }
  
  if (!editId) { 
    formData.append('password', f.password.value); 
    formData.append('password_confirmation', f.password_confirmation.value); 
  } else if (document.getElementById('change-password-check')?.checked) {
    formData.append('password', f.new_password?.value);
    formData.append('password_confirmation', f.new_password_confirmation?.value);
  }
  
  if (editId) {
    formData.append('_method', 'PUT');
  }
  
  const res = editId ? 
    await api.postForm('/students/' + editId, formData) : 
    await api.postForm('/students', formData);
    
  if (res.ok) { 
    toast('Data berhasil disimpan'); 
    closeModal('siswa-modal'); 
    loadSiswa(currentPage); 
  } else {
    toast(res.data?.message ?? 'Gagal menyimpan', 'error');
  }
}

async function approve(userId) {
  confirmDialog('Setujui akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/approve');
    r.ok ? (toast('Akun disetujui'), loadSiswa(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function reject(userId) {
  const reason = prompt('Masukkan alasan penolakan (wajib diisi):');
  if (!reason || !reason.trim()) { toast('Alasan penolakan wajib diisi', 'warn'); return; }
  confirmDialog('Tolak akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/reject', { reason: reason.trim() });
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

  const photoInput = document.getElementById('siswa-photo-input');
  if (photoInput) {
    photoInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('siswa-photo-img').src = event.target.result;
          document.getElementById('siswa-photo-preview').style.display = 'block';
          document.getElementById('siswa-photo-upload-zone').style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});
</script>
@endpush
