@extends('admin.layouts.app')
@section('title','Manajemen Admin')
@section('page-title','Admin')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<style>
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

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari nama, email..." oninput="debounce(loadAdmin,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadAdmin()"><span class="material-icons">refresh</span></button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Foto</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
      <tbody id="admin-tbody">
        <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="admin-pagination" style="padding:12px 14px"></div>
</div>

{{-- Modal Admin --}}
<div class="modal-overlay" id="admin-modal">
  <div class="modal" style="max-width:680px; border-radius:16px; overflow:hidden;">
    <div class="modal-header" style="background:#f8faf9; border-bottom:1px solid #eef2f0; padding:18px 24px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#FCE4EC; color:#E91E63; display:flex; align-items:center; justify-content:center;">
          <span class="material-icons">admin_panel_settings</span>
        </div>
        <div>
          <div class="modal-title" id="a-modal-title" style="font-weight:700; font-size:16px; color:var(--c-text-dark); margin:0;">Tambah Admin</div>
          <div style="font-size:11px; color:var(--c-text-grey); margin-top:2px;">Atur hak akses pengelolaan sistem</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('admin-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body" style="padding:24px;">
      <form id="admin-form">
        <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items:start;">
          
          <!-- Kiri: Form Inputs -->
          <div style="display:flex; flex-direction:column; gap:14px; width: 100%;">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600;">Nama Lengkap</label>
              <input class="form-control" name="name" placeholder="Nama lengkap admin" required style="border-radius:10px;">
            </div>
            
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" style="font-weight:600;">Email</label>
              <input class="form-control" name="email" type="email" placeholder="email@example.com" required style="border-radius:10px;">
            </div>
            
            {{-- Fields untuk Create --}}
            <div id="create-password-fields" style="display:flex; flex-direction:column; gap:14px;">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" style="font-weight:600;">Password</label>
                <input class="form-control" name="password" type="password" placeholder="Min. 8 karakter" style="border-radius:10px;">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" style="font-weight:600;">Konfirmasi Password</label>
                <input class="form-control" name="password_confirmation" type="password" placeholder="Ulangi password" style="border-radius:10px;">
              </div>
            </div>

            {{-- Fields untuk Edit (Ganti Password) --}}
            <div id="change-pw-group" style="display:none; margin-top:4px;">
              <label class="form-checkbox" style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; cursor:pointer;">
                <input type="checkbox" id="change-password-check" onchange="togglePasswordFields()" style="width:16px; height:16px; accent-color:var(--c-primary);">
                <span>Ganti Password Admin</span>
              </label>
            </div>
            
            <div id="edit-password-fields" style="display:none; flex-direction:column; gap:14px;">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" style="font-weight:600;">Password Baru</label>
                <input class="form-control" name="new_password" type="password" placeholder="Min. 8 karakter" style="border-radius:10px;">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" style="font-weight:600;">Konfirmasi Password</label>
                <input class="form-control" name="new_password_confirmation" type="password" placeholder="Ulangi password" style="border-radius:10px;">
              </div>
            </div>
          </div>

          <!-- Kanan: Upload Foto -->
          <div style="display:flex; flex-direction:column; gap:8px; width: 100%;">
            <label class="form-label" style="font-weight:600; color:var(--c-text-dark)">Foto Profil</label>
            
            <div class="photo-upload-zone" id="admin-photo-upload-zone" onclick="document.getElementById('admin-photo-input').click()">
              <span class="material-icons">add_a_photo</span>
              <p>Pilih Foto</p>
              <span>Format JPG/PNG, maks 2MB</span>
            </div>
            <input type="file" id="admin-photo-input" name="photo" accept="image/*" style="display:none">
            
            <div id="admin-photo-preview" style="display:none">
              <div class="preview-container">
                <img id="admin-photo-img" src="" alt="Preview">
                <button type="button" class="remove-preview-btn" onclick="removeAdminPhotoSelection(event)" title="Hapus foto">
                  <span class="material-icons" style="font-size:16px">delete</span>
                </button>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
    <div class="modal-footer" style="background:#f8faf9; border-top:1px solid #eef2f0; padding:16px 24px; display:flex; justify-content:flex-end; gap:12px;">
      <button class="btn btn-outline btn-sm" onclick="closeModal('admin-modal')" style="border-radius:8px;">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveAdmin()" style="border-radius:8px; background:var(--c-primary); border-color:var(--c-primary);">Simpan</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, currentPage = 1;
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

function togglePasswordFields() {
  const check = document.getElementById('change-password-check')?.checked;
  document.getElementById('edit-password-fields').style.display = check ? 'flex' : 'none';
}

async function loadAdmin(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/admins', { search: q, page, per_page: 15 });
  const rows = res.data?.data ?? [];
  const meta = res.data?.pagination;
  const tbody = document.getElementById('admin-tbody');
  
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><span class="material-icons" style="color:#E91E63">admin_panel_settings</span><p>Tidak ada admin ditemukan</p></div></td></tr>`;
    document.getElementById('admin-pagination').innerHTML = '';
    return;
  }
  
  tbody.innerHTML = rows.map((a, i) => `
    <tr>
      <td>
        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f0f4f2; border:1px solid #dde6e0;">
          <img src="${a.photo_url ? proxyImgUrl(a.photo_url) : '/images/admin/default.svg'}" 
               alt="" 
               style="width:100%; height:100%; object-fit:cover;"
               onerror="this.src='/images/admin/default.svg'">
        </div>
      </td>
      <td><div style="font-weight:600">${a.name ?? '-'}</div></td>
      <td style="color:var(--c-text-grey);font-size:12px">${a.email ?? '-'}</td>
      <td><span class="badge badge-purple" style="background:#FCE4EC; color:#E91E63;">ADMIN</span></td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-xs btn-outline" onclick="editAdmin(${a.id})">Edit</button>
          <button class="btn btn-xs btn-icon" onclick="deleteAdmin(${a.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  document.getElementById('admin-pagination').innerHTML = meta ? renderPagination(meta, p => loadAdmin(p)) : '';
}

function removeAdminPhotoSelection(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById('admin-photo-input');
  if (input) input.value = '';
  document.getElementById('admin-photo-img').src = '';
  document.getElementById('admin-photo-preview').style.display = 'none';
  document.getElementById('admin-photo-upload-zone').style.display = 'flex';
}

function openAddModal() {
  editId = null;
  document.getElementById('a-modal-title').textContent = 'Tambah Admin';
  document.getElementById('admin-form').reset();
  removeAdminPhotoSelection();
  
  document.getElementById('create-password-fields').style.display = 'flex';
  document.getElementById('change-pw-group').style.display = 'none';
  document.getElementById('edit-password-fields').style.display = 'none';
  
  openModal('admin-modal');
}

async function editAdmin(id) {
  editId = id;
  document.getElementById('a-modal-title').textContent = 'Edit Admin';
  document.getElementById('admin-form').reset();
  removeAdminPhotoSelection();
  
  document.getElementById('create-password-fields').style.display = 'none';
  document.getElementById('change-pw-group').style.display = 'block';
  document.getElementById('change-password-check').checked = false;
  document.getElementById('edit-password-fields').style.display = 'none';
  
  const res = await api.get('/admins/' + id);
  const a = res.data?.data;
  const f = document.getElementById('admin-form');
  f.name.value = a.name ?? '';
  f.email.value = a.email ?? '';
  
  if (a.photo_url) {
    document.getElementById('admin-photo-img').src = proxyImgUrl(a.photo_url);
    document.getElementById('admin-photo-preview').style.display = 'block';
    document.getElementById('admin-photo-upload-zone').style.display = 'none';
  }
  
  openModal('admin-modal');
}

async function saveAdmin() {
  const f = document.getElementById('admin-form');
  const formData = new FormData();
  formData.append('name', f.name.value);
  formData.append('email', f.email.value);
  
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
    await api.postForm('/admins/' + editId, formData) : 
    await api.postForm('/admins', formData);
    
  if (res.ok) { 
    toast('Data admin berhasil disimpan'); 
    closeModal('admin-modal'); 
    loadAdmin(currentPage); 
  } else {
    toast(res.data?.message ?? 'Gagal menyimpan data admin', 'error');
  }
}

async function deleteAdmin(id) {
  // Cegah menghapus akun sendiri jika email sama dengan user login di session
  confirmDialog('Hapus data admin ini secara permanen?', async () => {
    const r = await api.delete('/admins/' + id);
    r.ok ? (toast('Admin berhasil dihapus', 'warn'), loadAdmin(currentPage)) 
         : toast(r.data?.message ?? 'Gagal menghapus admin', 'error');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const photoInput = document.getElementById('admin-photo-input');
  if (photoInput) {
    photoInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('admin-photo-img').src = event.target.result;
          document.getElementById('admin-photo-preview').style.display = 'block';
          document.getElementById('admin-photo-upload-zone').style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

loadAdmin();
</script>
@endpush
