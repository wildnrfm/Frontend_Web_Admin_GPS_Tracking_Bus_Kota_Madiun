@extends('admin.layouts.app')
@section('title','Edit Profil Admin')
@section('page-title','Edit Profil')
@section('content')

@php $user = session('admin_user'); @endphp

<style>
  .edit-container {
    max-width: 600px;
    margin: 0 auto;
  }

  .edit-header {
    margin-bottom: 32px;
  }

  .edit-title {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
  }

  .edit-subtitle {
    font-size: 14px;
    color: #6b7280;
  }

  .form-section {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
  }

  .section-title {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group:last-child {
    margin-bottom: 0;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 8px;
  }

  .form-label.required::after {
    content: "*";
    color: #dc2626;
    margin-left: 4px;
  }

  .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
  }

  .form-control:focus {
    outline: none;
    border-color: #2d5016;
    box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
  }

  .form-control::placeholder {
    color: #9ca3af;
  }

  textarea.form-control {
    resize: vertical;
    min-height: 100px;
  }

  .form-hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
  }

  .button-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
  }

  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex: 1;
  }

  .btn-primary {
    background: #2d5016;
    color: white;
  }

  .btn-primary:hover {
    background: #1f3810;
  }

  .btn-cancel {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
  }

  .btn-cancel:hover {
    background: #e5e7eb;
  }

  .alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
  }

  .alert-success {
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #15803d;
  }

  .alert-error {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #dc2626;
  }

  .divider {
    height: 1px;
    background: #e5e7eb;
    margin: 24px 0;
  }

  .password-section {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #78350f;
  }

  .material-icons {
    font-size: 20px;
  }

  .avatar-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
  }

  .avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    color: #9ca3af;
    overflow: hidden;
    object-fit: cover;
  }

  .avatar-preview.has-image {
    border: 2px solid #2d5016;
    background: transparent;
  }

  .avatar-input-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
  }

  .avatar-input-wrapper input[type="file"] {
    display: none;
  }

  .avatar-button {
    padding: 10px 16px;
    background: #2d5016;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .avatar-button:hover {
    background: #1f3810;
  }

  .avatar-delete-btn {
    padding: 10px 16px;
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .avatar-delete-btn:hover {
    background: #fecaca;
  }
</style>

<div class="edit-container">
  {{-- Header --}}
  <div class="edit-header">
    <div class="edit-title">Edit Profil</div>
    <div class="edit-subtitle">Perbarui informasi profil Anda</div>
  </div>

  {{-- Data Pribadi --}}
  <div class="form-section">
    <div class="section-title">Informasi Dasar</div>

    <div class="form-group">
      <label class="form-label required">Nama Lengkap</label>
      <input class="form-control" id="pf-name" type="text" value="{{ $user['name'] ?? '' }}" placeholder="Masukkan nama lengkap">
    </div>

    <div class="form-group">
      <label class="form-label required">Email</label>
      <input class="form-control" id="pf-email" type="email" value="{{ $user['email'] ?? '' }}" placeholder="Masukkan email" readonly>
      <div class="form-hint">Email tidak dapat diubah</div>
    </div>

    <div class="form-group">
      <label class="form-label">Nomor HP</label>
      <input class="form-control" id="pf-nohp" type="tel" value="{{ $user['no_hp'] ?? '' }}" placeholder="Contoh: 081234567890">
    </div>

    <div class="form-group">
      <label class="form-label">Alamat</label>
      <textarea class="form-control" id="pf-alamat" placeholder="Masukkan alamat lengkap">{{ $user['alamat'] ?? '' }}</textarea>
    </div>
  </div>

  {{-- Avatar Section --}}
  <div class="form-section">
    <div class="section-title">Avatar</div>
    
    <div class="avatar-section">
      @if($user['photo'] ?? null)
        <img id="avatar-preview-img" src="{{ url('storage/' . $user['photo']) }}" alt="Avatar" class="avatar-preview has-image">
      @else
        <div id="avatar-preview-img" class="avatar-preview">{{ strtoupper(substr($user['name'] ?? 'A', 0, 1)) }}</div>
      @endif
      
      <div class="avatar-input-wrapper">
        <input type="file" id="edit-avatar-input" accept="image/jpeg,image/png,image/jpg">
        <button type="button" class="avatar-button" onclick="document.getElementById('edit-avatar-input').click()">
          <span class="material-icons">image</span>
          Pilih Foto
        </button>
        
        @if($user['photo'] ?? null)
        <button type="button" class="avatar-delete-btn" onclick="deleteEditAvatar()">
          <span class="material-icons">delete</span>
          Hapus Avatar
        </button>
        @endif
      </div>
      
      <div class="form-hint" style="text-align: center;">
        Format: JPEG, PNG, JPG | Ukuran maksimal 2MB
      </div>
    </div>
  </div>

  {{-- Ganti Password --}}
  <div class="form-section">
    <div class="section-title">Keamanan</div>

    <div class="password-section">
      <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 6px;">info</span>
      Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol
    </div>

    <div class="form-group">
      <label class="form-label required">Password Saat Ini</label>
      <input class="form-control" id="pw-current" type="password" placeholder="Masukkan password saat ini">
    </div>

    <div class="form-group">
      <label class="form-label required">Password Baru</label>
      <input class="form-control" id="pw-new" type="password" placeholder="Minimal 8 karakter">
      <div class="form-hint">Minimal 8 karakter dengan kombinasi huruf, angka, dan simbol</div>
    </div>

    <div class="form-group">
      <label class="form-label required">Konfirmasi Password Baru</label>
      <input class="form-control" id="pw-confirm" type="password" placeholder="Ulangi password baru">
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="button-group">
    <a href="{{ route('admin.profil') }}" class="btn btn-cancel">
      <span class="material-icons">arrow_back</span>
      Kembali
    </a>
    <button type="button" class="btn btn-primary" onclick="saveProfile()">
      <span class="material-icons">save</span>
      Simpan Perubahan
    </button>
  </div>
</div>

@endsection
@push('scripts')
<script>
async function saveProfile() {
  const name   = document.getElementById('pf-name').value?.trim();
  const nohp   = document.getElementById('pf-nohp').value?.trim();
  const alamat = document.getElementById('pf-alamat').value?.trim();
  const pwCurrent = document.getElementById('pw-current').value;
  const pwNew = document.getElementById('pw-new').value;
  const pwConfirm = document.getElementById('pw-confirm').value;

  // Validasi nama
  if (!name) {
    toast('Nama lengkap harus diisi', 'warn');
    return;
  }

  // Jika ingin ganti password, semua field password harus diisi
  if (pwNew || pwConfirm) {
    if (!pwCurrent || !pwNew || !pwConfirm) {
      toast('Semua field password harus diisi jika ingin mengubah password', 'warn');
      return;
    }
    if (pwNew.length < 8) {
      toast('Password baru minimal 8 karakter', 'warn');
      return;
    }
    if (pwNew !== pwConfirm) {
      toast('Konfirmasi password tidak cocok', 'warn');
      return;
    }
  }

  const body = {
    name:   name,
    no_hp:  nohp || null,
    alamat: alamat || null,
  };

  // Jika ingin ganti password, tambahkan ke body
  if (pwNew) {
    body.current_password = pwCurrent;
    body.new_password = pwNew;
    body.new_password_confirmation = pwConfirm;
  }

  // Update profil
  const res = await api.put('/auth/profile', body);
  if (!res.ok) {
    toast(res.data?.message ?? 'Gagal mengubah profil', 'error');
    return;
  }

  toast('Profil berhasil diperbarui');

  // Jika password diubah, lakukan password change juga
  if (pwNew) {
    const pwRes = await api.post('/auth/change-password', {
      current_password: pwCurrent,
      new_password: pwNew,
      new_password_confirmation: pwConfirm,
    });

    if (pwRes.ok) {
      toast('Password berhasil diubah');
    } else {
      toast(pwRes.data?.message ?? 'Gagal mengubah password', 'warn');
    }
  }

  // Redirect ke profil setelah sukses
  setTimeout(() => {
    window.location.href = "{{ route('admin.profil') }}";
  }, 1500);
}

let selectedEditAvatarFile = null;

document.getElementById('edit-avatar-input').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  
  // Validasi file
  const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
  if (!validTypes.includes(file.type)) {
    toast('Format foto harus jpeg, jpg, atau png', 'error');
    return;
  }
  
  if (file.size > 2 * 1024 * 1024) {
    toast('Ukuran foto maksimal 2MB', 'error');
    return;
  }
  
  selectedEditAvatarFile = file;
  
  // Tampilkan preview
  const reader = new FileReader();
  reader.onload = function(event) {
    const preview = document.getElementById('avatar-preview-img');
    
    // Buat img element jika sebelumnya div
    if (preview.tagName === 'DIV') {
      const img = document.createElement('img');
      img.id = 'avatar-preview-img';
      img.alt = 'Avatar';
      img.className = 'avatar-preview has-image';
      preview.parentNode.replaceChild(img, preview);
    }
    
    const newPreview = document.getElementById('avatar-preview-img');
    newPreview.src = event.target.result;
    newPreview.classList.add('has-image');
  };
  reader.readAsDataURL(file);
});

async function deleteEditAvatar() {
  if (!confirm('Yakin ingin menghapus avatar?')) return;
  
  try {
    const response = await api.delete('/auth/profile/photo');
    
    if (!response.ok) {
      toast(response.data?.message ?? 'Gagal menghapus foto', 'error');
      return;
    }
    
    toast('Avatar berhasil dihapus');
    
    // Reload page untuk memperbarui tampilan
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Delete error:', error);
    toast('Terjadi kesalahan saat menghapus foto', 'error');
  }
}

// Intercept saveProfile to upload avatar if selected
const originalSaveProfile = saveProfile;
saveProfile = async function() {
  // Upload avatar jika ada file baru
  if (selectedEditAvatarFile) {
    const formData = new FormData();
    formData.append('photo', selectedEditAvatarFile);
    
    try {
      const response = await api.postForm('/auth/profile/photo', formData);
      
      if (!response.ok) {
        toast(response.data?.message ?? 'Gagal mengupload foto', 'error');
        return;
      }
      
      toast('Avatar berhasil diperbarui');
      selectedEditAvatarFile = null;
    } catch (error) {
      console.error('Upload error:', error);
      toast('Terjadi kesalahan saat mengupload foto', 'error');
      return;
    }
  }
  
  // Lanjut dengan save profile data
  const name   = document.getElementById('pf-name').value?.trim();
  const nohp   = document.getElementById('pf-nohp').value?.trim();
  const alamat = document.getElementById('pf-alamat').value?.trim();
  const pwCurrent = document.getElementById('pw-current').value;
  const pwNew = document.getElementById('pw-new').value;
  const pwConfirm = document.getElementById('pw-confirm').value;

  // Validasi nama
  if (!name) {
    toast('Nama lengkap harus diisi', 'warn');
    return;
  }

  // Jika ingin ganti password, semua field password harus diisi
  if (pwNew || pwConfirm) {
    if (!pwCurrent || !pwNew || !pwConfirm) {
      toast('Semua field password harus diisi jika ingin mengubah password', 'warn');
      return;
    }
    if (pwNew.length < 8) {
      toast('Password baru minimal 8 karakter', 'warn');
      return;
    }
    if (pwNew !== pwConfirm) {
      toast('Konfirmasi password tidak cocok', 'warn');
      return;
    }
  }

  const body = {
    name:   name,
    no_hp:  nohp || null,
    alamat: alamat || null,
  };

  // Jika ingin ganti password, tambahkan ke body
  if (pwNew) {
    body.current_password = pwCurrent;
    body.new_password = pwNew;
    body.new_password_confirmation = pwConfirm;
  }

  // Update profil
  const res = await api.put('/auth/profile', body);
  if (!res.ok) {
    toast(res.data?.message ?? 'Gagal mengubah profil', 'error');
    return;
  }

  toast('Profil berhasil diperbarui');

  // Jika password diubah, lakukan password change juga
  if (pwNew) {
    const pwRes = await api.post('/auth/change-password', {
      current_password: pwCurrent,
      new_password: pwNew,
      new_password_confirmation: pwConfirm,
    });

    if (pwRes.ok) {
      toast('Password berhasil diubah');
    } else {
      toast(pwRes.data?.message ?? 'Gagal mengubah password', 'warn');
    }
  }

  // Redirect ke profil setelah sukses
  setTimeout(() => {
    window.location.href = "{{ route('admin.profil') }}";
  }, 1500);
};
</script>
@endpush
