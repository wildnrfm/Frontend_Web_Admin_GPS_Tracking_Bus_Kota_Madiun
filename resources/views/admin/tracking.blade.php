@extends('admin.layouts.app')
@section('title','Live Tracking')
@section('page-title','Live Tracking')
@section('content')

<div id="tracking-main-grid" style="display:grid;grid-template-columns:1fr 300px;gap:16px;height:calc(100vh - var(--topbar-h) - var(--bottomnav-h) - 40px)">

  {{-- Map --}}
  <div class="card" style="padding:0;overflow:hidden;position:relative;min-height:400px">
    <div id="tracking-map" style="height:100%;min-height:400px;border-radius:var(--radius-lg)"></div>
    <div id="sse-status" style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.5);color:#fff;font-size:11px;padding:4px 10px;border-radius:20px;z-index:400">
      <span id="sse-dot" style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#aaa;margin-right:4px"></span>
      <span id="sse-label">Menghubungkan...</span>
    </div>
    {{-- Route editor toggle --}}
    <div style="position:absolute;top:12px;left:12px;z-index:400;display:flex;flex-direction:column;gap:6px">
      <button class="btn btn-primary btn-sm" id="btn-route-editor" onclick="toggleRouteEditor()" title="Atur Rute">
        <span class="material-icons" style="font-size:14px">route</span> Atur Rute
      </button>
    </div>
  </div>

  {{-- Side Panel --}}
  <div style="display:flex;flex-direction:column;gap:12px;overflow-y:auto;padding-right:2px">

    {{-- Bus list --}}
    <div class="card" style="flex:1;overflow-y:auto">
      <div class="card-header" style="margin-bottom:10px">
        <div class="card-title">Bus Aktif</div>
        <span class="badge badge-green" id="active-count">0</span>
      </div>
      <div id="tracking-bus-list">
        <div class="empty-state" style="padding:24px">
          <span class="material-icons">directions_bus</span>
          <p>Menunggu data GPS...</p>
        </div>
      </div>
    </div>

    {{-- Route Editor Panel --}}
    <div class="card" id="route-editor-panel" style="display:none">
      <div style="font-size:14px;font-weight:700;margin-bottom:10px">
        <span class="material-icons" style="font-size:16px;vertical-align:middle;color:var(--c-primary)">route</span>
        Editor Rute
      </div>
      <div class="form-group">
        <label class="form-label">Pilih Bus</label>
        <select class="form-control" id="route-bus-select" onchange="loadRouteForBus()">
          <option value="">-- Pilih bus --</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Nama Rute</label>
        <input class="form-control" id="route-name" placeholder="Contoh: Rute A Pagi">
      </div>
      <div class="info-bar" style="font-size:11px;margin-bottom:10px">
        <span class="material-icons" style="font-size:13px">info</span>
        Klik peta untuk tambah titik rute. Drag marker untuk pindah.
      </div>
      <div style="font-size:12px;color:var(--c-text-grey);margin-bottom:6px">Titik rute: <b id="route-points-count">0</b></div>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <button class="btn btn-outline btn-sm" onclick="clearRoute()">
          <span class="material-icons" style="font-size:13px">clear</span> Hapus Semua
        </button>
        <button class="btn btn-primary btn-sm" onclick="saveRoute()">
          <span class="material-icons" style="font-size:13px">save</span> Simpan Rute
        </button>
      </div>
    </div>

  </div>
</div>

{{-- Styles: tracking page overrides --}}
<style>
/* Remove default page-content padding for full-height map layout */
.page-content { padding: 16px !important; padding-bottom: calc(var(--bottomnav-h) + 16px) !important; }

/* Mobile: stack map on top, panel below */
@media (max-width: 640px) {
  #tracking-main-grid {
    grid-template-columns: 1fr !important;
    height: auto !important;
  }
  #tracking-main-grid > div:first-child { height: 55vh; min-height: 260px; }
}

/* Desktop: wider side panel */
@media (min-width: 1024px) {
  #tracking-main-grid { grid-template-columns: 1fr 340px; gap: 20px; }
}
@media (min-width: 1280px) {
  #tracking-main-grid { grid-template-columns: 1fr 380px; gap: 24px; }
  .page-content { padding: 20px !important; padding-bottom: calc(var(--bottomnav-h) + 20px) !important; }
}
</style>

@endsection
@push('scripts')
<script>
// ── MAP INIT ─────────────────────────────────────────────────────
let tMap, busMarkers = {}, routePolyline = null, routePoints = [], routeMarkers = [];
let sseSource = null, routeEditorActive = false, selectedBusForRoute = null;

document.addEventListener('DOMContentLoaded', () => {
  tMap = L.map('tracking-map', { attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(tMap);
  tMap.setView([-7.6298, 111.5233], 13);
  tMap.on('click', onMapClick);

  loadBusesForEditor();
  startSSE();
});

// ── SSE GPS STREAM ────────────────────────────────────────────────
function startSSE() {
  const token = document.querySelector('meta[name="admin-token"]').content;
  const dot = document.getElementById('sse-dot');
  const label = document.getElementById('sse-label');

  // SSE endpoint — token di query param karena EventSource tdk support custom headers
  // Jika SSE gagal, fallback ke polling tiap 5 detik
  let sseWorking = false;
  try {
    if (sseSource) { sseSource.close(); }
    // Kita coba SSE via fetch streaming; jika gagal gunakan polling
    fallbackPolling();
  } catch(e) {
    fallbackPolling();
  }
}

async function fallbackPolling() {
  const dot = document.getElementById('sse-dot');
  const label = document.getElementById('sse-label');
  dot.style.background = '#4CAF50'; label.textContent = 'Live';
  await pollGPS();
  setInterval(pollGPS, 5000);
}

async function pollGPS() {
  try {
    const res = await api.get('/gps-tracks/dashboard');
    const buses = res.data?.data ?? [];
    const mapped = buses.map(b => ({
      bus_id: b.bus_id, bus_code: b.bus_code, bus_plate: b.bus_plate,
      gps_status: b.gps_status, driver_name: b.driver?.name ?? '',
      position: b.current_position
    }));
    updateBusMarkers(mapped);
  } catch(e) {
    const dot = document.getElementById('sse-dot');
    const label = document.getElementById('sse-label');
    dot.style.background = '#F44336'; label.textContent = 'Error';
  }
}

function updateBusMarkers(buses) {
  const active = buses.filter(b => b.gps_status === 'on' && b.position);
  document.getElementById('active-count').textContent = active.length;

  // Remove stale markers
  Object.keys(busMarkers).forEach(id => {
    if (!active.find(b => b.bus_id == id)) { tMap.removeLayer(busMarkers[id]); delete busMarkers[id]; }
  });

  let listHtml = '';
  active.forEach(b => {
    const pos = b.position;
    const ll = [pos.latitude, pos.longitude];
    const icon = L.divIcon({
      html: `<div style="background:var(--c-primary);color:#fff;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 2px 8px rgba(0,0,0,.3)">🚌</div>`,
      iconSize: [34, 34], iconAnchor: [17, 17], className: ''
    });
    if (busMarkers[b.bus_id]) busMarkers[b.bus_id].setLatLng(ll);
    else busMarkers[b.bus_id] = L.marker(ll, { icon }).addTo(tMap)
      .bindPopup(`<b>${b.bus_code}</b><br>${b.bus_plate}<br>Driver: ${b.driver_name || '-'}<br>${(pos.speed||0).toFixed(0)} km/h`);

    listHtml += `<div class="bus-item" onclick="focusBus(${b.bus_id})">
      <div class="bus-icon-wrap" style="background:var(--c-primary-light)">
        <span class="material-icons" style="color:var(--c-primary);font-size:18px">directions_bus</span>
      </div>
      <div style="flex:1">
        <div style="font-weight:600;font-size:13px">${b.bus_code}</div>
        <div style="font-size:11px;color:var(--c-text-grey)">${b.driver_name || '-'}</div>
      </div>
      <div style="text-align:right">
        <span class="live-badge">LIVE</span>
        <div style="font-size:12px;font-weight:600;margin-top:2px">${(pos.speed||0).toFixed(0)} km/h</div>
      </div>
    </div>`;
  });

  if (!active.length) listHtml = `<div class="empty-state" style="padding:24px"><span class="material-icons">directions_bus</span><p>Belum ada bus beroperasi</p></div>`;
  document.getElementById('tracking-bus-list').innerHTML = listHtml;
}

function focusBus(busId) {
  const m = busMarkers[busId];
  if (m) { tMap.setView(m.getLatLng(), 16); m.openPopup(); }
}

// ── ROUTE EDITOR ─────────────────────────────────────────────────
async function loadBusesForEditor() {
  const res = await api.get('/buses', { per_page: 100 });
  const buses = res.data?.data ?? [];
  const sel = document.getElementById('route-bus-select');
  buses.forEach(b => sel.add(new Option(b.kode_bus + ' — ' + b.plat_nomor, b.id)));
}

function toggleRouteEditor() {
  routeEditorActive = !routeEditorActive;
  document.getElementById('route-editor-panel').style.display = routeEditorActive ? '' : 'none';
  document.getElementById('btn-route-editor').innerHTML = routeEditorActive
    ? '<span class="material-icons" style="font-size:14px">close</span> Tutup Editor'
    : '<span class="material-icons" style="font-size:14px">route</span> Atur Rute';
}

function onMapClick(e) {
  if (!routeEditorActive) return;
  addRoutePoint(e.latlng);
}

function addRoutePoint(latlng) {
  routePoints.push([latlng.lat, latlng.lng]);
  const m = L.circleMarker(latlng, { radius: 6, color: '#1B5E37', fillColor: '#A7C957', fillOpacity: 1, draggable: true })
    .addTo(tMap);
  m.on('drag', () => redrawPolyline());
  m.on('dragend', ev => {
    const idx = routeMarkers.indexOf(m);
    if (idx >= 0) routePoints[idx] = [ev.target.getLatLng().lat, ev.target.getLatLng().lng];
    redrawPolyline();
  });
  routeMarkers.push(m);
  redrawPolyline();
  document.getElementById('route-points-count').textContent = routePoints.length;
}

function redrawPolyline() {
  if (routePolyline) tMap.removeLayer(routePolyline);
  if (routePoints.length > 1) {
    routePolyline = L.polyline(routeMarkers.map(m => m.getLatLng()), { color: '#1B5E37', weight: 4, opacity: .8 }).addTo(tMap);
  }
}

function clearRoute() {
  routePoints = [];
  routeMarkers.forEach(m => tMap.removeLayer(m)); routeMarkers = [];
  if (routePolyline) { tMap.removeLayer(routePolyline); routePolyline = null; }
  document.getElementById('route-points-count').textContent = 0;
}

async function loadRouteForBus() {
  const busId = document.getElementById('route-bus-select').value;
  if (!busId) return;
  clearRoute();
  const res = await api.get('/buses/' + busId + '/route');
  const route = res.data?.data ?? res.data;
  if (!route) return;
  document.getElementById('route-name').value = route.nama_rute ?? '';
  const polylines = route.polylines ?? [];
  polylines.sort((a,b) => a.urutan - b.urutan).forEach(p => addRoutePoint({lat: p.latitude, lng: p.longitude}));
}

async function saveRoute() {
  const busId = document.getElementById('route-bus-select').value;
  const name  = document.getElementById('route-name').value;
  if (!busId || !name) { toast('Pilih bus dan isi nama rute', 'warn'); return; }
  if (routePoints.length < 2) { toast('Minimal 2 titik rute', 'warn'); return; }

  // Get existing route id or create
  const existing = await api.get('/buses/' + busId + '/route');
  let routeId = existing.data?.data?.id ?? existing.data?.id;
  let res;
  if (routeId) {
    res = await api.post('/routes/' + routeId + '/sync', {
      nama_rute: name,
      polylines: routePoints.map((p, i) => ({ latitude: p[0], longitude: p[1], urutan: i + 1 })),
      haltes: []
    });
  } else {
    const createRes = await api.post('/routes', { bus_id: busId, nama_rute: name });
    routeId = createRes.data?.data?.id ?? createRes.data?.id;
    if (routeId) {
      res = await api.post('/routes/' + routeId + '/sync', {
        nama_rute: name,
        polylines: routePoints.map((p, i) => ({ latitude: p[0], longitude: p[1], urutan: i + 1 })),
        haltes: []
      });
    }
  }
  res?.ok ? toast('Rute berhasil disimpan') : toast(res?.data?.message ?? 'Gagal simpan rute', 'error');
}
</script>
@endpush
