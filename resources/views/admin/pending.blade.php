@extends('admin.layouts.app')
@section('title','Persetujuan Akun')
@section('page-title','Persetujuan Akun')
@section('content')

<div class="filter-bar">
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <button class="chip active" id="chip-pending"   onclick="setFilter('pending')">Pending</button>
    <button class="chip"        id="chip-approved"  onclick="setFilter('approved')">Disetujui</button>
    <button class="chip"        id="chip-rejected"  onclick="setFilter('rejected')">Ditolak</button>
    <button class="chip"        id="chip-suspended" onclick="setFilter('suspended')">Suspend</button>
  </div>
  <button class="btn btn-icon" onclick="loadPending()"><span class="material-icons">refresh</span></button>
</div>

<div id="pending-cards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px">
  <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--c-text-grey)">
    <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...
  </div>
</div>

@endsection
@push('scripts')
<script>
let filterStatus = 'pending';

function setFilter(s) {
  filterStatus = s;
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  document.getElementById('chip-' + s)?.classList.add('active');
  loadPending();
}

async function loadPending() {
  const container = document.getElementById('pending-cards');
  container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--c-text-grey)">
    <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...</div>`;

  // Pending → endpoint khusus /students/pending
  // Lainnya → load semua lalu filter di client (API tidak support ?status=)
  let rows = [];
  if (filterStatus === 'pending') {
    const res = await api.get('/students/pending', { per_page: 100 });
    rows = res.data?.data ?? [];
  } else {
    const res = await api.get('/students', { per_page: 500 });
    const all = res.data?.data ?? [];
    rows = all.filter(s => {
      const st = s.approval_status ?? s.student?.approval_status;
      if (filterStatus === 'approved')  return st === 'approved'  && !s.is_suspended;
      if (filterStatus === 'rejected')  return st === 'rejected';
      if (filterStatus === 'suspended') return s.is_suspended === true || s.is_suspended === 1;
      return false;
    });
  }

  if (!rows.length) {
    container.innerHTML = `<div style="grid-column:1/-1">
      <div class="empty-state"><span class="material-icons">check_circle</span>
      <p>Tidak ada siswa dengan status "${filterStatus}"</p></div></div>`;
    return;
  }

  container.innerHTML = rows.map(s => {
    const st    = s.approval_status ?? s.student?.approval_status ?? (s.is_suspended ? 'suspended' : 'approved');
    const name  = s.user?.name  ?? s.name  ?? '-';
    const email = s.user?.email ?? s.email ?? '-';
    const nis   = s.nis   ?? s.student?.nis   ?? '-';
    const sek   = s.sekolah ?? s.student?.sekolah ?? '-';
    const hp    = s.no_hp ?? s.user?.no_hp ?? '-';
    const userId = s.user_id ?? s.id;

    const actions = st === 'pending' ? `
      <button class="btn btn-primary btn-sm" onclick="approve(${userId})">
        <span class="material-icons" style="font-size:14px">check</span> Setujui
      </button>
      <button class="btn btn-sm" style="background:#FDECEA;color:var(--c-red)" onclick="openRejectModal(${userId})">
        <span class="material-icons" style="font-size:14px">close</span> Tolak
      </button>` :
      st === 'approved' ? `
        <button class="btn btn-sm" style="background:#FFF3CD;color:var(--c-amber)" onclick="suspend(${userId},'approved')">Suspend</button>` :
      st === 'suspended' ? `
        <button class="btn btn-primary btn-sm" onclick="suspend(${userId},'suspended')">Aktifkan</button>` : '';

    return `<div class="card">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
        <div style="width:44px;height:44px;border-radius:50%;background:var(--c-primary-light);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;color:var(--c-primary);flex-shrink:0">
          ${name.charAt(0).toUpperCase()}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${name}</div>
          <div style="font-size:11px;color:var(--c-text-grey);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${email}</div>
        </div>
        ${statusBadge(st)}
      </div>
      <div style="font-size:12px;color:var(--c-text-grey);display:flex;flex-direction:column;gap:4px;margin-bottom:14px">
        <div><b>NIS:</b> ${nis}</div>
        <div><b>Sekolah:</b> ${sek}</div>
        <div><b>No HP:</b> ${hp}</div>
        <div><b>Daftar:</b> ${fmtDate(s.created_at)}</div>
        ${s.rejection_reason ? `<div style="color:var(--c-red)"><b>Alasan Tolak:</b> ${s.rejection_reason}</div>` : ''}
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">${actions}</div>
    </div>`;
  }).join('');
}

async function approve(userId) {
  confirmDialog('Setujui akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/approve');
    r.ok ? (toast('Akun disetujui'), loadPending()) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

function openRejectModal(userId) {
  const reason = prompt('Masukkan alasan penolakan (wajib diisi):');
  if (!reason || !reason.trim()) { toast('Alasan penolakan wajib diisi', 'warn'); return; }
  confirmDialog('Tolak akun siswa ini?', async () => {
    const r = await api.post('/students/' + userId + '/reject', { reason: reason.trim() });
    r.ok ? (toast('Akun ditolak', 'warn'), loadPending()) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

async function suspend(userId, currentStatus) {
  const isSus = currentStatus === 'suspended';
  confirmDialog(isSus ? 'Aktifkan kembali akun ini?' : 'Suspend akun ini?', async () => {
    const endpoint = isSus ? '/students/' + userId + '/unsuspend' : '/students/' + userId + '/suspend';
    const r = await api.post(endpoint);
    r.ok
      ? (toast(isSus ? 'Akun diaktifkan' : 'Akun disuspend', 'warn'), loadPending())
      : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

loadPending();
// Auto-refresh setiap 30 detik
setInterval(loadPending, 30000);
</script>
@endpush
