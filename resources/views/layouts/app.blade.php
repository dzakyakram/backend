<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'NusantaraMap') — PANEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<style>
  :root {
    --black: #0a0a0a;
    --white: #f5f0e8;
    --yellow: #FFE135;
    --red:    #FF3B30;
    --blue:   #0047FF;
    --green:  #00C853;
    --border: 3px solid #0a0a0a;
    --shadow: 4px 4px 0 #0a0a0a;
    --shadow-lg: 6px 6px 0 #0a0a0a;
  }
  * { box-sizing: border-box; }
  body { font-family: 'Space Mono', monospace; background: var(--white); color: var(--black); margin: 0; }
  h1,h2,h3,h4,.font-display { font-family: 'Syne', sans-serif; }

  .nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 14px; font-size: 13px; font-weight: 700; border: 2px solid transparent; color: var(--black); text-decoration: none; transition: background 0.1s; font-family: 'Space Mono', monospace; letter-spacing: -0.3px; }
  .nav-link:hover { background: var(--yellow); border: 2px solid var(--black); }
  .nav-link.active { background: var(--black); color: var(--yellow); border: 2px solid var(--black); }

  .brut-card { background: var(--white); border: var(--border); box-shadow: var(--shadow); }
  .brut-card-lg { background: var(--white); border: var(--border); box-shadow: var(--shadow-lg); }

  .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-family: 'Space Mono', monospace; font-size: 12px; font-weight: 700; border: 2px solid var(--black); cursor: pointer; transition: transform 0.1s, box-shadow 0.1s; box-shadow: 3px 3px 0 var(--black); text-decoration: none; }
  .btn:hover { transform: translate(-1px,-1px); box-shadow: 4px 4px 0 var(--black); }
  .btn:active { transform: translate(2px,2px); box-shadow: 1px 1px 0 var(--black); }
  .btn-black { background: var(--black); color: var(--yellow); }
  .btn-yellow { background: var(--yellow); color: var(--black); }
  .btn-red { background: var(--red); color: #fff; }
  .btn-green { background: var(--green); color: var(--black); }
  .btn-blue { background: var(--blue); color: #fff; }
  .btn-ghost { background: var(--white); color: var(--black); }
  .btn-sm { padding: 5px 10px; font-size: 11px; box-shadow: 2px 2px 0 var(--black); }

  .badge { display: inline-block; padding: 3px 10px; font-size: 10px; font-weight: 700; font-family: 'Space Mono', monospace; border: 2px solid var(--black); letter-spacing: 0.5px; text-transform: uppercase; }
  .badge-pending  { background: var(--yellow); color: var(--black); }
  .badge-approved { background: var(--green);  color: var(--black); }
  .badge-rejected { background: var(--red);    color: #fff; }
  .badge-admin    { background: var(--black);  color: var(--yellow); }
  .badge-pengelola{ background: var(--blue);   color: #fff; }
  .badge-pengguna { background: #ddd;          color: var(--black); }

  .brut-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .brut-table thead { background: var(--black); color: var(--yellow); }
  .brut-table th { padding: 12px 14px; text-align: left; font-family: 'Space Mono', monospace; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; border-right: 2px solid #333; }
  .brut-table th:last-child { border-right: none; }
  .brut-table td { padding: 12px 14px; border-bottom: 2px solid var(--black); border-right: 2px solid #e0dbd0; }
  .brut-table td:last-child { border-right: none; }
  .brut-table tbody tr:hover td { background: var(--yellow); }

  .brut-input { width: 100%; padding: 10px 14px; font-family: 'Space Mono', monospace; font-size: 13px; border: var(--border); background: var(--white); color: var(--black); outline: none; transition: box-shadow 0.1s; }
  .brut-input:focus { box-shadow: 4px 4px 0 var(--black); }
  .brut-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%230a0a0a' stroke-width='3'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }

  .flash-success { background: var(--green); border: var(--border); box-shadow: var(--shadow); padding: 12px 16px; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
  .flash-error   { background: var(--red);   border: var(--border); box-shadow: var(--shadow); padding: 12px 16px; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 16px; }

  ::-webkit-scrollbar { width: 8px; }
  ::-webkit-scrollbar-track { background: #e8e4d8; border-left: 2px solid var(--black); }
  ::-webkit-scrollbar-thumb { background: var(--black); }
  [x-cloak]{display:none!important;}

  /* ── APP SHELL ── */
  .app-shell { display: flex; height: 100vh; overflow: hidden; position: relative; isolation: isolate; }

  .sidebar {
    width: 270px; background: #f5f0e8;
    border-right: 3px solid #0a0a0a;
    display: flex; flex-direction: column; flex-shrink: 0;
    transition: transform 0.25s ease; z-index: 200;
  }
  .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

  .topbar {
    height: 56px; background: var(--yellow);
    border-bottom: 3px solid var(--black);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 24px; flex-shrink: 0; overflow: hidden;
    position: relative; z-index: 1000;
  }
  .topbar > div:first-child { display: flex; align-items: center; min-width: 0; overflow: hidden; }
  .topbar .page-title { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }

  .hamburger { display: none; background: none; border: 2px solid var(--black); cursor: pointer; padding: 6px 8px; margin-right: 10px; box-shadow: 2px 2px 0 var(--black); flex-shrink: 0; width: 36px; height: 36px; align-items: center; justify-content: center; }
  /* overlay di dalam app-shell — position:absolute menutup seluruh area termasuk Leaflet */
  .sidebar-overlay { display: none; position: absolute; inset: 0; background: rgba(10,10,10,.55); z-index: 1199; }

  /* ── MOBILE ── */
  @media (max-width: 768px) {
    .hamburger { display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .sidebar { position: fixed; top: 0; left: 0; bottom: 0; transform: translateX(-100%); z-index: 1200; }
    .sidebar.open { transform: translateX(0); box-shadow: 6px 0 20px rgba(0,0,0,.3); }
    .sidebar-overlay.open { display: block; }
    .topbar { padding: 0 12px; }
    .main-content-inner { padding: 14px 12px 28px !important; }
    .topbar-date { display: none; }
    .kpi-grid { grid-template-columns: repeat(2,1fr) !important; }
    .kpi-grid > div:nth-child(2n) { border-right: none !important; }
    .kpi-grid > div:nth-last-child(-n+2) { border-bottom: none !important; }
    .dash-two-col { grid-template-columns: 1fr !important; }
    .brut-table th, .brut-table td { padding: 8px 10px; font-size: 11px; }
    .page-title { font-size: 14px !important; }
    .flash-area { padding: 10px 12px 0 !important; }
    .toolbar-flex { flex-wrap: nowrap !important; overflow-x: auto; }
    .toolbar-flex form { flex-wrap: nowrap !important; min-width: 0; }
    .toolbar-flex form input { min-width: 100px !important; }
    .toolbar-flex form select { min-width: 80px !important; font-size: 11px !important; padding-left: 8px !important; padding-right: 8px !important; }
    .toolbar-flex form button[type=submit] { white-space: nowrap; }
    .toolbar-flex > a.btn { white-space: nowrap; flex-shrink: 0; }
  }
  @media (max-width: 480px) {
    .kpi-grid { grid-template-columns: 1fr 1fr !important; }
  }

  /* ── DETAIL / MODERASI TWO-COL ── */
  @media (max-width: 900px) {
    .detail-grid { grid-template-columns: 1fr !important; }
  }

  /* ── LAPORAN CHART TWO-COL ── */
  @media (max-width: 768px) {
    .dash-two-col { grid-template-columns: 1fr !important; }
  }

  /* ── TABLE COLUMN HIDING ── */
  @media (max-width: 900px) {
    .hide-md { display: none !important; }
  }
  @media (max-width: 640px) {
    .hide-sm { display: none !important; }
    .show-sm { display: block !important; }
  }
  @media (max-width: 480px) {
    .hide-xs { display: none !important; }
  }

  /* ── PENGELOLA LOKASI CARD GRID ── */
  @media (max-width: 900px) {
    .lokasi-grid { grid-template-columns: repeat(2,1fr) !important; gap: 14px !important; }
  }
  @media (max-width: 480px) {
    .lokasi-grid { grid-template-columns: 1fr !important; }
  }

  /* ── PENGELOLA DASHBOARD KPI (4 col) ── */
  @media (max-width: 768px) {
    .kpi-grid[style*="repeat(4"] { grid-template-columns: repeat(2,1fr) !important; }
    .kpi-grid[style*="repeat(4"] > div { border-bottom: 3px solid #0a0a0a !important; }
    .kpi-grid[style*="repeat(4"] > div:nth-child(2n) { border-right: none !important; }
    .kpi-grid[style*="repeat(4"] > div:nth-last-child(-n+2) { border-bottom: none !important; }
  }
</style>
@stack('styles')
</head>
<body>

<div class="app-shell">

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
  <aside class="sidebar" id="sidebar">
    <div style="padding:20px 20px 16px;border-bottom:3px solid #0a0a0a;background:#0a0a0a;">
      <div class="font-display" style="font-size:22px;font-weight:800;color:#FFE135;letter-spacing:-1px;line-height:1;">
        &nbspNUSANTARA &nbsp<span style="color:#fff;">MAP</span>
      </div>
      <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
        <span class="badge badge-{{ auth()->user()->role }}">{{ strtoupper(auth()->user()->role) }}</span>
        <span style="font-size:10px;color:#888;font-family:'Space Mono',monospace;">PANEL v2</span>
      </div>
    </div>

    <nav style="flex:1;padding:12px;overflow-y:auto;">
      @if(auth()->user()->isAdmin())
      <div style="font-size:9px;font-weight:700;letter-spacing:2px;color:#888;padding:8px 14px 4px;font-family:'Space Mono',monospace;">// ADMIN</div>
      <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">▪ Dashboard</a>
      <a href="{{ route('admin.users.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">▪ Manajemen User</a>
      <a href="{{ route('admin.locations.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">▪ Semua Lokasi</a>
      <a href="{{ route('admin.moderasi.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.moderasi.*') ? 'active' : '' }}">
        ▪ Moderasi
        @php $pendingCount = \App\Models\Location::pending()->count() @endphp
        @if($pendingCount)
          <span style="margin-left:auto;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border:2px solid var(--black);">{{ $pendingCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.laporan.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">▪ Laporan &amp; Export</a>
      <a href="{{ route('admin.peta') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('admin.peta') ? 'active' : '' }}">▪ Peta Interaktif</a>
      @endif

      @if(auth()->user()->isPengelola() && !auth()->user()->isAdmin())
      <div style="font-size:9px;font-weight:700;letter-spacing:2px;color:#888;padding:8px 14px 4px;font-family:'Space Mono',monospace;">// PENGELOLA</div>
      <a href="{{ route('pengelola.dashboard') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('pengelola.dashboard') ? 'active' : '' }}">▪ Dashboard</a>
      <a href="{{ route('pengelola.lokasi.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('pengelola.lokasi.*') ? 'active' : '' }}">▪ Kelola Lokasi</a>
      <a href="{{ route('pengelola.moderasi.index') }}" onclick="closeSidebar()" class="nav-link {{ request()->routeIs('pengelola.moderasi.*') ? 'active' : '' }}">▪ Antrian Moderasi</a>
      @endif
    </nav>

    <div style="border-top:3px solid #0a0a0a;padding:14px 16px;background:#0a0a0a;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:36px;height:36px;background:var(--yellow);border:2px solid #555;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--black);flex-shrink:0;font-family:'Space Mono',monospace;">
          {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:12px;font-weight:700;color:#f5f0e8;font-family:'Space Mono',monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->nama }}</div>
          <div style="font-size:10px;color:#888;font-family:'Space Mono',monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" style="background:none;border:none;cursor:pointer;padding:4px;" title="Logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#FFE135" stroke-width="2.5">
              <path stroke-linecap="square" stroke-linejoin="miter" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </aside>

  {{-- ── MAIN ─────────────────────────────────────────────────── --}}
  <div class="main-area">
    <header class="topbar">
      <div style="display:flex;align-items:center;">
        <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0a0a0a" stroke-width="2.5" stroke-linecap="square">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <h1 class="font-display page-title" style="font-size:17px;font-weight:800;letter-spacing:-0.5px;color:var(--black);margin:0;">
          @yield('page-title', 'DASHBOARD')
        </h1>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span class="topbar-date" style="font-family:'Space Mono',monospace;font-size:11px;font-weight:700;color:var(--black);opacity:.6;">
          {{ now()->isoFormat('ddd, D MMM YYYY') }}
        </span>
        <span style="width:10px;height:10px;background:var(--green);border:2px solid var(--black);display:inline-block;" title="Online"></span>
      </div>
    </header>

    <div class="flash-area" style="padding:16px 24px 0;">
      @if(session('success'))
        <div class="flash-success">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="flash-error">✗ {{ session('error') }}</div>
      @endif
    </div>

    <main class="main-content-inner" style="flex:1;overflow-y:auto;padding:20px 24px 32px;">
      @yield('content')
    </main>
  </div>
</div>

@stack('scripts')

{{-- Confirm Modal --}}
<div id="brut-modal-overlay" onclick="brutModalCancel()" style="display:none;position:fixed;inset:0;background:rgba(10,10,10,.55);z-index:9998;backdrop-filter:blur(1px);"></div>
<div id="brut-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;width:420px;max-width:92vw;background:var(--white);border:3px solid var(--black);box-shadow:8px 8px 0 var(--black);">
  <div id="brut-modal-header" style="background:var(--red);border-bottom:3px solid var(--black);padding:14px 18px;display:flex;align-items:center;gap:10px;">
    <span style="font-size:20px;" id="brut-modal-icon">⚠</span>
    <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#fff;" id="brut-modal-title">KONFIRMASI</span>
  </div>
  <div style="padding:22px 20px;">
    <p id="brut-modal-body" style="font-size:13px;line-height:1.7;color:var(--black);font-family:'Space Mono',monospace;margin-bottom:6px;"></p>
    <p style="font-size:11px;color:#888;font-family:'Space Mono',monospace;">Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
  </div>
  <div style="display:flex;border-top:3px solid var(--black);">
    <button onclick="brutModalCancel()" style="flex:1;padding:14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;background:var(--white);border:none;border-right:3px solid var(--black);cursor:pointer;letter-spacing:.5px;" onmouseover="this.style.background='#e8e4d8'" onmouseout="this.style.background='var(--white)'">BATAL</button>
    <button id="brut-modal-confirm" onclick="brutModalConfirm()" style="flex:1;padding:14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;background:var(--red);color:#fff;border:none;cursor:pointer;letter-spacing:.5px;" onmouseover="this.style.background='#cc2a20'" onmouseout="this.style.background='var(--red)'">YA, HAPUS</button>
  </div>
</div>

<script>
function toggleSidebar() {
  const s = document.getElementById('sidebar');
  const o = document.getElementById('sidebarOverlay');
  const isOpen = s.classList.contains('open');
  if (isOpen) {
    s.classList.remove('open');
    o.classList.remove('open');
    document.body.style.overflow = '';
  } else {
    s.classList.add('open');
    o.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  // Invalidate Leaflet map size after sidebar transition (if any map on page)
  setTimeout(function() {
    if (window._leafletMap) { window._leafletMap.invalidateSize(); }
    // also support multiple maps stored in window._leafletMaps array
    if (window._leafletMaps && Array.isArray(window._leafletMaps)) {
      window._leafletMaps.forEach(function(m) { try { m.invalidateSize(); } catch(e){} });
    }
  }, 260);
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('open');
  document.body.style.overflow = '';
  setTimeout(function() {
    if (window._leafletMap) { window._leafletMap.invalidateSize(); }
    if (window._leafletMaps && Array.isArray(window._leafletMaps)) {
      window._leafletMaps.forEach(function(m) { try { m.invalidateSize(); } catch(e){} });
    }
  }, 260);
}

let _brutPendingForm = null;
function brutConfirm(formEl, title, body, icon) {
  _brutPendingForm = formEl;
  document.getElementById('brut-modal-title').textContent = title || 'KONFIRMASI HAPUS';
  document.getElementById('brut-modal-body').textContent  = body  || 'Yakin ingin menghapus data ini?';
  document.getElementById('brut-modal-icon').textContent  = icon  || '🗑';
  document.getElementById('brut-modal-overlay').style.display = 'block';
  document.getElementById('brut-modal').style.display         = 'block';
  document.body.style.overflow = 'hidden';
}
function brutModalConfirm() { if (_brutPendingForm) _brutPendingForm.submit(); brutModalClose(); }
function brutModalCancel()  { brutModalClose(); }
function brutModalClose() {
  document.getElementById('brut-modal-overlay').style.display = 'none';
  document.getElementById('brut-modal').style.display         = 'none';
  document.body.style.overflow = '';
  _brutPendingForm = null;
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') brutModalClose(); });
</script>
</body>
</html>