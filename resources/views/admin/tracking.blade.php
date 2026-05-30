@extends('admin.layouts.app')
@section('title','Live Tracking')
@section('page-title','Live Tracking')
@section('content')

<style>
/* ╔══════════════════════════════════════════════════════════════╗ */
/* ║              TRACKING PAGE REDESIGN STYLES                    ║ */
/* ╚══════════════════════════════════════════════════════════════╝ */
.tracking-hero {
  background: linear-gradient(135deg, #0F3D22 0%, #1B5E37 60%, #2E7D52 100%);
  border-radius: 20px;
  padding: 26px 28px;
  color: #fff;
  margin-bottom: 22px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.24);
}
.tracking-hero::before {
  content: '';
  position: absolute;
  top: -70px; right: -50px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
}
.tracking-hero::after {
  content: '';
  position: absolute;
  bottom: -50px; left: -30px;
  width: 180px; height: 180px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
}
.tracking-hero-top {
  display: flex; align-items: center; gap: 16px;
}
.tracking-hero-icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  background: rgba(255,255,255,0.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
}
.tracking-hero-text h2 {
  margin: 0; font-size: 24px; font-weight: 700; color: #fff;
}
.tracking-hero-text p {
  margin: 4px 0 0; font-size: 13px; color: rgba(255,255,255,.82);
}
.tracking-action-bar {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; margin-top: 18px; flex-wrap: wrap;
}
.tracking-action-bar .btn-route {
  background: linear-gradient(135deg, #0F5E2C 0%, #2E9D63 100%);
  border: none; color: #fff;
}

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

/* ── Custom Pin Map Bus Marker ── */
.map-bus-marker {
  width: 44px;
  height: 44px;
  position: relative;
  filter: drop-shadow(0 4px 10px rgba(15, 61, 34, 0.25));
}
.map-bus-marker-pulse {
  position: absolute;
  top: -2px; left: -2px;
  width: 44px; height: 44px;
  border-radius: 50%;
  border: 2px solid var(--c-primary, #1B5E37);
  animation: markerPulse 1.8s infinite ease-out;
  pointer-events: none;
  z-index: -1;
}
@keyframes markerPulse {
  0% { transform: scale(0.95); opacity: 0.8; }
  100% { transform: scale(1.4); opacity: 0; }
}
.map-bus-marker-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 3px solid var(--c-primary, #1B5E37);
  background: var(--c-primary-light, #E8F5ED);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.map-bus-marker-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.map-bus-marker-fallback {
  font-size: 20px;
  line-height: 1;
}
.map-bus-marker-label {
  position: absolute;
  top: -6px;
  right: -6px;
  background: var(--c-primary-dark, #0F3D22);
  color: #fff;
  border-radius: 10px;
  padding: 2px 6px;
  font-size: 9px;
  font-weight: 800;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  white-space: nowrap;
  border: 1.5px solid #fff;
}
.map-bus-marker-arrow {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid var(--c-primary, #1B5E37);
  z-index: 2;
}
</style>

<div class="tracking-hero">
  <div class="tracking-hero-top">
    <div class="tracking-hero-icon"><span class="material-icons">map</span></div>
    <div class="tracking-hero-text">
      <h2>Live Tracking</h2>
      <p>Pantau armada bus secara real-time dan kelola rute dengan cepat</p>
    </div>
  </div>
  <div class="tracking-action-bar">
    <div style="font-size:13px;color:rgba(255,255,255,0.9)">Status koneksi GPS dan titik armada langsung tampil di peta</div>
  </div>
</div>

<div id="tracking-main-grid" style="display:grid;grid-template-columns:1fr 300px;gap:16px;height:calc(100vh - var(--topbar-h) - var(--bottomnav-h) - 40px)">

  {{-- Map --}}
  <div class="card" style="padding:0;overflow:hidden;position:relative;min-height:400px">
    <div id="tracking-map" style="height:100%;min-height:400px;border-radius:var(--radius-lg)"></div>
    <div id="sse-status" style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.5);color:#fff;font-size:11px;padding:4px 10px;border-radius:20px;z-index:400">
      <span id="sse-dot" style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#aaa;margin-right:4px"></span>
      <span id="sse-label">Menghubungkan...</span>
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



  </div>
</div>

@endsection
@push('scripts')
<script>
// ── MAP INIT ─────────────────────────────────────────────────────
let tMap, busMarkers = {};
let sseSource = null;
let sseReconnectDelay = 1000;
let sseReconnectTimer = null;
let fallbackPollTimer = null;
let useFallback = false;
let prevTrackStatus = {}; // untuk deteksi perubahan GPS status di tracking page
let activeRoutePolyline = null;
let activeStopMarkers = [];

document.addEventListener('DOMContentLoaded', () => {
  tMap = L.map('tracking-map', { attributionControl: false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(tMap);
  tMap.setView([-7.6298, 111.5233], 13);

  // Bersihkan rute terpilih saat klik area kosong di peta
  tMap.on('click', (e) => {
    if (e.target === tMap) {
      clearActiveRoute();
    }
  });

  startSSE();
});

function clearActiveRoute() {
  if (activeRoutePolyline) {
    tMap.removeLayer(activeRoutePolyline);
    activeRoutePolyline = null;
  }
  activeStopMarkers.forEach(m => tMap.removeLayer(m));
  activeStopMarkers = [];
}

// ── GPS Toast (pakai fungsi global dari app.blade.php jika ada) ───
function showTrackingToast(driverName, status, busCode) {
  if (typeof showGpsToast === 'function') {
    showGpsToast(driverName, status, busCode);
    return;
  }
  // Fallback: pakai toast bawaan admin jika ada
  if (typeof toast === 'function') {
    const msg = status === 'on'
      ? `Driver ${driverName} mengaktifkan GPS (${busCode})`
      : `Driver ${driverName} mematikan GPS (${busCode})`;
    toast(msg, status === 'on' ? 'success' : 'warn');
  }
}

// ── SSE GPS STREAM ────────────────────────────────────────────────
function setConnStatus(color, text) {
  const dot   = document.getElementById('sse-dot');
  const label = document.getElementById('sse-label');
  if (dot)   dot.style.background = color;
  if (label) label.textContent = text;
}

function startSSE() {
  const token = window.adminToken || document.querySelector('meta[name="admin-token"]')?.content;
  if (!token) { startFallback(); return; }

  const baseUrl = window.apiBaseUrl || '/api';
  const url = baseUrl + '/gps-tracks/stream?token=' + encodeURIComponent(token);

  if (sseSource) { sseSource.close(); sseSource = null; }

  setConnStatus('#FF9800', 'Menghubungkan...');

  try {
    sseSource = new EventSource(url);

    sseSource.addEventListener('gps_update', function(e) {
      sseReconnectDelay = 1000; // reset
      useFallback = false;
      setConnStatus('#4CAF50', 'Live');

      try {
        const data  = JSON.parse(e.data);
        const buses = data.buses ?? data;

        // Deteksi perubahan GPS status → toast
        if (Array.isArray(buses)) {
          buses.forEach(b => {
            const prev = prevTrackStatus[b.bus_id];
            const curr = b.gps_status;
            if (prev !== undefined && prev !== curr) {
              showTrackingToast(b.driver_name || '—', curr, b.bus_code || '');
            }
            prevTrackStatus[b.bus_id] = curr;
          });
        }

        const mapped = (Array.isArray(buses) ? buses : []).map(b => ({
          bus_id:      b.bus_id,
          bus_code:    b.bus_code,
          bus_plate:   b.bus_plate,
          photo_url:   b.photo_url,
          gps_status:  b.gps_status,
          driver_name: b.driver_name ?? b.driver?.name ?? '',
          position:    b.position ?? b.current_position,
        }));
        updateBusMarkers(mapped);
      } catch(err) { /* ignore */ }
    });

    sseSource.addEventListener('ping', function(e) {
      setConnStatus('#4CAF50', 'Live');
      sseReconnectDelay = 1000;
    });

    sseSource.addEventListener('close', function(e) {
      sseSource.close();
      scheduleReconnect();
    });

    sseSource.onerror = function() {
      sseSource.close();
      sseSource = null;
      if (sseReconnectDelay >= 8000) {
        startFallback();
      } else {
        scheduleReconnect();
      }
    };

    // Stop fallback polling jika SSE berhasil
    if (fallbackPollTimer) { clearInterval(fallbackPollTimer); fallbackPollTimer = null; }

  } catch(e) {
    startFallback();
  }
}

function scheduleReconnect() {
  setConnStatus('#FF9800', 'Reconnecting...');
  if (sseReconnectTimer) clearTimeout(sseReconnectTimer);
  sseReconnectTimer = setTimeout(() => {
    sseReconnectDelay = Math.min(sseReconnectDelay * 2, 30000);
    startSSE();
  }, sseReconnectDelay);
}

function startFallback() {
  if (useFallback) return;
  useFallback = true;
  setConnStatus('#FF9800', 'Polling');
  pollGPS();
  fallbackPollTimer = setInterval(pollGPS, 3000);
}

async function pollGPS() {
  try {
    const res = await api.get('/gps-tracks/dashboard');
    const buses = res.data?.data?.data ?? res.data?.data ?? [];

    // Deteksi perubahan untuk fallback polling juga
    buses.forEach(b => {
      const prev = prevTrackStatus[b.bus_id];
      const curr = b.gps_status;
      if (prev !== undefined && prev !== curr) {
        showTrackingToast(b.driver?.name ?? b.driver_name ?? '—', curr, b.bus_code || '');
      }
      prevTrackStatus[b.bus_id] = curr;
    });

    const mapped = buses.map(b => ({
      bus_id:      b.bus_id,
      bus_code:    b.bus_code,
      bus_plate:   b.bus_plate,
      photo_url:   b.photo_url,
      gps_status:  b.gps_status,
      driver_name: b.driver?.name ?? '',
      position:    b.current_position,
    }));
    updateBusMarkers(mapped);
  } catch(e) {
    setConnStatus('#F44336', 'Error');
  }
}

function updateBusMarkers(buses) {
  const active = buses.filter(b => b.gps_status === 'on' && b.position);
  document.getElementById('active-count').textContent = active.length;

  // Remove stale markers
  Object.keys(busMarkers).forEach(id => {
    if (!active.find(b => b.bus_id == id)) { tMap.removeLayer(busMarkers[id]); delete busMarkers[id]; }
  });

  // Urutkan bus: Aktif (ON) dulu, baru Offline (OFF)
  const sortedBuses = [...buses].sort((a, b) => {
    const aOn = a.gps_status === 'on' ? 1 : 0;
    const bOn = b.gps_status === 'on' ? 1 : 0;
    return bOn - aOn; // 1 (aktif) di atas, 0 (offline) di bawah
  });

  let listHtml = '';
  sortedBuses.forEach(b => {
    const isOn = b.gps_status === 'on';
    const busPhotoHtml = b.photo_url 
      ? `<img src="${proxyImgUrl(b.photo_url)}" style="width:36px;height:36px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="${b.bus_code}">`
      : `<div class="bus-icon-wrap" style="background:${isOn ? 'rgba(76, 175, 80, 0.1)' : 'rgba(244, 67, 54, 0.1)'}">
          <span class="material-icons" style="color:${isOn ? '#4CAF50' : '#F44336'};font-size:18px">directions_bus</span>
        </div>`;

    if (isOn && b.position) {
      const pos = b.position;
      const ll = [pos.latitude, pos.longitude];
      const markerHtml = `
        <div class="map-bus-marker">
          <div class="map-bus-marker-pulse"></div>
          <div class="map-bus-marker-avatar">
            ${b.photo_url 
              ? `<img src="${proxyImgUrl(b.photo_url)}" class="map-bus-marker-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">` 
              : ''}
            <span class="map-bus-marker-fallback" style="${b.photo_url ? 'display:none' : ''}">🚌</span>
          </div>
          <div class="map-bus-marker-label">${b.bus_code}</div>
          <div class="map-bus-marker-arrow"></div>
        </div>
      `;
      const icon = L.divIcon({
        html: markerHtml,
        iconSize: [44, 48],
        iconAnchor: [22, 48],
        className: ''
      });
      if (busMarkers[b.bus_id]) {
        busMarkers[b.bus_id].setLatLng(ll);
      } else {
        busMarkers[b.bus_id] = L.marker(ll, { icon }).addTo(tMap)
          .bindPopup(`<b>${b.bus_code}</b><br>${b.bus_plate}<br>Driver: ${b.driver_name || '-'}<br>${(pos.speed||0).toFixed(0)} km/h`);
        
        // Klik pada marker memicu muat rute & fokus
        busMarkers[b.bus_id].on('click', () => {
          focusBus(b.bus_id);
        });
      }

      listHtml += `<div class="bus-item" onclick="focusBus(${b.bus_id})" style="border-left: 4px solid #4CAF50; margin-bottom: 6px; cursor: pointer;">
        ${busPhotoHtml}
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">${b.bus_code}</div>
          <div style="font-size:11px;color:var(--c-text-grey)">${b.driver_name || '-'}</div>
        </div>
        <div style="text-align:right">
          <span class="live-badge" style="background:#4CAF50">LIVE</span>
          <div style="font-size:12px;font-weight:600;margin-top:2px">${(pos.speed||0).toFixed(0)} km/h</div>
        </div>
      </div>`;
    } else {
      listHtml += `<div class="bus-item" style="border-left: 4px solid #F44336; margin-bottom: 6px; opacity: 0.75; cursor: default;">
        ${busPhotoHtml}
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px;color:var(--c-text-grey)">${b.bus_code}</div>
          <div style="font-size:11px;color:var(--c-text-grey)">${b.driver_name || '-'}</div>
        </div>
        <div style="text-align:right">
          <span class="live-badge" style="background:#F44336">OFFLINE</span>
        </div>
      </div>`;
    }
  });

  if (!sortedBuses.length) listHtml = `<div class="empty-state" style="padding:24px"><span class="material-icons">directions_bus</span><p>Belum ada bus beroperasi</p></div>`;
  document.getElementById('tracking-bus-list').innerHTML = listHtml;
}

async function focusBus(busId) {
  const m = busMarkers[busId];
  if (m) { 
    tMap.setView(m.getLatLng(), 15); 
    m.openPopup(); 
  }

  // Clear rute & halte sebelumnya
  clearActiveRoute();

  try {
    const res = await api.get('/buses/' + busId + '/route').catch(() => null);
    if (!res || !res.ok) return;

    const route = res.data?.data ?? res.data;
    if (!route) return;

    // 1. Plot Halte-Halte Bernomor
    const routeHaltes = (route.haltes ?? []).sort((a, b) => a.urutan - b.urutan);
    const colors = ['#4CAF50','#F44336','#2196F3','#FF9800','#9C27B0','#00BCD4','#795548','#607D8B'];
    const haltes = [];

    routeHaltes.forEach((rh, i) => {
      const h = rh.halte;
      if (!h || !h.latitude || !h.longitude) return;
      const latlng = [parseFloat(h.latitude), parseFloat(h.longitude)];
      haltes.push(h);

      // Marker bulatan halte berwarna
      const circle = L.circleMarker(latlng, {
        radius: 12,
        fillColor: colors[i % colors.length],
        color: '#fff',
        weight: 2,
        fillOpacity: 1
      }).addTo(tMap).bindPopup(`<b>Halte ${i+1}: ${h.nama_halte}</b><br>${h.alamat || ''}`);

      // Marker teks nomor halte di dalam bulatan
      const label = L.marker(latlng, {
        icon: L.divIcon({
          html: `<div style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:10px;font-family:Poppins,sans-serif;margin-top:-2px">${i+1}</div>`,
          iconSize: [20, 20],
          className: ''
        })
      }).addTo(tMap);

      activeStopMarkers.push(circle);
      activeStopMarkers.push(label);
    });

    // 2. Gambar Rute Jalan Raya (OSRM) jika ada 2+ halte
    if (haltes.length >= 2) {
      const waypoints = haltes.map(h => `${parseFloat(h.longitude)},${parseFloat(h.latitude)}`).join(';');
      const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;

      try {
        const resp = await fetch(osrmUrl);
        const data = await resp.json();
        if (data.code === 'Ok' && data.routes?.[0]?.geometry) {
          const rGeo = data.routes[0];
          const geojsonCoords = rGeo.geometry.coordinates.map(c => [c[1], c[0]]);
          activeRoutePolyline = L.polyline(geojsonCoords, {
            color: '#1B5E37',
            weight: 5,
            opacity: 0.9,
            lineJoin: 'round',
            lineCap: 'round'
          }).addTo(tMap);
        } else {
          // Fallback ke garis lurus antar halte
          drawFallbackLine(haltes);
        }
      } catch (err) {
        // Fallback ke garis lurus
        drawFallbackLine(haltes);
      }
    } else {
      // Jika halte < 2, coba pakai database polyline (jika ada)
      const polyPoints = route.polyline ?? [];
      if (polyPoints.length >= 2) {
        const latlngs = polyPoints.sort((a, b) => a.urutan - b.urutan).map(p => [p.latitude, p.longitude]);
        activeRoutePolyline = L.polyline(latlngs, {
          color: '#1B5E37',
          weight: 5,
          opacity: 0.85,
          lineJoin: 'round',
          lineCap: 'round'
        }).addTo(tMap);
      }
    }

    // Sesuaikan kamera agar menampilkan seluruh rute secara pas
    if (activeRoutePolyline) {
      tMap.fitBounds(activeRoutePolyline.getBounds(), { padding: [50, 50] });
    } else if (haltes.length > 0) {
      const coords = haltes.map(h => [parseFloat(h.latitude), parseFloat(h.longitude)]);
      tMap.fitBounds(L.latLngBounds(coords), { padding: [50, 50] });
    }
  } catch (err) {
    console.error('Gagal mengambil data rute bus:', err);
  }
}

function drawFallbackLine(haltes) {
  const coords = haltes.map(h => [parseFloat(h.latitude), parseFloat(h.longitude)]);
  activeRoutePolyline = L.polyline(coords, {
    color: '#1B5E37',
    weight: 4.5,
    opacity: 0.75,
    dashArray: '8,5',
    lineJoin: 'round',
    lineCap: 'round'
  }).addTo(tMap);
}




</script>
@endpush
