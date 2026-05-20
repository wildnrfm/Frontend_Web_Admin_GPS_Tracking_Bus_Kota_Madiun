// Debug logging utility with visual panel
window.debugLogs = [];
window.debugPanel = null;

function debugLog(msg) {
  console.log(msg);
  window.debugLogs.push(msg);
  
  // Update visual panel if it exists
  if (window.debugPanel) {
    const div = document.createElement('div');
    div.className = 'debug-line';
    div.style.fontSize = '10px';
    div.style.color = '#0f0';
    div.textContent = msg.substring(0, 70);
    window.debugPanel.appendChild(div);
    
    // Keep only last 25 lines
    const lines = window.debugPanel.querySelectorAll('.debug-line');
    if (lines.length > 25) lines[0].remove();
    
    window.debugPanel.scrollTop = window.debugPanel.scrollHeight;
  }
}

class ApiClient {
  constructor() {
    // Point to actual API server at port 8000
    this.base = 'http://localhost:8000/api';
    this.token = document.querySelector('meta[name="admin-token"]')?.content ?? '';
    debugLog(`[DEBUG] ApiClient initialized with token: ${this.token ? this.token.substring(0,20) + '...' : '(empty)'}`);
  }
  headers() {
    return { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token };
  }
  async get(path, params = {}, silent = false) {
    const url = new URL(this.base + path, location.origin);
    Object.entries(params).forEach(([k, v]) => v !== '' && url.searchParams.set(k, v));
    const r = await fetch(url, { headers: this.headers() });
    return this._handle(r, silent);
  }
  async post(path, body = {}) {
    const r = await fetch(this.base + path, { method: 'POST', headers: this.headers(), body: JSON.stringify(body) });
    return this._handle(r);
  }
  async put(path, body = {}) {
    const r = await fetch(this.base + path, { method: 'PUT', headers: this.headers(), body: JSON.stringify(body) });
    return this._handle(r);
  }
  async delete(path) {
    const r = await fetch(this.base + path, { method: 'DELETE', headers: this.headers() });
    return this._handle(r);
  }
  async postForm(path, formData) {
    const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token };
    const r = await fetch(this.base + path, { method: 'POST', headers, body: formData });
    return this._handle(r);
  }
  async putForm(path, formData) {
    const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token };
    const r = await fetch(this.base + path, { method: 'PUT', headers, body: formData });
    return this._handle(r);
  }
  async _handle(r, silent = false) {
    // Cek content-type: kalau Laravel return HTML (bukan JSON), jangan redirect
    const ct = r.headers.get('content-type') ?? '';
    let data;
    const rawText = await r.clone().text();  // Get raw response
    try { 
      data = ct.includes('json') ? JSON.parse(rawText) : {}; 
    } catch (e) {
      debugLog(`[ERROR] JSON parse failed: ${e.message}`);
      debugLog(`[DEBUG] Raw response (first 300 chars): ${rawText.substring(0, 300)}`);
      data = {}; 
    }
    debugLog(`[DEBUG] API Response: status=${r.status}, ct=${ct}, dataKeys=${Object.keys(data).join(',')} rawLen=${rawText.length}`);
    // Redirect ke login hanya jika: 401 JSON, tidak silent, tidak di halaman login
    if (r.status === 401 && !silent && ct.includes('json') && !location.pathname.includes('/admin/login')) {
      location.href = '/admin/login';
    }
    return { ok: r.ok, status: r.status, data };
  }
}

const api = new ApiClient();

/* ── Toast ─────────────────────────────────────────────────────── */
function toast(msg, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  const icon = type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'warning';
  el.innerHTML = `<span class="material-icons" style="font-size:18px">${icon}</span>${msg}`;
  container.appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

/* ── Modal helpers ─────────────────────────────────────────────── */
function openModal(id) { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay') && !e.target.dataset.noClose) {
    e.target.classList.remove('open');
  }
});

/* ── Confirm dialog ─────────────────────────────────────────────── */
function confirmDialog(msg, onYes) {
  document.getElementById('confirm-msg').textContent = msg;
  openModal('confirm-modal');
  const btn = document.getElementById('confirm-yes');
  const clone = btn.cloneNode(true);
  btn.parentNode.replaceChild(clone, btn);
  clone.addEventListener('click', () => { closeModal('confirm-modal'); onYes(); });
}

/* ── Tab switching ─────────────────────────────────────────────── */
function initTabs(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      container.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      container.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById(btn.dataset.tab)?.classList.add('active');
    });
  });
}

/* ── Sidebar active state ───────────────────────────────────────── */
function setActiveNav() {
  const path = location.pathname;
  document.querySelectorAll('.nav-item[data-href]').forEach(el => {
    el.classList.toggle('active', el.dataset.href === path || (path.startsWith(el.dataset.href) && el.dataset.href !== '/admin/'));
  });
  document.querySelectorAll('.bottom-nav-item[data-href]').forEach(el => {
    el.classList.toggle('active', el.dataset.href === path);
  });
}

/* ── Bottom nav click ────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  setActiveNav();
  document.querySelectorAll('.nav-item[data-href], .bottom-nav-item[data-href]').forEach(el => {
    el.addEventListener('click', () => location.href = el.dataset.href);
  });
});

/* ── Pending badge loader ────────────────────────────────────────── */
async function loadPendingBadge() {
  // Jangan fetch jika tidak ada token (halaman guest/login)
  if (!api.token) return;
  try {
    // silent=true: jangan redirect ke login kalau API gagal/401
    const res = await api.get('/students', { status: 'pending', per_page: 1 }, true);
    if (!res.ok) return; // Diam-diam abaikan error
    const count = res.data?.meta?.total ?? res.data?.data?.filter(s => s.approval_status === 'pending')?.length ?? 0;
    document.querySelectorAll('.pending-badge-count').forEach(el => {
      el.textContent = count > 0 ? (count > 99 ? '99+' : count) : '';
      el.style.display = count > 0 ? 'flex' : 'none';
    });
  } catch {}
}

/* ── Greeting ────────────────────────────────────────────────────── */
function greeting() {
  const h = new Date().getHours();
  if (h < 11) return 'Selamat pagi';
  if (h < 15) return 'Selamat siang';
  if (h < 18) return 'Selamat sore';
  return 'Selamat malam';
}

/* ── Formatters ─────────────────────────────────────────────────── */
function fmtDate(str) {
  if (!str) return '-';
  return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
function fmtDateTime(str) {
  if (!str) return '-';
  return new Date(str).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

/* ── Status badge helper ─────────────────────────────────────────── */
function statusBadge(status, isSuspended = false) {
  // Prioritas: suspended > approval_status
  if (isSuspended) {
    return `<span class="badge badge-grey">Nonaktif</span>`;
  }
  
  const map = {
    approved: ['badge-green', 'Disetujui'],
    active:   ['badge-green', 'Aktif'],
    pending:  ['badge-orange', 'Pending'],
    rejected: ['badge-red', 'Ditolak'],
    suspended:['badge-red', 'Nonaktif'],
    inactive: ['badge-grey', 'Tidak Aktif'],
    aktif:    ['badge-green', 'Aktif'],
    maintenance: ['badge-orange', 'Perawatan'],
    non_aktif:   ['badge-grey', 'Non-aktif'],
  };
  const [cls, label] = map[status] ?? ['badge-grey', status ?? '-'];
  return `<span class="badge ${cls}">${label}</span>`;
}

/* ── Pagination renderer ─────────────────────────────────────────── */
function renderPagination(meta, onPage) {
  if (!meta || meta.last_page <= 1) return '';
  let html = '<div style="display:flex;gap:6px;align-items:center;justify-content:flex-end;margin-top:14px;flex-wrap:wrap">';
  html += `<span style="font-size:12px;color:var(--c-text-grey)">${meta.total} data</span>`;
  for (let i = 1; i <= meta.last_page; i++) {
    const active = i === meta.current_page;
    html += `<button onclick="(${onPage.toString()})(${i})" class="btn btn-xs ${active ? 'btn-primary' : 'btn-icon'}">${i}</button>`;
  }
  html += '</div>';
  return html;
}

/* ── Confirm modal HTML (insert once) ──────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('confirm-modal')) {
    document.body.insertAdjacentHTML('beforeend', `
      <div class="modal-overlay" id="confirm-modal">
        <div class="modal" style="max-width:380px">
          <div class="modal-header">
            <div class="modal-title">Konfirmasi</div>
            <button class="modal-close" onclick="closeModal('confirm-modal')"><span class="material-icons">close</span></button>
          </div>
          <div class="modal-body"><p id="confirm-msg" style="font-size:14px"></p></div>
          <div class="modal-footer">
            <button class="btn btn-outline btn-sm" onclick="closeModal('confirm-modal')">Batal</button>
            <button id="confirm-yes" class="btn btn-danger btn-sm">Ya, Lanjutkan</button>
          </div>
        </div>
      </div>`);
  }
  // Hanya load badge jika sudah login (token tersedia)
  const token = document.querySelector('meta[name="admin-token"]')?.content;
  if (token) loadPendingBadge();
});
