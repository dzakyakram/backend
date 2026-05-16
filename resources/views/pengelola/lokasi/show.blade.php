@extends('layouts.app')
@section('title', 'Detail Lokasi')
@section('page-title', 'DETAIL LOKASI')

@section('content')

<a href="{{ route('pengelola.lokasi.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:1px;color:var(--black);text-decoration:none;margin-bottom:20px;">← SEMUA LOKASI</a>

<div class="detail-grid" style="display:grid;grid-template-columns:1fr 320px;gap:20px;">

  {{-- Kiri --}}
  <div style="display:grid;gap:20px;">

    {{-- Foto + Info --}}
    <div class="brut-card-lg" style="overflow:hidden;">
      <div style="height:260px;background:#e8e4d8;position:relative;border-bottom:3px solid var(--black);">
        @if($location->foto_url)
          <img src="{{ $location->foto_url }}" alt="{{ $location->nama }}" style="width:100%;height:100%;object-fit:cover;">
        @else
          <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
            <span style="font-size:64px;">{{ $location->kategori === 'wisata' ? '🏔' : ($location->kategori === 'kuliner' ? '🍜' : '🏨') }}</span>
            <span style="font-size:11px;font-weight:700;color:#888;letter-spacing:1px;">TIDAK ADA FOTO</span>
          </div>
        @endif
        <div style="position:absolute;top:12px;left:12px;display:flex;gap:6px;">
          <span class="badge badge-{{ $location->status }}">{{ strtoupper($location->status) }}</span>
          <span class="badge" style="{{ $location->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($location->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;') }}">
            {{ strtoupper($location->kategori) }}
          </span>
        </div>
      </div>
      <div style="padding:20px 22px;">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;letter-spacing:-0.5px;margin-bottom:6px;">{{ strtoupper($location->nama) }}</h2>
        <div style="font-size:12px;color:#666;margin-bottom:10px;">&#9679; {{ $location->alamat ?? '—' }}</div>
        @if($location->deskripsi)
          <p style="font-size:13px;color:#444;line-height:1.7;border-left:4px solid var(--black);padding-left:14px;">{{ $location->deskripsi }}</p>
        @endif
      </div>
    </div>

    {{-- Peta --}}
    <div class="brut-card" style="overflow:hidden;">
      <div style="background:var(--black);padding:12px 18px;border-bottom:3px solid var(--black);">
        <span style="font-family:'Syne',sans-serif;font-size:13px;font-weight:800;color:var(--yellow);">POSISI GPS</span>
      </div>
      <div id="map" style="height:260px;width:100%;"></div>
      <div style="padding:10px 16px;border-top:3px solid var(--black);background:#e8e4d8;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
        <code style="font-family:'Space Mono',monospace;font-size:11px;">
          {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}
        </code>
        <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" target="_blank"
           class="btn btn-ghost btn-sm">GOOGLE MAPS ↗</a>
      </div>
    </div>
  </div>

  {{-- Kanan / Sidebar --}}
  <div style="display:grid;gap:16px;align-content:start;">

    {{-- Moderasi action --}}
    @if($location->status === 'pending')
    <div style="border:3px solid var(--black);box-shadow:var(--shadow-lg);overflow:hidden;">
      <div style="background:var(--yellow);padding:12px 16px;border-bottom:3px solid var(--black);">
        <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:800;">⚡ PERLU TINDAKAN</div>
      </div>
      <div style="padding:16px;display:grid;gap:8px;">
        <form method="POST" action="{{ route('pengelola.moderasi.process', $location) }}">
          @csrf
          <input type="hidden" name="aksi" value="approve">
          <button class="btn btn-green" style="width:100%;justify-content:center;font-size:12px;padding:12px;">✓ SETUJUI LOKASI INI</button>
        </form>
        <button onclick="document.getElementById('rp').classList.toggle('hidden')"
                class="btn btn-red" style="width:100%;justify-content:center;font-size:12px;padding:12px;">✗ TOLAK LOKASI INI</button>
        <div id="rp" class="hidden" style="border:3px solid var(--red);padding:12px;background:#fff5f5;">
          <form method="POST" action="{{ route('pengelola.moderasi.process', $location) }}">
            @csrf
            <input type="hidden" name="aksi" value="reject">
            <textarea name="catatan" rows="3" placeholder="ALASAN PENOLAKAN..."
                      style="width:100%;font-family:'Space Mono',monospace;font-size:12px;border:2px solid var(--black);padding:8px;resize:none;outline:none;"></textarea>
            <div style="display:flex;gap:6px;margin-top:8px;">
              <button type="submit" class="btn btn-red btn-sm">KONFIRMASI TOLAK</button>
              <button type="button" onclick="document.getElementById('rp').classList.add('hidden')" class="btn btn-ghost btn-sm">BATAL</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif

    {{-- Uploader info --}}
    <div class="brut-card" style="overflow:hidden;">
      <div style="background:var(--black);padding:10px 16px;border-bottom:3px solid var(--black);">
        <span style="font-size:11px;font-weight:700;color:var(--yellow);letter-spacing:1px;">DIUNGGAH OLEH</span>
      </div>
      <div style="padding:16px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
          <div style="width:40px;height:40px;background:var(--black);color:var(--yellow);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;border:2px solid var(--black);flex-shrink:0;">
            {{ strtoupper(substr($location->user->nama ?? '?', 0, 2)) }}
          </div>
          <div>
            <div style="font-weight:700;font-size:13px;">{{ $location->user->nama ?? '–' }}</div>
            <div style="font-size:10px;color:#888;">{{ $location->user->email ?? '–' }}</div>
          </div>
        </div>
        <div style="display:grid;gap:6px;border-top:2px solid var(--black);padding-top:12px;font-size:11px;">
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#888;">DIUNGGAH</span>
            <span style="font-weight:700;">{{ $location->created_at->format('d M Y, H:i') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#888;">UPDATE</span>
            <span style="font-weight:700;">{{ $location->updated_at->diffForHumans() }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Moderasi info --}}
    @if($location->status !== 'pending')
    <div class="brut-card" style="overflow:hidden;">
      <div style="background:{{ $location->status === 'approved' ? 'var(--green)' : 'var(--red)' }};padding:10px 16px;border-bottom:3px solid var(--black);">
        <span style="font-size:11px;font-weight:700;letter-spacing:1px;color:{{ $location->status === 'approved' ? 'var(--black)' : '#fff' }};">
          {{ $location->status === 'approved' ? '✓ DISETUJUI' : '✗ DITOLAK' }}
        </span>
      </div>
      <div style="padding:16px;display:grid;gap:6px;font-size:11px;">
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#888;">OLEH</span>
          <span style="font-weight:700;">{{ $location->moderator->nama ?? '–' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#888;">WAKTU</span>
          <span style="font-weight:700;">{{ $location->dimoderasi_at?->format('d M Y') }}</span>
        </div>
        @if($location->catatan_moderasi)
        <div style="margin-top:6px;padding:10px;background:#fff5f5;border-left:4px solid var(--red);font-size:11px;color:var(--red);font-style:italic;">
          {{ $location->catatan_moderasi }}
        </div>
        @endif
      </div>
    </div>
    @endif

    {{-- Hapus --}}
    <div style="border:3px solid var(--black);padding:16px;">
      <div style="font-size:10px;font-weight:700;letter-spacing:1px;color:#888;margin-bottom:10px;">// ZONA BAHAYA</div>
      <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" id="del-peng-show">
        @csrf @method('DELETE')
        <button type="button" class="btn btn-red" style="width:100%;justify-content:center;font-size:11px;letter-spacing:1px;"
          onclick="brutConfirm(document.getElementById('del-peng-show'),'HAPUS LOKASI PERMANEN','Hapus '+{{ Js::from($location->nama) }}+' secara permanen? Tindakan ini tidak bisa dibatalkan.','🗑')">
          &#128465; HAPUS LOKASI PERMANEN
        </button>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const lat = {{ $location->latitude }}, lng = {{ $location->longitude }};
  const map = L.map('map').setView([lat, lng], 14);
  window._leafletMap = map;
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);
  const color = {wisata:'#0047FF',kuliner:'#FF6B35',hotel:'#FFE135'}['{{ $location->kategori }}']||'#888';
  const icon = L.divIcon({
    html: `<div style="width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);background:${color};border:3px solid #0a0a0a;box-shadow:3px 3px 0 rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;">
             <span style="transform:rotate(45deg);font-size:14px;">{{ $location->kategori === 'wisata' ? '🏔' : ($location->kategori === 'kuliner' ? '🍜' : '🏨') }}</span>
           </div>`,
    className:'', iconSize:[34,34], iconAnchor:[17,34]
  });
  setTimeout(() => { map.invalidateSize(); }, 200);
  L.marker([lat,lng],{icon}).bindPopup(`<strong>{{ $location->nama }}</strong>`).addTo(map).openPopup();
});
</script>
@endpush
