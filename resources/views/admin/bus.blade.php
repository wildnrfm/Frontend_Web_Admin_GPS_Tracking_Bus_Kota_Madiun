@extends('admin.layouts.app')
@section('title','Manajemen Bus')
@section('page-title','Bus')
@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="openAddModal()">
  <span class="material-icons" style="font-size:16px">add</span> Tambah
</button>
@endsection
@section('content')

<div class="filter-bar">
  <div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" id="search" placeholder="Cari kode bus, plat nomor..." oninput="debounce(loadBus,400)()">
  </div>
  <button class="btn btn-icon" onclick="loadBus()"><span class="material-icons">refresh</span></button>
</div>

<div style="padding:12px 14px; display:flex; gap:8px; flex-wrap:wrap; border-bottom:1px solid var(--c-border)">
  <button class="filter-btn active" data-filter="all" onclick="setBusFilter('all', this)">Semua</button>
  <button class="filter-btn" data-filter="aktif" onclick="setBusFilter('aktif', this)">Aktif</button>
  <button class="filter-btn" data-filter="maintenance" onclick="setBusFilter('maintenance', this)">Perawatan</button>
  <button class="filter-btn" data-filter="non_aktif" onclick="setBusFilter('non_aktif', this)">Nonaktif</button>
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
</style>

<div class="card" style="padding:0">
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Kode Bus</th><th>Plat Nomor</th><th>Status</th><th>GPS</th><th>Aksi</th></tr></thead>
      <tbody id="bus-tbody">
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-text-grey)">
          <div class="loading-spinner" style="margin:0 auto 8px"></div>Memuat...
        </td></tr>
      </tbody>
    </table>
  </div>
  <div id="bus-pagination" style="padding:12px 14px"></div>
</div>

{{-- Modal Bus --}}
<div class="modal-overlay" id="bus-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="bus-modal-title">Tambah Bus</div>
      <button class="modal-close" onclick="closeModal('bus-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <form id="bus-form">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 14px">
          <div class="form-group"><label class="form-label">Kode Bus</label>
            <input class="form-control" name="kode_bus" placeholder="BUS-01" required></div>
          <div class="form-group"><label class="form-label">Plat Nomor</label>
            <input class="form-control" name="plat_nomor" placeholder="AE 1234 XX" required></div>
          <div class="form-group"><label class="form-label">Status</label>
            <select class="form-control" name="status">
              <option value="aktif">Aktif</option>
              <option value="maintenance">Perawatan</option>
              <option value="non_aktif">Non-aktif</option>
            </select>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('bus-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveBus()">Simpan</button>
    </div>
  </div>
</div>

{{-- Modal Assign Driver --}}
<div class="modal-overlay" id="assign-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <div class="modal-title">Assign Driver</div>
      <button class="modal-close" onclick="closeModal('assign-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Pilih Driver</label>
        <select class="form-control" id="assign-driver-select"><option value="">Pilih driver...</option></select>
      </div>
      <div class="form-group"><label class="form-label">Tanggal Mulai</label>
        <input class="form-control" id="assign-start" type="date"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('assign-modal')">Batal</button>
      <button class="btn btn-primary btn-sm" onclick="saveAssign()">Assign</button>
    </div>
  </div>
</div>

{{-- Modal Route & Halte --}}
<div class="modal-overlay" id="route-halte-modal">
  <div class="modal" style="max-width:650px;max-height:90vh;overflow-y:auto">
    <div class="modal-header" style="position:sticky;top:0;z-index:10">
      <div>
        <div class="modal-title" id="route-title-text">Rute & Halte</div>
        <div style="font-size:12px;color:var(--c-text-grey);margin-top:4px" id="route-polyline-count"></div>
      </div>
      <button class="modal-close" onclick="closeModal('route-halte-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <!-- Peta -->
      <div id="route-map" style="width:100%;height:320px;border-radius:8px;margin-bottom:20px;background:#f0f0f0"></div>
      
      <!-- Urutan Halte -->
      <div style="margin-bottom:20px">
        <div style="font-weight:600;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
          <span>📍 Urutan Halte</span>
          <span id="halte-count-badge" style="background:var(--c-primary);color:white;padding:2px 8px;border-radius:12px;font-size:12px">0 halte</span>
        </div>
        <div id="halte-list-content" style="display:flex;flex-direction:column;gap:12px"></div>
      </div>
      
      <!-- Info Guides -->
      <div style="background:#E8F5E9;border-left:4px solid #2E7D32;padding:12px;border-radius:4px;font-size:12px;margin-bottom:16px">
        <div style="font-weight:600;color:#1B5E20;margin-bottom:8px">📌 Cara membuat rute</div>
        <ol style="margin:0;padding-left:16px">
          <li>Pastikan halte sudah terdaftar di menu Halte</li>
          <li>Tap "Ubah Rute" untuk setting rute baru (atau ubah rute lama)</li>
          <li>Pilih halte-halte yang dilalui bus, atur urutannya</li>
          <li>Jalan di peta otomatis terekam ikuti jalan nyata</li>
        </ol>
      </div>
    </div>
    <div class="modal-footer" style="position:sticky;bottom:0;z-index:10">
      <button class="btn btn-sm btn-outline" onclick="closeModal('route-halte-modal')">Tutup</button>
      <button class="btn btn-sm" style="background:#2E7D32;color:white;border:none" id="edit-route-btn" onclick="editRoute()">✏️ Ubah Rute</button>
      <button class="btn btn-sm" style="background:#D32F2F;color:white;border:none" id="delete-route-btn" onclick="deleteRoute()">🗑️ Hapus</button>
    </div>
  </div>
</div>

{{-- Modal Siswa --}}
<div class="modal-overlay" id="siswa-modal">
  <div class="modal" style="max-width:600px">
    <div class="modal-header">
      <div class="modal-title">Kelola Siswa - <span id="siswa-bus-name"></span></div>
      <button class="modal-close" onclick="closeModal('siswa-modal')"><span class="material-icons">close</span></button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom:12px">
        <button class="btn btn-primary btn-sm" onclick="openAddSiswaForm()">+ Tambah Siswa</button>
      </div>
      <div id="siswa-content" style="max-height:400px;overflow-y:auto"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('siswa-modal')">Tutup</button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let editId = null, assignBusId = null, currentPage = 1, currentFilter = 'all';
const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

function setBusFilter(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadBus(1);
}

async function loadBus(page = 1) {
  currentPage = page;
  const q = document.getElementById('search').value;
  const res = await api.get('/buses', { search: q, per_page: 1000 });
  
  let rows = res.data?.data ?? [];
  
  // Apply filter
  if (currentFilter !== 'all') {
    rows = rows.filter(b => b.status === currentFilter);
  }
  
  const tbody = document.getElementById('bus-tbody');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><span class="material-icons">directions_bus</span><p>Tidak ada bus</p></div></td></tr>`;
    document.getElementById('bus-pagination').innerHTML = ''; return;
  }
  
  const perPage = 15;
  const start = (page - 1) * perPage;
  const paginatedRows = rows.slice(start, start + perPage);
  
  tbody.innerHTML = paginatedRows.map((b, i) => `
    <tr>
      <td>${start + i + 1}</td>
      <td><span style="font-weight:700;color:var(--c-primary)">${b.kode_bus}</span></td>
      <td>${b.plat_nomor}</td>
      <td>${statusBadge(b.status)}</td>
      <td><span class="badge ${b.gps_active ? 'badge-green':'badge-grey'}">${b.gps_active ? 'ON':'OFF'}</span></td>
      <td>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          <button class="btn btn-xs btn-outline" onclick="editBus(${b.id})">Edit</button>
          <button class="btn btn-xs" style="background:var(--c-primary);color:white" onclick="openRouteHalte(${b.id},'${b.kode_bus}')">Rute & Halte</button>
          <button class="btn btn-xs" style="background:#E3F0FB;color:var(--c-primary)" onclick="openSiswa(${b.id},'${b.kode_bus}')">Siswa</button>
          <button class="btn btn-xs" style="background:#E3F0FB;color:var(--c-blue)" onclick="openAssign(${b.id})">Driver</button>
          <button class="btn btn-xs btn-icon" onclick="deleteBus(${b.id})"><span class="material-icons" style="font-size:14px">delete</span></button>
        </div>
      </td>
    </tr>`).join('');
  
  const totalPages = Math.ceil(rows.length / perPage);
  const paginationHtml = totalPages > 1 ? `
    <div style="display:flex;justify-content:center;gap:4px;padding:8px">
      ${page > 1 ? `<button class="btn btn-sm btn-outline" onclick="loadBus(${page - 1})">← Sebelumnya</button>` : ''}
      <span style="padding:8px 12px">Halaman ${page} dari ${totalPages}</span>
      ${page < totalPages ? `<button class="btn btn-sm btn-outline" onclick="loadBus(${page + 1})">Berikutnya →</button>` : ''}
    </div>
  ` : '';
  document.getElementById('bus-pagination').innerHTML = paginationHtml;
}

function openAddModal() {
  editId = null;
  document.getElementById('bus-modal-title').textContent = 'Tambah Bus';
  document.getElementById('bus-form').reset();
  openModal('bus-modal');
}

async function editBus(id) {
  editId = id;
  document.getElementById('bus-modal-title').textContent = 'Edit Bus';
  const res = await api.get('/buses/' + id);
  const b = res.data?.data;
  const f = document.getElementById('bus-form');
  f.kode_bus.value = b.kode_bus ?? '';
  f.plat_nomor.value = b.plat_nomor ?? '';
  f.status.value = b.status ?? 'aktif';
  openModal('bus-modal');
}

async function saveBus() {
  const f = document.getElementById('bus-form');
  const body = { kode_bus: f.kode_bus.value, plat_nomor: f.plat_nomor.value, status: f.status.value };
  const res = editId ? await api.put('/buses/' + editId, body) : await api.post('/buses', body);
  res.ok ? (toast('Bus berhasil disimpan'), closeModal('bus-modal'), loadBus(currentPage)) : toast(res.data?.message ?? 'Gagal', 'error');
}

async function openAssign(busId) {
  assignBusId = busId;
  const drRes = await api.get('/drivers', { per_page: 100 });
  const drivers = drRes.data ?? [];
  const sel = document.getElementById('assign-driver-select');
  sel.innerHTML = `<option value="">Pilih driver...</option>` + drivers.map(d => `<option value="${d.id}">${d.user?.name ?? d.name}</option>`).join('');
  document.getElementById('assign-start').value = new Date().toISOString().split('T')[0];
  openModal('assign-modal');
}

async function saveAssign() {
  const driverId = document.getElementById('assign-driver-select').value;
  const startDate = document.getElementById('assign-start').value;
  if (!driverId) { toast('Pilih driver terlebih dahulu', 'warn'); return; }
  const res = await api.post('/buses/' + assignBusId + '/drivers', { driver_id: driverId, tanggal_mulai: startDate });
  res.ok ? (toast('Driver berhasil di-assign'), closeModal('assign-modal'), loadBus(currentPage)) : toast(res.data?.message ?? 'Gagal', 'error');
}

async function deleteBus(id) {
  confirmDialog('Hapus bus ini?', async () => {
    const r = await api.delete('/buses/' + id);
    r.ok ? (toast('Bus dihapus', 'warn'), loadBus(currentPage)) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

// ──── Route & Halte ────
let currentBusId = null;
let routeMapInstance = null;
let currentRouteData = null;

async function openRouteHalte(busId, busName) {
  currentBusId = busId;
  
  // Get route for this bus
  const res = await api.get(`/buses/${busId}/route`);
  const route = res.data?.data;
  
  if (!route) {
    document.getElementById('route-title-text').textContent = 'Rute & Halte - ' + busName;
    document.getElementById('route-polyline-count').textContent = 'Belum ada rute untuk bus ini';
    document.getElementById('halte-list-content').innerHTML = '<p style="color:var(--c-text-grey);text-align:center;padding:20px">Belum ada halte</p>';
    document.getElementById('halte-count-badge').textContent = '0 halte';
    document.getElementById('route-map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--c-text-grey)">Belum ada data rute</div>';
    openModal('route-halte-modal');
    return;
  }
  
  currentRouteData = route;
  
  // Set title
  document.getElementById('route-title-text').textContent = route.nama_rute || 'Rute & Halte - ' + busName;
  document.getElementById('route-polyline-count').textContent = (route.polyline?.length || 0) + ' titik polyline';
  
  // Set halte count
  const halteCount = route.haltes?.length || 0;
  document.getElementById('halte-count-badge').textContent = halteCount + ' halte';
  
  // Render halte list
  let halteHtml = '';
  if (halteCount > 0) {
    for (let i = 0; i < route.haltes.length; i++) {
      const halte = route.haltes[i];
      const halteData = halte.halte;
      const colors = ['#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4'];
      const color = colors[i % colors.length];
      
      halteHtml += `
        <div style="display:flex;gap:12px;padding:12px;border:1px solid var(--c-border);border-radius:6px">
          <div style="display:flex;align-items:center;justify-content:center;min-width:36px;width:36px;height:36px;background:${color};color:white;border-radius:50%;font-weight:600">${i + 1}</div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px">${halteData?.nama_halte || 'N/A'}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${halteData?.alamat || 'Alamat tidak tersedia'}</div>
          </div>
        </div>
      `;
    }
  } else {
    halteHtml = '<p style="color:var(--c-text-grey);text-align:center;padding:20px">Belum ada halte dalam rute ini</p>';
  }
  document.getElementById('halte-list-content').innerHTML = halteHtml;
  
  // Render map
  setTimeout(() => {
    if (routeMapInstance) {
      routeMapInstance.remove();
    }
    
    const mapContainer = document.getElementById('route-map');
    routeMapInstance = L.map(mapContainer).setView([-7.6288, 111.5305], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(routeMapInstance);
    
    // Plot polyline
    if (route.polyline && route.polyline.length > 0) {
      const polylineCoords = route.polyline.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);
      L.polyline(polylineCoords, { color: '#2196F3', weight: 3, opacity: 0.8 }).addTo(routeMapInstance);
      routeMapInstance.fitBounds(L.latLngBounds(polylineCoords));
    }
    
    // Plot halte markers
    if (route.haltes && route.haltes.length > 0) {
      const colors = ['#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4'];
      route.haltes.forEach((rh, idx) => {
        const halteData = rh.halte;
        if (halteData?.latitude && halteData?.longitude) {
          const color = colors[idx % colors.length];
          const marker = L.circleMarker(
            [parseFloat(halteData.latitude), parseFloat(halteData.longitude)],
            { radius: 24, fillColor: color, color: color, weight: 2, opacity: 1, fillOpacity: 0.8 }
          ).addTo(routeMapInstance);
          
          marker.bindPopup(`<div style="font-weight:600">${halteData.nama_halte}</div><div style="font-size:12px">${halteData.alamat}</div>`);
          
          L.marker([parseFloat(halteData.latitude), parseFloat(halteData.longitude)], {
            icon: L.divIcon({
              html: `<div style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;background:${color};color:white;border-radius:50%;font-weight:600;font-size:14px">${idx + 1}</div>`,
              iconSize: [30, 30],
              className: 'custom-marker'
            })
          }).addTo(routeMapInstance);
        }
      });
    }
  }, 100);
  
  openModal('route-halte-modal');
}

async function editRoute() {
  if (currentRouteData) {
    toast('Edit rute feature sedang dikembangkan', 'info');
  }
}

async function deleteRoute() {
  if (currentRouteData) {
    confirmDialog('Hapus rute ini?', async () => {
      const r = await api.delete('/routes/' + currentRouteData.id);
      if (r.ok) {
        toast('Rute dihapus');
        closeModal('route-halte-modal');
        loadBus(currentPage);
      } else {
        toast(r.data?.message ?? 'Gagal menghapus rute', 'error');
      }
    });
  }
}

// ──── Siswa ────
async function openSiswa(busId, busName) {
  currentBusId = busId;
  document.getElementById('siswa-bus-name').textContent = busName;
  
  // Get students for this bus (API returns paginated data)
  const res = await api.get(`/buses/${busId}/students`);
  // Handle both direct array and paginated response structure
  const students = res.data?.data?.data ?? res.data?.data ?? [];
  
  if (!students || students.length === 0) {
    document.getElementById('siswa-content').innerHTML = `
      <div class="empty-state">
        <p style="color:var(--c-text-grey)">Belum ada siswa untuk bus ini</p>
      </div>
    `;
  } else {
    let html = `<div style="display:flex;flex-direction:column;gap:8px">`;
    for (const siswa of students) {
      const namaSiswa = siswa.user?.name || siswa.name || 'N/A';
      const emailSiswa = siswa.user?.email || siswa.email || 'N/A';
      const siswaId = siswa.id || siswa.student_id;
      
      html += `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;border:1px solid var(--c-border);border-radius:6px;background:white">
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px">${namaSiswa}</div>
            <div style="font-size:12px;color:var(--c-text-grey)">${emailSiswa}</div>
            ${siswa.halte_tujuan ? `<div style="font-size:11px;color:#1976d2;background:#e3f2fd;padding:2px 6px;border-radius:3px;display:inline-block;margin-top:4px">📍 ${siswa.halte_tujuan}</div>` : ''}
          </div>
          <button class="btn btn-xs btn-icon" onclick="removeSiswaFromBus(${siswaId})" style="background:#ffebee;border:none"><span class="material-icons" style="font-size:14px;color:#d32f2f">delete</span></button>
        </div>
      `;
    }
    html += `</div>`;
    document.getElementById('siswa-content').innerHTML = html;
  }
  
  openModal('siswa-modal');
}

async function openAddSiswaForm() {
  toast('Fitur tambah siswa sedang dikembangkan', 'info');
}

async function removeSiswaFromBus(siswaId) {
  confirmDialog('Hapus siswa dari bus ini?', async () => {
    const r = await api.delete(`/buses/${currentBusId}/students/${siswaId}`);
    r.ok ? (toast('Siswa dihapus dari bus'), openSiswa(currentBusId, 'Bus')) : toast(r.data?.message ?? 'Gagal', 'error');
  });
}

loadBus();
</script>
@endpush
