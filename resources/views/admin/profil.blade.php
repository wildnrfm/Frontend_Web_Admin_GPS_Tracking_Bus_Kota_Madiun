@extends('admin.layouts.app')
@section('title','Profil Admin')
@section('page-title','Profil')
@section('content')

@php $user = session('admin_user'); @endphp

<style>
  .profile-container {
    max-width: 600px;
    margin: 0 auto;
  }

  .profile-header {
    background: linear-gradient(135deg, #2d5016 0%, #3a6b1f 100%);
    border-radius: 16px;
    padding: 40px 32px;
    text-align: center;
    color: white;
    margin-bottom: 32px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    font-weight: bold;
    margin: 0 auto 16px;
    border: 3px solid rgba(255,255,255,0.3);
    object-fit: cover;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255,255,255,0.3);
  }

  .profile-avatar.avatar-div {
    display: flex;
  }

  .avatar-upload-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
  }

  .profile-avatar:hover .avatar-upload-overlay {
    opacity: 1;
  }

  .avatar-upload-icon {
    color: white;
    font-size: 28px;
  }

  .profile-name {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .profile-role {
    font-size: 13px;
    opacity: 0.9;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
  }

  .profile-status {
    display: block;
    font-size: 12px;
    margin-top: 12px;
    opacity: 0.9;
  }

  .profile-status::before {
    content: "●";
    color: #4ade80;
    margin-right: 6px;
  }

  .section-title {
    font-size: 16px;
    font-weight: 600;
    margin: 32px 0 16px;
    color: #1f2937;
  }

  .stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }

  .stat-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px 16px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    border-color: #2d5016;
    background: #fafaf9;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #2d5016;
    margin-bottom: 4px;
  }

  .stat-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .data-pribadi {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
  }

  .data-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 12px;
  }

  .data-item:last-child {
    margin-bottom: 0;
  }

  .data-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0284c7;
    flex-shrink: 0;
  }

  .data-content {
    flex: 1;
  }

  .data-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .data-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
    word-break: break-word;
  }

  .menu-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
  }

  .menu-item:hover {
    border-color: #2d5016;
    background: #fafaf9;
  }

  .menu-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .menu-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0284c7;
    font-size: 20px;
  }

  .menu-text {
    display: flex;
    flex-direction: column;
  }

  .menu-label {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 2px;
  }

  .menu-desc {
    font-size: 12px;
    color: #6b7280;
  }

  .menu-arrow {
    color: #d1d5db;
    font-size: 20px;
  }

  .logout-btn {
    padding: 12px 16px;
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    font-size: 14px;
  }

  .logout-btn:hover {
    background: #fecaca;
    border-color: #f87171;
  }

  .material-icons {
    font-size: 20px;
  }

  /* Mobile Responsive */
  @media (max-width: 480px) {
    .profile-container {
      margin: 0;
    }

    .profile-header {
      border-radius: 0;
      margin-bottom: 24px;
      padding: 32px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      font-size: 48px;
      margin-bottom: 16px;
    }

    .profile-name {
      font-size: 28px;
      margin-bottom: 12px;
    }

    .profile-role {
      font-size: 14px;
      padding: 8px 16px;
      margin-bottom: 12px;
    }

    .profile-status {
      display: block;
      font-size: 12px;
      margin: 0;
    }

    .section-title {
      font-size: 15px;
      margin: 24px 0 14px;
      padding: 0 20px;
    }

    .stat-grid {
      padding: 0 20px;
      gap: 12px;
    }

    .stat-card {
      padding: 18px 12px;
    }

    .stat-value {
      font-size: 24px;
    }

    .stat-label {
      font-size: 11px;
    }

    .data-pribadi {
      margin: 0 20px 16px;
      padding: 16px;
    }

    .data-item {
      margin-bottom: 14px;
      gap: 10px;
    }

    .data-icon {
      width: 36px;
      height: 36px;
      font-size: 18px;
    }

    .data-label {
      font-size: 10px;
    }

    .data-value {
      font-size: 13px;
    }

    .menu-section {
      padding: 0 20px 20px;
    }

    .menu-item {
      padding: 14px;
      font-size: 13px;
    }

    .menu-icon {
      width: 36px;
      height: 36px;
      font-size: 18px;
    }

    .menu-label {
      font-size: 13px;
    }

    .menu-desc {
      font-size: 11px;
    }

    .logout-btn {
      padding: 12px 16px;
      font-size: 14px;
    }
  }

</style>

<div class="profile-container">
  {{-- Profile Header --}}
  <div class="profile-header">
    @if($user['photo'] ?? null)
      <img src="{{ url('storage/' . $user['photo']) }}" alt="Avatar" class="profile-avatar" onclick="openAvatarUploadModal()" title="Klik untuk mengubah avatar">
    @else
      <div class="profile-avatar" onclick="openAvatarUploadModal()" title="Klik untuk mengubah avatar">
        {{ strtoupper(substr($user['name'] ?? 'A', 0, 1)) }}
        <div class="avatar-upload-overlay">
          <span class="material-icons avatar-upload-icon">camera_alt</span>
        </div>
      </div>
    @endif
    <div class="profile-name">{{ $user['name'] ?? 'Administrator' }}</div>
    <div class="profile-role">Administrator</div>
    <div class="profile-status">Akun aktif</div>
  </div>

  {{-- Ringkasan Sistem --}}
  <div class="section-title">Ringkasan Sistem</div>
  <div class="stat-grid" id="sys-stats">
    <div class="stat-card">
      <div class="stat-value" id="sys-bus">—</div>
      <div class="stat-label">Bus</div>
    </div>
    <div class="stat-card">
      <div class="stat-value" id="sys-driver">—</div>
      <div class="stat-label">Driver</div>
    </div>
    <div class="stat-card">
      <div class="stat-value" id="sys-siswa">—</div>
      <div class="stat-label">Sistem</div>
    </div>
    <div class="stat-card">
      <div class="stat-value" id="sys-pending">—</div>
      <div class="stat-label">Pending</div>
    </div>
  </div>

  {{-- Data Pribadi --}}
  <div class="section-title">Data Pribadi</div>
  <div class="data-pribadi">
    <div class="data-item">
      <div class="data-icon">
        <span class="material-icons">email</span>
      </div>
      <div class="data-content">
        <div class="data-label">Email</div>
        <div class="data-value">{{ $user['email'] ?? '-' }}</div>
      </div>
    </div>

    <div class="data-item">
      <div class="data-icon">
        <span class="material-icons">phone</span>
      </div>
      <div class="data-content">
        <div class="data-label">No. HP</div>
        <div class="data-value">{{ $user['no_hp'] ?? '-' }}</div>
      </div>
    </div>

    <div class="data-item">
      <div class="data-icon">
        <span class="material-icons">location_on</span>
      </div>
      <div class="data-content">
        <div class="data-label">Alamat</div>
        <div class="data-value">{{ $user['alamat'] ?? '-' }}</div>
      </div>
    </div>
  </div>

  {{-- Menu Akun --}}
  <div class="section-title">Akun</div>
  <div class="menu-section">
    <a href="{{ route('admin.profil.edit') }}" class="menu-item">
      <div class="menu-left">
        <div class="menu-icon">
          <span class="material-icons">edit</span>
        </div>
        <div class="menu-text">
          <div class="menu-label">Edit Profil</div>
          <div class="menu-desc">Ubah nama, nomor HP, dan alamat</div>
        </div>
      </div>
      <div class="menu-arrow">›</div>
    </a>

    <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
      @csrf
      <button type="button" class="logout-btn" onclick="confirmLogout()">
        <span class="material-icons">logout</span>
        Keluar dari Akun
      </button>
    </form>
  </div>
</div>

{{-- Avatar Upload Modal --}}
<div id="avatarUploadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
    <div style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1f2937;">Ubah Avatar</div>
    
    <div style="margin-bottom: 16px;">
      <div id="avatarPreview" style="width: 120px; height: 120px; border-radius: 50%; background: #f3f4f6; border: 2px dashed #d1d5db; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 48px; font-weight: bold; color: #9ca3af; overflow: hidden; object-fit: cover;">
        {{ strtoupper(substr($user['name'] ?? 'A', 0, 1)) }}
      </div>
      
      <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/jpg" style="display: none;">
      <button type="button" onclick="document.getElementById('avatarInput').click()" style="width: 100%; padding: 10px; background: #2d5016; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
        <span class="material-icons" style="vertical-align: middle; margin-right: 6px; font-size: 20px;">image</span>
        Pilih Foto
      </button>
      
      @if($user['photo'] ?? null)
      <button type="button" onclick="deleteAvatar()" style="width: 100%; padding: 10px; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.3s; margin-top: 8px;">
        <span class="material-icons" style="vertical-align: middle; margin-right: 6px; font-size: 20px;">delete</span>
        Hapus Avatar
      </button>
      @endif
    </div>

    <div style="display: flex; gap: 8px;">
      <button type="button" onclick="closeAvatarUploadModal()" style="flex: 1; padding: 10px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
        Batal
      </button>
      <button type="button" onclick="uploadAvatar()" style="flex: 1; padding: 10px; background: #2d5016; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
        Simpan
      </button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<style>
  #avatarUploadModal[style*="flex"] {
    display: flex !important;
  }
</style>
<script>
let selectedAvatarFile = null;

function openAvatarUploadModal() {
  document.getElementById('avatarUploadModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeAvatarUploadModal() {
  document.getElementById('avatarUploadModal').style.display = 'none';
  document.body.style.overflow = 'auto';
  selectedAvatarFile = null;
}

// Close modal when clicking outside
document.getElementById('avatarUploadModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeAvatarUploadModal();
  }
});

document.getElementById('avatarInput').addEventListener('change', function(e) {
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
  
  selectedAvatarFile = file;
  
  // Tampilkan preview
  const reader = new FileReader();
  reader.onload = function(event) {
    const preview = document.getElementById('avatarPreview');
    preview.style.backgroundImage = `url(${event.target.result})`;
    preview.style.backgroundSize = 'cover';
    preview.style.backgroundPosition = 'center';
    preview.textContent = '';
  };
  reader.readAsDataURL(file);
});

async function uploadAvatar() {
  if (!selectedAvatarFile) {
    toast('Pilih foto terlebih dahulu', 'warn');
    return;
  }
  
  const formData = new FormData();
  formData.append('photo', selectedAvatarFile);
  
  try {
    const response = await api.postForm('/auth/profile/photo', formData);
    
    if (!response.ok) {
      toast(response.data?.message ?? 'Gagal mengupload foto', 'error');
      return;
    }
    
    toast('Avatar berhasil diperbarui');
    selectedAvatarFile = null;
    closeAvatarUploadModal();
    
    // Reload page untuk menampilkan avatar baru
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Upload error:', error);
    toast('Terjadi kesalahan saat mengupload foto', 'error');
  }
}

async function deleteAvatar() {
  if (!confirm('Yakin ingin menghapus avatar?')) return;
  
  try {
    const response = await api.delete('/auth/profile/photo');
    
    if (!response.ok) {
      toast(response.data?.message ?? 'Gagal menghapus foto', 'error');
      return;
    }
    
    toast('Avatar berhasil dihapus');
    closeAvatarUploadModal();
    
    // Reload page
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Delete error:', error);
    toast('Terjadi kesalahan saat menghapus foto', 'error');
  }
}

async function loadSysStats() {
  try {
    const [busR, stuR, drvR, pendR] = await Promise.all([
      api.get('/buses'),
      api.get('/students', { per_page: 1 }),
      api.get('/drivers'),
      api.get('/students/pending'),
    ]);
    document.getElementById('sys-bus').textContent     = busR.data?.data?.length ?? 0;
    document.getElementById('sys-driver').textContent  = drvR.data?.data?.length ?? 0;
    document.getElementById('sys-siswa').textContent   = stuR.data?.meta?.total ?? (stuR.data?.data?.length ?? 0);
    document.getElementById('sys-pending').textContent = pendR.data?.meta?.total ?? (pendR.data?.data?.length ?? 0);
  } catch (e) {
    console.error('Error loading stats:', e);
  }
}

function confirmLogout() {
  confirmDialog('Kamu yakin ingin keluar dari akun ini?', () => document.getElementById('logout-form').submit());
}

loadSysStats();
</script>
@endpush
