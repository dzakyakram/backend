@extends('layouts.app')
@section('title', 'Peta Interaktif')
@section('page-title', 'Peta Interaktif')

@push('styles')
<style>
  .peta-shell {
    display: flex;
    gap: 16px;
    height: calc(100vh - 9rem);
    min-height: 400px;
  }
  .peta-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: white;
    border: 3px solid var(--black);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .peta-map-wrap {
    flex: 1;
    border: 3px solid var(--black);
    overflow: hidden;
    position: relative;
    min-width: 0;
  }
  /* Map div harus punya height eksplisit */
  #map {
    width: 100%;
    height: 100%;
    min-height: 300px;
  }
  @media (max-width: 768px) {
    .peta-shell {
      flex-direction: column;
      height: auto;
      gap: 12px;
    }
    .peta-sidebar {
      width: 100%;
      flex-shrink: 0;
      overflow: hidden;
      max-height: 430px;
      display: flex;
      flex-direction: column;
    }
    .peta-sidebar > div:first-child {
      flex-shrink: 0;
    }
    .peta-sidebar > div:nth-child(2) {
      flex-shrink: 0;
    }
    .peta-sidebar > div:last-child {
      flex: 1;
      overflow-y: auto;
      min-height: 0;
    }
    .peta-map-wrap {
      height: 50vh;
      min-height: 320px;
      flex-shrink: 0;
    }
    #map {
      width: 100%;
      height: 100%;
      min-height: 320px;
    }
  }
</style>
@endpush

@section('content')
<div class="peta-shell">

  {{-- Sidebar filter --}}
  <div class="peta-sidebar">
    <div style="padding:12px;border-bottom:3px solid var(--black);background:var(--black);">
      <p style="font-size:9px;font-weight:700;color:var(--yellow);letter-spacing:2px;margin-bottom:8px;">// FILTER KATEGORI</p>
      <div style="display:grid;gap:4px;">
        <button onclick="filterMap('all')" id="btn-all"
                style="width:100%;text-align:left;padding:8px 10px;font-family:'Space Mono',monospace;font-size:11px;font-weight:700;border:2px solid #333;background:var(--yellow);color:var(--black);cursor:pointer;">
          📍 Semua Lokasi
        </button>
        <button onclick="filterMap('wisata')" id="btn-wisata"
                style="width:100%;text-align:left;padding:8px 10px;font-family:'Space Mono',monospace;font-size:11px;font-weight:700;border:2px solid #333;background:transparent;color:#aaa;cursor:pointer;">
          🏔️ Wisata
        </button>
        <button onclick="filterMap('kuliner')" id="btn-kuliner"
                style="width:100%;text-align:left;padding:8px 10px;font-family:'Space Mono',monospace;font-size:11px;font-weight:700;border:2px solid #333;background:transparent;color:#aaa;cursor:pointer;">
          🍜 Kuliner
        </button>
        <button onclick="filterMap('hotel')" id="btn-hotel"
                style="width:100%;text-align:left;padding:8px 10px;font-family:'Space Mono',monospace;font-size:11px;font-weight:700;border:2px solid #333;background:transparent;color:#aaa;cursor:pointer;">
          🏨 Hotel
        </button>
      </div>
    </div>

    <div style="padding:12px;border-bottom:3px solid var(--black);">
      <p style="font-size:9px;font-weight:700;color:#888;letter-spacing:2px;margin-bottom:8px;">// STATISTIK</p>
      <div style="display:grid;gap:4px;">
        <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;">
          <span style="color:#666;">🏔️ Wisata</span>
          <span style="font-weight:700;color:#0047FF;" id="cnt-wisata">–</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;">
          <span style="color:#666;">🍜 Kuliner</span>
          <span style="font-weight:700;color:#FF6B35;" id="cnt-kuliner">–</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;">
          <span style="color:#666;">🏨 Hotel</span>
          <span style="font-weight:700;color:#d4a843;" id="cnt-hotel">–</span>
        </div>
      </div>
    </div>

    <div style="flex:1;overflow-y:auto;padding:8px;">
      <p style="font-size:9px;font-weight:700;color:#888;letter-spacing:2px;padding:4px 4px 6px;">// DAFTAR LOKASI</p>
      <div id="loc-list"></div>
    </div>
  </div>

  {{-- Map --}}
  <div class="peta-map-wrap">
    <div id="map"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const allLocations = @json($locations);
let map, markers = [], activeFilter = 'all';
const colors = { wisata:'#0047FF', kuliner:'#FF6B35', hotel:'#FFE135' };
const btnColors = {
  all:     { active:'var(--yellow)', activeText:'var(--black)', inactive:'transparent', inactiveText:'#aaa' },
  wisata:  { active:'#0047FF',       activeText:'#fff',         inactive:'transparent', inactiveText:'#aaa' },
  kuliner: { active:'#FF6B35',       activeText:'#fff',         inactive:'transparent', inactiveText:'#aaa' },
  hotel:   { active:'#FFE135',       activeText:'var(--black)', inactive:'transparent', inactiveText:'#aaa' },
};

document.addEventListener('DOMContentLoaded', () => {
  map = L.map('map', { zoomControl: true }).setView([3.5952, 98.6722], 12);
  window._leafletMap = map;
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // Invalidate size setelah layout selesai — penting di mobile
  setTimeout(() => { map.invalidateSize(); }, 100);
  setTimeout(() => { map.invalidateSize(); }, 500);

  renderMarkers(allLocations);
  renderList(allLocations);
  updateCounts(allLocations);
});

function renderMarkers(locs) {
  markers.forEach(m => m.remove());
  markers = locs.map(loc => {
    const c = colors[loc.kategori] || '#888';
    const icon = L.divIcon({
      html: `<div style="width:32px;height:32px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);background:${c};border:3px solid #0a0a0a;display:flex;align-items:center;justify-content:center;box-shadow:2px 2px 0 rgba(0,0,0,.3)">
               <span style="transform:rotate(45deg);font-size:13px">${loc.kategori==='wisata'?'🏔️':loc.kategori==='kuliner'?'🍜':'🏨'}</span>
             </div>`,
      className: '', iconSize: [32,32], iconAnchor: [16,32]
    });
    const m = L.marker([loc.latitude, loc.longitude], { icon })
      .bindPopup(`
        <div style="min-width:180px">
          ${loc.foto_url ? `<img src="${loc.foto_url}" style="width:100%;height:90px;object-fit:cover;margin-bottom:8px">` : ''}
          <strong style="font-size:13px">${loc.nama}</strong><br>
          <small style="color:#888">📌 ${loc.alamat||'–'}</small><br>
          <a href="/admin/locations/${loc.id}" style="color:#0047FF;font-size:11px;font-weight:700">Lihat Detail →</a>
        </div>
      `)
      .addTo(map);
    return m;
  });
  if (locs.length) map.fitBounds(markers.map(m => m.getLatLng()), { padding: [30,30] });
}

function renderList(locs) {
  document.getElementById('loc-list').innerHTML = locs.map(l => `
    <button onclick="focusLoc(${l.latitude},${l.longitude})"
            style="width:100%;text-align:left;padding:8px 10px;border:2px solid transparent;background:transparent;cursor:pointer;font-family:'Space Mono',monospace;"
            onmouseover="this.style.background='var(--yellow)';this.style.borderColor='var(--black)'"
            onmouseout="this.style.background='transparent';this.style.borderColor='transparent'">
      <p style="font-size:11px;font-weight:700;color:var(--black);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;">${l.nama}</p>
      <p style="font-size:10px;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;">${l.alamat||'–'}</p>
    </button>`).join('');
}

function updateCounts(locs) {
  document.getElementById('cnt-wisata').textContent  = locs.filter(l=>l.kategori==='wisata').length;
  document.getElementById('cnt-kuliner').textContent = locs.filter(l=>l.kategori==='kuliner').length;
  document.getElementById('cnt-hotel').textContent   = locs.filter(l=>l.kategori==='hotel').length;
}

function filterMap(kat) {
  activeFilter = kat;
  ['all','wisata','kuliner','hotel'].forEach(k => {
    const btn = document.getElementById('btn-'+k);
    const isActive = k === kat;
    btn.style.background = isActive ? btnColors[k].active : btnColors[k].inactive;
    btn.style.color = isActive ? btnColors[k].activeText : btnColors[k].inactiveText;
    btn.style.borderColor = isActive ? 'var(--black)' : '#333';
  });
  const filtered = kat === 'all' ? allLocations : allLocations.filter(l => l.kategori === kat);
  renderMarkers(filtered);
  renderList(filtered);
  updateCounts(allLocations);
}

function focusLoc(lat, lng) {
  map.setView([lat, lng], 16, { animate: true });
}
</script>
@endpush
