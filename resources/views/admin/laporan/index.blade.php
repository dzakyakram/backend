@extends('layouts.app')
@section('title', 'Laporan & Export')
@section('page-title', 'LAPORAN & EXPORT DATA')

@section('content')

{{-- Charts --}}
<div class="dash-two-col" style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:20px;">

  {{-- Pie chart --}}
  <div class="brut-card" style="overflow:hidden;">
    <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);">DISTRIBUSI KATEGORI</span>
    </div>
    <div style="padding:20px;">
      <div style="position:relative;height:200px;">
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>

  {{-- Bar chart --}}
  <div class="brut-card" style="overflow:hidden;">
    <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);">UPLOAD PER BULAN</span>
    </div>
    <div style="padding:20px;">
      <div style="position:relative;height:200px;">
        <canvas id="barChart"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- KPI per kategori --}}
<div class="kpi-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin-bottom:20px;border:3px solid var(--black);box-shadow:var(--shadow-lg);">
  @php $colors = ['wisata'=>['#0047FF','#fff'],'kuliner'=>['#FF6B35','#fff'],'hotel'=>['#FFE135','#0a0a0a']] @endphp
  @foreach($stats['per_kategori'] as $kat => $total)
  @php $col = $colors[$kat] ?? ['#0a0a0a','#FFE135'] @endphp
  <div style="background:{{ $col[0] }};padding:24px;{{ !$loop->last ? 'border-right:3px solid var(--black);' : '' }}">
    <div style="font-size:40px;margin-bottom:6px;">{{ $kat === 'wisata' ? '🏔️' : ($kat === 'kuliner' ? '🍜' : '🏨') }}</div>
    <div style="font-family:'Syne',sans-serif;font-size:36px;font-weight:800;color:{{ $col[1] }};line-height:1;">{{ $total }}</div>
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;color:{{ $col[1] }};opacity:.8;margin-top:4px;">TOTAL {{ strtoupper($kat) }}</div>
  </div>
  @endforeach
</div>

{{-- Export --}}
<div class="brut-card-lg" style="overflow:hidden;">
  <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);">
    <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);">EXPORT DATA LOKASI</span>
  </div>
  <div style="padding:24px;">
    <p style="font-size:13px;color:#555;margin-bottom:6px;">
      Export semua (<strong>{{ $stats['total'] }}</strong>) lokasi terverifikasi ke format standar GIS.
    </p>
    <p style="font-size:12px;color:#888;margin-bottom:20px;border-left:3px solid var(--black);padding-left:10px;">
      Kompatibel dengan QGIS, ArcGIS, dan Google Earth.
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <a href="{{ route('admin.laporan.csv') }}" class="btn btn-green" style="font-size:12px;letter-spacing:1px;">
        &#128202; EXPORT CSV
      </a>
      <a href="{{ route('admin.laporan.geojson') }}" class="btn btn-blue" style="font-size:12px;letter-spacing:1px;">
        &#127758; EXPORT GEOJSON
      </a>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
      labels: @json(array_keys($stats['per_kategori']->toArray())),
      datasets: [{
        data: @json(array_values($stats['per_kategori']->toArray())),
        backgroundColor: ['#0047FF','#FF6B35','#FFE135'],
        borderWidth: 3, borderColor: '#0a0a0a',
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position:'bottom', labels:{ font:{family:'Space Mono',size:11,weight:'bold'}, boxWidth:14 } } }
    }
  });

  const months = @json($stats['per_bulan']->pluck('bulan'));
  const totals = @json($stats['per_bulan']->pluck('total'));
  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: months,
      datasets: [{ label:'Upload', data: totals,
        backgroundColor: '#FFE135', borderWidth: 2, borderColor: '#0a0a0a', borderRadius: 0 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { family: 'Space Mono', size: 10 } } },
        y: { ticks: { stepSize: 1, font: { family: 'Space Mono', size: 10 } } }
      }
    }
  });
});
</script>
@endpush
