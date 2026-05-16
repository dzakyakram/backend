@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'DASHBOARD')

@section('content')

{{-- KPI Cards --}}
<div class="kpi-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin-bottom:24px;border:3px solid var(--black);box-shadow:var(--shadow-lg);">
  @php
  $cards = [
    ['label'=>'WISATA',          'value'=>$stats['total_wisata'],   'bg'=>'#f5f0e8', 'accent'=>'#0047FF'],
    ['label'=>'KULINER',         'value'=>$stats['total_kuliner'],  'bg'=>'#FFE135', 'accent'=>'#0a0a0a'],
    ['label'=>'HOTEL',           'value'=>$stats['total_hotel'],    'bg'=>'#f5f0e8', 'accent'=>'#0a0a0a'],
    ['label'=>'PENDING',         'value'=>$stats['total_pending'],  'bg'=>'#FF3B30', 'accent'=>'#fff'],
    ['label'=>'PENGGUNA AKTIF',  'value'=>$stats['total_pengguna'], 'bg'=>'#00C853', 'accent'=>'#0a0a0a'],
    ['label'=>'TOTAL LOKASI',    'value'=>$stats['total_lokasi'],   'bg'=>'#0a0a0a', 'accent'=>'#FFE135'],
  ];
  @endphp

  @foreach($cards as $i => $card)
  <div style="background:{{ $card['bg'] }};padding:20px 22px;border-right:{{ ($i+1)%3===0 ? 'none' : '3px solid #0a0a0a' }};border-bottom:{{ $i<3 ? '3px solid #0a0a0a' : 'none' }};">
    <div style="font-family:'Syne',sans-serif;font-size:42px;font-weight:800;line-height:1;color:{{ $card['accent'] }};">
      {{ $card['value'] }}
    </div>
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;color:{{ $card['accent'] }};opacity:.7;margin-top:4px;">{{ $card['label'] }}</div>
  </div>
  @endforeach
</div>

{{-- Charts + Antrian --}}
<div class="dash-two-col" style="display:grid;grid-template-columns:1fr 360px;gap:20px;margin-bottom:20px;">

  {{-- Chart --}}
  <div class="brut-card" style="padding:0;overflow:hidden;">
    <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);letter-spacing:0;">UPLOAD 7 HARI TERAKHIR</span>
    </div>
    <div style="padding:20px;">
      <canvas id="uploadChart" height="100"></canvas>
    </div>
  </div>

  {{-- Antrian pending --}}
  <div class="brut-card" style="padding:0;overflow:hidden;">
    <div style="background:var(--red);padding:14px 18px;border-bottom:3px solid var(--black);display:flex;align-items:center;justify-content:space-between;">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#fff;">ANTRIAN MODERASI</span>
      <a href="{{ route('admin.moderasi.index') }}" style="font-size:10px;font-weight:700;color:#fff;text-decoration:none;border:2px solid rgba(255,255,255,.5);padding:3px 8px;">LIHAT SEMUA →</a>
    </div>
    <div style="overflow-y:auto;max-height:320px;">
      @forelse($pending as $loc)
      <div style="padding:12px 16px;border-bottom:2px solid #e0dbd0;display:flex;align-items:center;gap:10px;">
        <div style="width:40px;height:40px;background:#e0dbd0;border:2px solid var(--black);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:18px;">
          @if($loc->foto_url)
            <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $loc->kategori === 'wisata' ? '🏔' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
          @endif
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $loc->nama }}</div>
          <div style="font-size:10px;color:#888;">{{ $loc->user->nama ?? '–' }} · {{ $loc->created_at->diffForHumans() }}</div>
        </div>
        <div style="display:flex;gap:4px;flex-shrink:0;">
          <form method="POST" action="{{ route('admin.moderasi.approve', $loc->id) }}">
            @csrf @method('PATCH')
            <button class="btn btn-green btn-sm">✓</button>
          </form>
          <form method="POST" action="{{ route('admin.moderasi.reject', $loc->id) }}">
            @csrf @method('PATCH')
            <button class="btn btn-red btn-sm">✗</button>
          </form>
        </div>
      </div>
      @empty
      <div style="padding:40px;text-align:center;color:#888;font-size:12px;">
        <div style="font-size:32px;margin-bottom:8px;">✓</div>
        SEMUA SUDAH DIMODERASI
      </div>
      @endforelse
    </div>
  </div>
</div>

{{-- Aktivitas table --}}
<div class="brut-card" style="overflow:hidden;">
  <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);display:flex;align-items:center;justify-content:space-between;">
    <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);">AKTIVITAS TERBARU</span>
    <a href="{{ route('admin.locations.index') }}" style="font-size:10px;font-weight:700;color:var(--yellow);text-decoration:none;border:2px solid rgba(255,225,53,.3);padding:3px 8px;">SEMUA LOKASI →</a>
  </div>
  <div style="overflow-x:auto;">
    <table class="brut-table">
      <thead>
        <tr>
          <th>NAMA LOKASI</th>
          <th>KATEGORI</th>
          <th class="hide-xs">UPLOADER</th>
          <th>STATUS</th>
          <th class="hide-xs">WAKTU</th>
          <th>AKSI</th>
        </tr>
      </thead>
      <tbody>
        @foreach($aktivitas as $loc)
        <tr>
          <td style="font-weight:700;">{{ $loc->nama }}</td>
          <td>
            <span class="badge" style="{{ $loc->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($loc->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;color:#0a0a0a;') }}">
              {{ strtoupper($loc->kategori) }}
            </span>
          </td>
          <td class="hide-xs" style="color:#666;font-size:12px;">{{ $loc->user->nama ?? '–' }}</td>
          <td><span class="badge badge-{{ $loc->status }}">{{ strtoupper($loc->status) }}</span></td>
          <td class="hide-xs" style="color:#888;font-size:11px;">{{ $loc->created_at->diffForHumans() }}</td>
          <td>
            <a href="{{ route('admin.locations.show', $loc->id) }}" class="btn btn-ghost btn-sm">DETAIL</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const chartData = @json($uploadChart);
  const days = Object.keys(chartData);
  const kategoris = ['wisata', 'kuliner', 'hotel'];
  const colors = { wisata:'#0047FF', kuliner:'#FF6B35', hotel:'#FFE135' };

  const datasets = kategoris.map(kat => ({
    label: kat.charAt(0).toUpperCase() + kat.slice(1),
    data: days.map(d => {
      const found = (chartData[d] || []).find(r => r.kategori === kat);
      return found ? found.total : 0;
    }),
    backgroundColor: colors[kat],
    borderWidth: 2,
    borderColor: '#0a0a0a',
    borderRadius: 0,
  }));

  new Chart(document.getElementById('uploadChart'), {
    type: 'bar',
    data: { labels: days, datasets },
    options: {
      responsive: true, maintainAspectRatio: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { family: 'Space Mono', size: 11, weight: 'bold' }, boxWidth: 14 } }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { family: 'Space Mono', size: 10 } } },
        y: { grid: { color: 'rgba(0,0,0,.08)' }, ticks: { stepSize: 1, font: { family: 'Space Mono', size: 10 } } }
      }
    }
  });
});
</script>
@endpush
