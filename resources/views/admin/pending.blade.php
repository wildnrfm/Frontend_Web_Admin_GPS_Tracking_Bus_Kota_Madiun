@extends('admin.layouts.app')
@section('title','Persetujuan Akun')
@section('page-title','Persetujuan Akun')
@section('content')

<style>
/* ╔══════════════════════════════════════════════════════════════════╗ */
/* ║              PENDING PAGE REDESIGN STYLES                      ║ */
/* ╚══════════════════════════════════════════════════════════════════╝ */
.pending-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 28px;
  color: #fff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.24);
  margin-bottom: 24px;
}
.pending-hero::before {
  content: '';
  position: absolute;
  top: -80px; right: -50px;
  width: 240px; height: 240px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
}
.pending-hero::after {
  content: '';
  position: absolute;
  bottom: -50px; left: -40px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
}
.pending-hero-top {
  display: flex; align-items: center; gap: 16px;
}
.pending-hero-icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  background: rgba(255,255,255,0.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
}
.pending-hero-text h2 {
  margin: 0; font-size: 24px; font-weight: 700;
}
.pending-hero-text p {
  margin: 4px 0 0; font-size: 13px; color: rgba(255,255,255,.85);
}
.pending-filter-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
  margin-bottom: 20px;
}
.pending-filter-bar .search-box {
  position: relative;
  flex: 1;
  min-width: 220px;
}
.pending-filter-bar .search-box input {
  width: 100%;
  padding: 12px 40px 12px 14px;
  border: 1px solid var(--c-border);
  border-radius: 12px;
  font-size: 14px;
  color: var(--c-text-dark);
  background: #fff;
}
.pending-filter-bar .search-box .material-icons {
  position: absolute;
  top: 50%;
  right: 14px;
  transform: translateY(-50%);
  color: var(--c-text-grey);
}
.pending-filter-bar .chip {
  border: 1px solid rgba(0,0,0,0.08);
  padding: 10px 16px;
  border-radius: 999px;
  background: #fff;
  color: var(--c-text-dark);
  cursor: pointer;
  transition: all 200ms ease;
  font-size: 13px;
  font-weight: 600;
}
.pending-filter-bar .chip:hover {
  background: rgba(15,61,34,0.06);
}
.pending-filter-bar .chip.active {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 100%);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 6px 18px rgba(15, 61, 34, 0.18);
}
.pending-table {
  width: 100%;
  border-collapse: collapse;
}
.pending-table th,
.pending-table td {
  padding: 14px 12px;
  text-align: left;
  vertical-align: middle;
  border-bottom: 1px solid var(--c-border);
  font-size: 13px;
  color: var(--c-text-dark);
}
.pending-table th {
  background: #F7F9FA;
  color: var(--c-text-grey);
  font-weight: 700;
}
.pending-table td {
  background: #fff;
}
.pending-table tbody tr:hover td {
  background: #F8FBFC;
}
.table-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.status-badge.badge-orange { background: rgba(245, 166, 35, 0.14); color: #B26C00; }
.status-badge.badge-green { background: rgba(46, 125, 82, 0.12); color: #1B5E37; }
.status-badge.badge-red   { background: rgba(244, 67, 54, 0.12); color: #B00020; }
.status-badge.badge-grey  { background: rgba(120, 123, 128, 0.12); color: #424242; }

.hidden-column { display: none; }

#reject-modal .modal {
  width: 100%;
  max-width: 520px;
  border-radius: 16px;
}
#reject-modal .modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #F1F3F5;
}
#reject-modal .modal-body {
  padding: 24px;
}
#reject-modal .modal-body label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 13px;
  color: #333;
}
#reject-modal .modal-body textarea {
  width: 100%;
  min-height: 130px;
  border: 1px solid var(--c-border);
  border-radius: 12px;
  padding: 14px 16px;
  resize: vertical;
  font-size: 14px;
}
#reject-modal .modal-footer {
  padding: 16px 24px;
  justify-content: space-between;
}

/* ╔══════════════════════════════════════════════════════════════════════╗ */
/* ║         REJECTION HISTORY MODAL STYLES - SCOPED (SAFE)        ║ */
/* ╚══════════════════════════════════════════════════════════════════════╝ */
#rejectionHistoryModal.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.48);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(2px);
  animation: fadeIn 200ms ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

#rejectionHistoryModal .modal-content {
  background: #fff;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 75vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  animation: slideUp 280ms ease-out;
}

@keyframes slideUp {
  from { transform: translateY(24px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

#rejectionHistoryModal .modal-header {
  padding: 24px;
  border-bottom: 1px solid #EFEFEF;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

#rejectionHistoryModal .modal-header-title {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0;
}

#rejectionHistoryModal .modal-header-title .icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #FDECEA;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #D32F2F;
  font-size: 20px;
}

#rejectionHistoryModal .modal-header-title h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #212121;
}

#rejectionHistoryModal .modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: background 200ms ease;
}

#rejectionHistoryModal .modal-close:hover {
  background: #F5F5F5;
}

#rejectionHistoryModal .modal-body {
  padding: 24px;
  flex: 1;
  overflow-y: auto;
}

.rejection-item {
  padding: 16px;
  border: 1px solid #EFEFEF;
  border-radius: 12px;
  margin-bottom: 12px;
  transition: all 200ms ease;
}

.rejection-item:last-child {
  margin-bottom: 0;
}

.rejection-item:hover {
  background: #FAFAFA;
  border-color: #E0E0E0;
}

.rejection-item-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 8px;
}

.rejection-item-date {
  font-size: 12px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.rejection-item-by {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: #999;
}

.rejection-item-by .icon {
  width: 16px;
  height: 16px;
  background: #FDECEA;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: #D32F2F;
}

.rejection-item-reason {
  font-size: 13px;
  line-height: 1.6;
  color: #424242;
  background: #FFFBF9;
  padding: 12px;
  border-left: 3px solid #FF6B6B;
  border-radius: 4px;
  margin-top: 8px;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #EFEFEF;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.modal-footer .btn {
  padding: 8px 16px;
  font-size: 13px;
}

.empty-rejection-state {
  text-align: center;
  padding: 48px 24px;
  color: #999;
}

.empty-rejection-state .icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #F5F5F5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin: 0 auto 16px;
}

.empty-rejection-state p {
  margin: 0;
  font-size: 14px;
}
</style>

<div class="pending-hero">
  <div class="pending-hero-top">
    <div class="pending-hero-icon"><span class="material-icons">pending_actions</span></div>
    <div class="pending-hero-text">
      <h2>Persetujuan Akun</h2>
      <p>Review dan kelola permintaan akun siswa secara cepat dan konsisten.</p>
    </div>
  </div>
</div>

<div class="pending-filter-bar">
  <div class="search-box">
    <input id="pending-search" type="text" placeholder="Cari nama, email, NIS, sekolah..." oninput="onSearchInput()">
    <span class="material-icons">search</span>
  </div>
  <button class="btn btn-icon" onclick="loadPending()" title="Refresh"><span class="material-icons">refresh</span></button>
  <div style="flex:1"></div>
  <button class="chip active" id="chip-pending" onclick="setFilter('pending')">Pending</button>
  <button class="chip" id="chip-approved" onclick="setFilter('approved')">Disetujui</button>
  <button class="chip" id="chip-rejected" onclick="setFilter('rejected')">Ditolak</button>
  <button class="chip" id="chip-suspended" onclick="setFilter('suspended')">Suspend</button>
</div>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table class="pending-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th class="col-email">Email</th>
          <th>NIS</th>
          <th>Sekolah</th>
          <th class="col-alamat">Alamat</th>
          <th>No HP</th>
          <th>Status</th>
          <th id="pending-date-header">Daftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="pending-tbody">
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="reject-modal" data-no-close="true" style="display:none;">
  <div class="modal">
    <div class="modal-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:12px;background:#FDECEA;display:flex;align-items:center;justify-content:center;color:#D32F2F;">
          <span class="material-icons">close</span>
        </div>
        <div>
          <h3 style="margin:0;font-size:16px;">Tolak Akun Siswa</h3>
          <p style="margin:4px 0 0;font-size:13px;color:#666;">Masukkan alasan penolakan untuk catatan internal dan notifikasi siswa.</p>
        </div>
      </div>
      <button class="modal-close" type="button" onclick="closeRejectModal()"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="reject-student-id">
      <label for="reject-reason">Alasan Penolakan</label>
      <textarea id="reject-reason" placeholder="Contoh: Data tidak lengkap atau tidak sesuai syarat..."></textarea>
    </div>
    <div class="modal-footer" style="display:flex;justify-content:space-between;">
      <button class="btn" type="button" onclick="closeRejectModal()" style="background:#F5F5F5;color:#424242">Batal</button>
      <button class="btn btn-primary" type="button" onclick="submitRejectReason()">Tolak</button>
    </div>
  </div>
</div>

<!-- Rejection History Modal -->
<div id="rejectionHistoryModal" style="display:none" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-header-title">
        <div class="icon"><span class="material-icons">history</span></div>
        <h3>Riwayat Penolakan</h3>
      </div>
      <button class="modal-close" onclick="closeRejectionModal()"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body" id="rejectionHistoryList">
      <div class="empty-rejection-state">
        <div class="icon"><span class="material-icons">hourglass_empty</span></div>
        <p>Memuat riwayat penolakan...</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeRejectionModal()" style="background:#F5F5F5;color:#424242">Tutup</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let filterStatus = 'pending';
let searchQuery = '';

function setFilter(status) {
  filterStatus = status;
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  document.getElementById('chip-' + status)?.classList.add('active');
  loadPending();
}

const loadPendingDebounced = debounce(() => loadPending(), 250);

function onSearchInput() {
  const value = document.getElementById('pending-search').value.trim().toLowerCase();
  searchQuery = value;
  loadPendingDebounced();
}

function debounce(fn, delay) {
  let timeoutId;
  return function(...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
}

function formatDate(value) {
  if (!value) return '-';
  const dt = new Date(value);
  if (Number.isNaN(dt.getTime())) {
    const normalized = String(value).trim().replace(' ', 'T');
    const fallback = new Date(normalized);
    if (Number.isNaN(fallback.getTime())) {
      return '-';
    }
    return fallback.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
  }
  return dt.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusBadge(status) {
  const classes = {
    pending: 'status-badge badge-orange',
    approved: 'status-badge badge-green',
    rejected: 'status-badge badge-red',
    suspended: 'status-badge badge-grey',
  };
  const labels = {
    pending: 'Pending',
    approved: 'Disetujui',
    rejected: 'Ditolak',
    suspended: 'Suspend',
  };
  return `<span class="${classes[status] || 'status-badge'}">${labels[status] || status}</span>`;
}

function setDateColumnVisible(visible) {
  const header = document.getElementById('pending-date-header');
  if (header) {
    header.classList.toggle('hidden-column', !visible);
  }
  document.querySelectorAll('.col-date').forEach(cell => {
    cell.classList.toggle('hidden-column', !visible);
  });
}

let rejectionRowsById = {};

function normalizeRow(row) {
  const isRejectionHistory = !row.approval_status && !row.is_suspended && !!row.reason && row.student_id;
  const isApproved = row.approval_status === 'approved' || row.student?.approval_status === 'approved';
  const isSuspended = row.is_suspended === true || row.is_suspended === 1 || row.approval_status === 'suspended' || row.student?.approval_status === 'suspended';
  const status = isRejectionHistory
    ? 'rejected'
    : isSuspended
      ? 'suspended'
      : isApproved
        ? 'approved'
        : 'pending';
  const registrationDate = row.created_at ?? row.student?.created_at ?? '-';
  const approvalDate = row.updated_at ?? row.student?.updated_at ?? registrationDate;

  return {
    id: row.id,
    name: row.user?.name ?? row.name ?? '-',
    email: row.user?.email ?? row.email ?? '-',
    nis: row.nis ?? row.student?.nis ?? '-',
    sekolah: row.sekolah ?? row.student?.sekolah ?? '-',
    alamat: row.alamat ?? row.user?.alamat ?? row.student?.alamat ?? '-',
    no_hp: row.no_hp ?? row.user?.no_hp ?? '-',
    status,
    tanggal: status === 'approved' ? approvalDate : registrationDate,
    created_at: row.created_at ?? row.student?.created_at ?? null,
    reason: row.reason ?? '-',
    history_student_id: row.student_id ?? null,
    rejected_by: typeof row.rejected_by === 'object' && row.rejected_by?.name ? row.rejected_by.name : row.rejected_by ?? null,
  };
}

function applySearchFilter(rows) {
  if (!searchQuery) return rows;
  return rows.filter(row => {
    const text = [row.name, row.email, row.nis, row.sekolah, row.alamat, row.no_hp].join(' ').toLowerCase();
    return text.includes(searchQuery);
  });
}

async function loadPending() {
  const tbody = document.getElementById('pending-tbody');
  tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--c-text-grey)">
    <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat data...</td></tr>`;

  try {
    let rows = [];
    if (filterStatus === 'pending') {
      const res = await api.get('/students/pending', { per_page: 500 });
      rows = res.data?.data ?? [];
    } else if (filterStatus === 'rejected') {
      const res = await api.get('/student-rejection-histories', { per_page: 500 });
      rows = res.data?.data ?? [];
    } else if (filterStatus === 'approved') {
      const res = await api.get('/students', { per_page: 500, approval_status: 'approved' });
      rows = res.data?.data ?? [];
    } else if (filterStatus === 'suspended') {
      const res = await api.get('/students', { per_page: 500 });
      rows = res.data?.data ?? [];
      rows = rows.filter(r => r.is_suspended === true || r.is_suspended === 1 || r.approval_status === 'suspended');
    }

    rows = rows.map(normalizeRow);
    if (filterStatus === 'rejected') {
      rejectionRowsById = {};
      rows.forEach(row => { rejectionRowsById[row.id] = row; });
    }
    rows = applySearchFilter(rows);

    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:42px;color:var(--c-text-grey)">
        <div class="empty-state"><span class="material-icons">search_off</span>
        <p style="margin:8px 0 0;">Tidak ada data untuk status "${filterStatus}"</p></div></td></tr>`;
      setDateColumnVisible(filterStatus === 'rejected');
      return;
    }

    const dateHeader = document.getElementById('pending-date-header');
    if (dateHeader) {
      dateHeader.textContent = filterStatus === 'rejected' ? 'Tanggal Ditolak'
        : filterStatus === 'approved' ? 'Tanggal Disetujui'
        : 'Tanggal Daftar';
    }

    tbody.innerHTML = rows.map(row => {
      const actions = row.status === 'pending'
        ? `<div class="table-actions">
            <button type="button" class="btn btn-primary btn-sm" onclick="approve(${row.id})">
              <span class="material-icons" style="font-size:16px">check</span> Setujui
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="openRejectModal(${row.id})">
              <span class="material-icons" style="font-size:16px">close</span> Tolak
            </button>
           </div>`
        : row.status === 'approved'
        ? `<div class="table-actions"><button class="btn btn-sm btn-outline-warning" onclick="suspend(${row.id},'approved')">
              <span class="material-icons" style="font-size:16px">pause_circle</span> Suspend
           </button></div>`
        : row.status === 'suspended'
        ? `<div class="table-actions"><button class="btn btn-primary btn-sm" onclick="suspend(${row.id},'suspended')">
              <span class="material-icons" style="font-size:16px">play_arrow</span> Aktifkan
           </button></div>`
        : row.status === 'rejected'
        ? `<div class="table-actions">
            <button class="btn btn-sm btn-outline" onclick="showRejectionDetail(${row.id})">
              <span class="material-icons" style="font-size:16px">info</span> Detail
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteRejectionHistory(${row.id})">
              <span class="material-icons" style="font-size:16px">delete</span> Hapus Riwayat
            </button>
          </div>`
        : '';

      return `<tr>
        <td>${row.name}</td>
        <td class="col-email">${row.email}</td>
        <td>${row.nis}</td>
        <td>${row.sekolah}</td>
        <td class="col-alamat">${row.alamat}</td>
        <td>${row.no_hp}</td>
        <td>${statusBadge(row.status)}</td>
        <td class="col-date">${formatDate(row.tanggal)}</td>
        <td>${actions}</td>
      </tr>`;
    }).join('');
    setDateColumnVisible(filterStatus === 'rejected');
  } catch (err) {
    console.error('Failed load pending data', err);
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--c-text-grey)">
      Terjadi kesalahan saat memuat data.</td></tr>`;
  }
}

function showRejectionDetail(historyId) {
  const row = rejectionRowsById[historyId];
  if (!row) {
    toast('Riwayat tidak ditemukan', 'error');
    return;
  }

  const rejectedByName = row.rejected_by ? row.rejected_by : 'System';
  const list = document.getElementById('rejectionHistoryList');
  list.innerHTML = `
    <div class="rejection-item">
      <div class="rejection-item-header">
        <span class="rejection-item-date">${formatDate(row.created_at)}</span>
        <div class="rejection-item-by">
          <div class="icon"><span class="material-icons" style="font-size:10px">person</span></div>
          ${rejectedByName}
        </div>
      </div>
      <div style="margin-bottom:12px;font-size:13px;color:#424242">
        <strong>Nama:</strong> ${row.name}<br>
        <strong>Email:</strong> ${row.email}<br>
        <strong>NIS:</strong> ${row.nis}<br>
        <strong>Sekolah:</strong> ${row.sekolah}<br>
        <strong>No HP:</strong> ${row.no_hp}
      </div>
      <div class="rejection-item-reason">${row.reason}</div>
    </div>`;
  document.getElementById('rejectionHistoryModal').style.display = 'flex';
}

async function deleteRejectionHistory(historyId) {
  confirmDialog('Hapus riwayat penolakan ini?', async () => {
    const r = await api.delete('/student-rejection-histories/' + historyId);
    if (r.ok) {
      toast('Riwayat berhasil dihapus');
      loadPending();
    } else {
      toast(r.data?.message ?? 'Gagal menghapus riwayat', 'error');
    }
  });
}

async function approve(userId) {
  confirmDialog('Setujui akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/approve');
    if (r.ok) {
      toast('Akun disetujui');
      loadPending();
    } else {
      toast(r.data?.message ?? 'Gagal menyetujui akun', 'error');
    }
  });
}

function openRejectModal(userId) {
  document.getElementById('reject-student-id').value = userId;
  document.getElementById('reject-reason').value = '';
  const modal = document.getElementById('reject-modal');
  if (modal) {
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
  }
}

function closeRejectModal() {
  const modal = document.getElementById('reject-modal');
  if (modal) {
    modal.style.display = 'none';
  }
}

function closeRejectionModal() {
  const modal = document.getElementById('rejectionHistoryModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

async function submitRejectReason() {
  const userId = document.getElementById('reject-student-id').value;
  const reason = document.getElementById('reject-reason').value.trim();
  if (!reason) {
    toast('Alasan penolakan wajib diisi', 'warning');
    return;
  }
  confirmDialog('Tolak akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/reject', { reason });
    if (r.ok) {
      toast('Akun ditolak', 'warn');
      closeRejectModal();
      loadPending();
    } else {
      toast(r.data?.message ?? 'Gagal menolak akun', 'error');
    }
  });
}

async function suspend(userId, currentStatus) {
  const isSus = currentStatus === 'suspended';
  confirmDialog(isSus ? 'Aktifkan kembali akun ini?' : 'Suspend akun ini?', async () => {
    const endpoint = isSus ? '/students/' + userId + '/unsuspend' : '/students/' + userId + '/suspend';
    const r = await api.post(endpoint);
    if (r.ok) {
      toast(isSus ? 'Akun diaktifkan' : 'Akun disuspend', 'warn');
      loadPending();
    } else {
      toast(r.data?.message ?? 'Gagal mengubah status', 'error');
    }
  });
}

loadPending();
</script>
@endpush
