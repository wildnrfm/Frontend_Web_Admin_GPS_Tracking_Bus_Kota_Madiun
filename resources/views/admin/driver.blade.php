@extends('admin.layouts.app')
@section('title','Manajemen Driver')
@section('page-title','Driver')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<style>
/* ╔══════════════════════════════════════════════════════════════╗ */
/* ║              DRIVER PAGE REDESIGN STYLES                      ║ */
/* ╚══════════════════════════════════════════════════════════════╝ */

/* Hero Card - Green Gradient (Driver Theme) */
.driver-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px;
  color: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.24);
  margin-bottom: 24px;
}
.driver-hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  pointer-events: none;
}
.driver-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.04);
  pointer-events: none;
}
.driver-hero-top {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 2;
}
.driver-hero-icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
}
.driver-hero-text h2 {
  margin: 0; font-size: 24px; font-weight: 700; color: #fff;
  letter-spacing: -0.3px;
}
.driver-hero-text p {
  margin: 4px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.75);
}

/* Filter Bar Improvements */
.driver-filter-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding: 12px 0;
  flex-wrap: wrap;
}
.driver-filter-bar .search-box {
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

/* Premium Action Icons - Green Theme */
.btn-purple-icon {
  background: rgba(46, 125, 82, 0.12);
  color: #1B5E37;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-purple-icon:hover {
  background: rgba(46, 125, 82, 0.2);
  transform: translateY(-1px);
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
</style>

{{-- Hero Card --}}
<div class="driver-hero">
  <div class="driver-hero-top">
    <div class="driver-hero-icon"><span class="material-icons">local_shipping</span></div>
    <div class="driver-hero-text">
      <h2>Manajemen Driver</h2>
      <p>Kelola profil pengemudi, kendaraan yang ditugaskan, dan jadwal perjalanan</p>
    </div>
  </div>
</div>

{{-- Filter & Search Bar --}}
<div class="driver-filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama driver, NIK..." oninput="debounce(loadDriver,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadDriver()"><span class="material-icons">refresh</span></button>
  <div style="flex: 1;"></div>
  <button class="filter-btn active" data-filter="all" onclick="setDriverFilter('all', this)">Semua</button>
  <button class="filter-btn" data-filter="online" onclick="setDriverFilter('online', this)">🟢 Online</button>
  <button class="filter-btn" data-filter="offline" onclick="setDriverFilter('offline', this)">🔴 Offline</button>
  <button class="filter-btn" data-filter="no-bus" onclick="setDriverFilter('no-bus', this)">No-Bus</button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Foto</th><th>Nama</th><th>Email</th><th>No HP</th><th>Status GPS</th><th>Status Akun</th><th>Status Bus</th><th>Aksi</th></tr></thead>
      <tbody id="driver-tbody">
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="driver-pagination" style="padding:12px 14px"></div>
</div>

<div class="modal-overlay" id="driver-modal">
  <div class="modal" style="max-width:680px; border-radius:16px; overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border-bottom:none; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,0.18); color:#fff; display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">badge</span>
        </div>
        <div>
          <div class="modal-title" id="d-modal-title" style="font-weight:700; font-size:16px; color:#fff; margin:0;">Tambah Driver</div>
          <div style="font-size:11px; color:rgba(255,255,255,0.75); margin-top:2px;">Lengkapi informasi data diri driver</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('driver-modal')" style="color:#fff"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body" style="padding:24px;">
      <form id="driver-form">
        <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items:start;">
          
          <!-- Kiri: Form Inputs -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 14px">
            <div class="form-group">
              <label class="form-label">Nama</label>
              <input class="form-control" name="name" placeholder="Nama lengkap" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" placeholder="email@example.com" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">No HP (WhatsApp)</label>
              <input class="form-control" name="no_hp" placeholder="08xxxxxxxx" required style="border-radius:10px;">
            </div>
            <div class="form-group">
              <label class="form-label">NIK (KTP)</label>
              <input class="form-control" name="nik" placeholder="16 digit NIK" required style="border-radius:10px;">
            </div>
            <div class="form-group" style="grid-column: span 2;">
              <label class="form-label">Alamat</label>
              <input class="form-control" name="alamat" placeholder="Alamat lengkap" required style="border-radius:10px;">
            </div>
            <div class="form-group" id="d-pw-group">
              <label class="form-label">Password</label>
              <input class="form-control" name="password" type="password" placeholder="Min. 8 karakter" style="border-radius:10px;">
            </div>
            <div class="form-group" id="d-pwc-group">
              <label class="form-label">Konfirmasi Password</label>
              <input class="form-control" name="password_confirmation" type="password" placeholder="Ulangi password" style="border-radius:10px;">
            </div>
            
            <div class="form-group" id="change-pw-group" style="grid-column:1/-1; display:none">
              <label class="form-checkbox">
                <input type="checkbox" id="change-password-check" onchange="togglePasswordFields()">
                <span>Ganti Password Driver</span>
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
            
            <div class="photo-upload-zone" id="driver-photo-upload-zone" onclick="document.getElementById('driver-photo-input').click()">
              <span class="material-icons">add_a_photo</span>
              <p>Pilih Foto</p>
              <span>Format JPG/PNG, maks 2MB</span>
            </div>
            <input type="file" id="driver-photo-input" name="photo" accept="image/*" style="display:none">
            
            <div id="driver-photo-preview" style="display:none">
              <div class="preview-container">
                <img id="driver-photo-img" src="" alt="Preview">
                <button type="button" class="remove-preview-btn" onclick="removeDriverPhotoSelection(event)" title="Hapus foto">
                  <span class="material-icons" style="font-size:16px">delete</span>
                </button>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
    <div class="modal-footer" style="background:#f8faf9; border-top:1px solid #eef2f0; padding:16px 24px; display:flex; justify-content:flex-end; gap:12px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('driver-modal')" style="border-radius:8px;">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveDriver()" style="border-radius:8px; background:linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%); border:none;">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Chat/Hubungi --}}
<div class="modal-overlay" id="chat-modal">
  <div class="modal" style="max-width:400px; border-radius:20px; overflow:hidden;">
    <div class="modal-header" style="background:#f8faf9; border-bottom:1px solid #eef2f0; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#E8F5ED; color:var(--c-primary); display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">chat</span>
        </div>
        <div>
          <div class="modal-title" style="font-weight:700; font-size:16px; color:var(--c-text-dark); margin:0;">Hubungi Driver</div>
          <div style="font-size:11px; color:var(--c-text-grey); margin-top:2px;">Kontak WhatsApp atau Salin Nomor</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('chat-modal')"><span class="material-icons">close</span></button>
    </div>
    
    <div class="modal-body" style="padding:24px; text-align:center;">
      <div style="font-size:14px; color:var(--c-text-grey); margin-bottom:6px;">Hubungi</div>
      <div id="chat-driver-name" style="font-weight:700; font-size:20px; color:var(--c-text-dark); margin-bottom:4px;">Nama Driver</div>
      <div id="chat-driver-phone" style="font-size:22px; font-weight:700; color:var(--c-primary); letter-spacing:0.5px; margin-bottom:24px;">08xxxxxxxxxx</div>
      
      <div style="display:flex; flex-direction:column; gap:12px;">
        <a id="wa-btn" href="#" target="_blank" class="btn btn-primary" style="background:#25D366; border-color:#25D366; color:white; border-radius:12px; padding:12px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; transition: all 0.2s;">
          <span class="material-icons" style="font-size:20px">chat</span>
          Buka WhatsApp
        </a>
        <button onclick="copyDriverNumber()" class="btn btn-outline" style="border-radius:12px; padding:12px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px; border-color:var(--c-border); background:white;">
          <span class="material-icons" style="font-size:20px">content_copy</span>
          Salin Nomor
        </button>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1, currentFilter = 'all';
let selectedDriverPhone = '';
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

function setDriverFilter(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadDriver(1);
}

async function loadDriver(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/drivers', { search: q, per_page: 1000 });
  let rows = res.data?.data ?? [];
  const tbody = document.getElementById('driver-tbody');
  
  // Apply online/offline/no-bus filters
  if (currentFilter !== 'all') {
    rows = rows.filter(d => {
      const buses = d.buses ?? [];
      const isOnline = buses.some(b => b.pivot?.gps_status === 'on');
      const hasActiveBus = buses.some(b => {
        const end = b.pivot?.tanggal_selesai;
        return !end || end >= new Date().toISOString().split('T')[0];
      });
      if (currentFilter === 'online') return isOnline;
      if (currentFilter === 'offline') return !isOnline;
      if (currentFilter === 'no-bus') return !hasActiveBus;
      return true;
    });
  }

  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><span class="material-icons">badge</span><p>Tidak ada driver ditemukan</p></div></td></tr>`;
    document.getElementById('driver-pagination').innerHTML = '';
    return;
  }
  
  const perPage = 15;
  const total = rows.length;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);

  tbody.innerHTML = paginatedRows.map((d, i) => {
    const buses = d.buses ?? [];
    const isOnline = buses.some(b => b.pivot?.gps_status === 'on');
    const isSuspended = d.user?.is_suspended ?? false;
    const name = d.user?.name ?? d.name ?? '-';
    const activeBuses = buses.filter(b => {
      const end = b.pivot?.tanggal_selesai;
      return !end || end >= new Date().toISOString().split('T')[0];
    });
    const hasBus = activeBuses.length > 0;
    
    return `
      <tr>
        <td>
          <div style="width:40px; height:40px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f0f4f2; border:1px solid #dde6e0;">
            <img src="${d.user?.photo_url ? proxyImgUrl(d.user.photo_url) : '/images/driver/default.svg'}" 
                 alt="" 
                 style="width:100%; height:100%; object-fit:cover;"
                 onerror="this.src='/images/driver/default.svg'">
          </div>
        </td>
        <td><div style="font-weight:600">${name}</div></td>
        <td style="color:var(--c-text-grey);font-size:12px">${d.user?.email ?? d.email ?? '-'}</td>
        <td>${d.no_hp ?? '-'}</td>
        <td>
          <span class="badge ${isOnline ? 'badge-green' : 'badge-red'}">
            ${isOnline ? '🟢 Online' : '🔴 Offline'}
          </span>
        </td>
        <td>${statusBadge(isSuspended ? 'suspended' : 'active')}</td>
        <td>
          <span style="font-size:12px;padding:4px 10px;border-radius:999px;display:inline-block;${hasBus ? 'background:#e7f5e6;color:#1b5e20' : 'background:#fff4e5;color:#b15f00'}">
            ${hasBus ? 'Bus Aktif' : 'No-Bus'}
          </span>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn-purple-icon" onclick="openChatModal('${name.replace(/'/g, "\\'")}', '${d.no_hp ?? ''}')" title="Hubungi Driver">
              <span class="material-icons" style="font-size:16px">chat</span>
            </button>
            <button class="btn-gray-icon" onclick="editDriver(${d.id})" title="Edit Driver">
              <span class="material-icons" style="font-size:16px">edit</span>
            </button>
            <button class="btn-red-icon" onclick="deleteDriver(${d.id})" title="Hapus Driver">
              <span class="material-icons" style="font-size:16px">delete</span>
            </button>
          </div>
        </td>
      </tr>`;
  }).join('');

  const totalPages = Math.ceil(total / perPage);
  const meta = {
    current_page: page,
    last_page: totalPages,
    total: total
  };
  document.getElementById('driver-pagination').innerHTML = renderPagination(meta, p => loadDriver(p));
}

function removeDriverPhotoSelection(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById('driver-photo-input');
  if (input) input.value = '';
  document.getElementById('driver-photo-img').src = '';
  document.getElementById('driver-photo-preview').style.display = 'none';
  document.getElementById('driver-photo-upload-zone').style.display = 'flex';
}

function openAddModal() {
  editId = null;
  document.getElementById('d-modal-title').textContent = 'Tambah Driver';
  document.getElementById('driver-form').reset();
  removeDriverPhotoSelection();
  document.getElementById('d-pw-group').style.display = '';
  document.getElementById('d-pwc-group').style.display = '';
  document.getElementById('change-pw-group').style.display = 'none';
  openModal('driver-modal');
}

async function editDriver(id) {
  editId = id;
  document.getElementById('d-modal-title').textContent = 'Edit Driver';
  document.getElementById('d-pw-group').style.display = 'none';
  document.getElementById('d-pwc-group').style.display = 'none';
  document.getElementById('change-pw-group').style.display = 'block';
  document.getElementById('change-password-check').checked = false;
  document.getElementById('edit-password-fields').style.display = 'none';
  removeDriverPhotoSelection();
  
  const res = await api.get('/drivers/' + id);
  const d = res.data?.data;
  const f = document.getElementById('driver-form');
  f.name.value = d.user?.name ?? d.name ?? '';
  f.email.value = d.user?.email ?? d.email ?? '';
  f.no_hp.value = d.no_hp ?? '';
  f.nik.value = d.nik ?? '';
  f.alamat.value = d.alamat ?? d.user?.alamat ?? '';
  
  if (d.user?.photo_url) {
    document.getElementById('driver-photo-img').src = proxyImgUrl(d.user.photo_url);
    document.getElementById('driver-photo-preview').style.display = 'block';
    document.getElementById('driver-photo-upload-zone').style.display = 'none';
  }
  
  openModal('driver-modal');
}

async function saveDriver() {
  const f = document.getElementById('driver-form');
  const formData = new FormData();
  formData.append('name', f.name.value);
  formData.append('email', f.email.value);
  formData.append('no_hp', f.no_hp.value);
  formData.append('nik', f.nik.value);
  formData.append('alamat', f.alamat.value);
  
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
    await api.postForm('/drivers/' + editId, formData) : 
    await api.postForm('/drivers', formData);
    
  if (res.ok) {
    toast('Data berhasil disimpan');
    closeModal('driver-modal');
    loadDriver(currentPage);
  } else {
    toast(res.data?.message ?? 'Gagal menyimpan', 'error');
  }
}

async function deleteDriver(id) {
  confirmDialog('Hapus driver ini?', async () => {
    const r = await api.delete('/drivers/' + id);
    r.ok ? (toast('Driver dihapus', 'warn'), loadDriver(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

// ── WhatsApp & Chat Modal Helper ──
function formatWhatsAppNumber(phone) {
  if (!phone) return '';
  let cleaned = phone.replace(/[^0-9]/g, '');
  if (cleaned.startsWith('0')) {
    cleaned = '62' + cleaned.substring(1);
  }
  return cleaned;
}

function openChatModal(name, phone) {
  selectedDriverPhone = phone;
  document.getElementById('chat-driver-name').textContent = name;
  document.getElementById('chat-driver-phone').textContent = phone || 'Tidak ada nomor HP';
  
  const waBtn = document.getElementById('wa-btn');
  if (phone) {
    const waPhone = formatWhatsAppNumber(phone);
    waBtn.href = `https://wa.me/${waPhone}`;
    waBtn.style.display = 'flex';
  } else {
    waBtn.href = '#';
    waBtn.style.display = 'none';
  }
  
  openModal('chat-modal');
}

function copyDriverNumber() {
  if (!selectedDriverPhone) {
    toast('Tidak ada nomor untuk disalin', 'error');
    return;
  }
  navigator.clipboard.writeText(selectedDriverPhone).then(() => {
    toast('Nomor telepon berhasil disalin');
  }).catch(() => {
    toast('Gagal menyalin nomor', 'error');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const photoInput = document.getElementById('driver-photo-input');
  if (photoInput) {
    photoInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('driver-photo-img').src = event.target.result;
          document.getElementById('driver-photo-preview').style.display = 'block';
          document.getElementById('driver-photo-upload-zone').style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

loadDriver();
</script>
@endpush
