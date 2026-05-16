@extends('layouts.app')
@section('title', 'Dashboard Pengelola')
@section('page-title', 'DASHBOARD PENGELOLA')

@section('content')
{{-- Stats KPI --}}
<div class="kpi-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin-bottom:24px;border:3px solid var(--black);box-shadow:var(--shadow-lg);">
  @php
  $cards = [
    ['label'=>'MENUNGGU VALIDASI', 'value'=>$stats['pending'],  'bg'=>'#FFE135', 'accent'=>'#0a0a0a'],
    ['label'=>'SUDAH DISETUJUI',   'value'=>$stats['approved'], 'bg'=>'#00C853', 'accent'=>'#0a0a0a'],
    ['label'=>'DITOLAK',           'value'=>$stats['rejected'], 'bg'=>'#FF3B30', 'accent'=>'#fff'],
    ['label'=>'SAYA MODERASI',     'value'=>$stats['my_mod'],   'bg'=>'#0047FF', 'accent'=>'#fff'],
  ];
  @endphp
  @foreach($cards as $i => $card)
  <div style="background:{{ $card['bg'] }};padding:22px;{{ $i < 3 ? 'border-right:3px solid var(--black);' : '' }}border-bottom:0;">
    <div style="font-family:'Syne',sans-serif;font-size:36px;font-weight:800;line-height:1;color:{{ $card['accent'] }};">{{ $card['value'] }}</div>
    <div style="font-size:9px;font-weight:700;letter-spacing:1px;color:{{ $card['accent'] }};opacity:.75;margin-top:6px;">{{ $card['label'] }}</div>
  </div>
  @endforeach
</div>

{{-- Antrian --}}
<div class="brut-card" style="overflow:hidden;">
  <div style="background:var(--red);padding:14px 18px;border-bottom:3px solid var(--black);display:flex;align-items:center;justify-content:space-between;">
    <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#fff;">ANTRIAN PERLU DIVALIDASI</span>
    <a href="{{ route('pengelola.moderasi.index') }}" style="font-size:10px;font-weight:700;color:#fff;text-decoration:none;border:2px solid rgba(255,255,255,.5);padding:3px 10px;white-space:nowrap;">LIHAT SEMUA →</a>
  </div>

  @forelse($antrian as $loc)
  <div style="padding:16px 20px;border-bottom:2px solid var(--black);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <div style="width:64px;height:64px;background:#e8e4d8;border:3px solid var(--black);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:26px;">
      @if($loc->foto_url)
        <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
      @else
        {{ $loc->kategori === 'wisata' ? '🏔' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
      @endif
    </div>
    <div style="flex:1;min-width:180px;">
      <div style="font-weight:700;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $loc->nama }}</div>
      <div style="font-size:11px;color:#888;margin-top:2px;">&#128100; {{ $loc->user->nama ?? '–' }} · {{ $loc->created_at->diffForHumans() }}</div>
      <code style="font-family:'Space Mono',monospace;font-size:10px;background:#e8e4d8;border:2px solid var(--black);padding:2px 8px;display:inline-block;margin-top:4px;">
        {{ number_format($loc->latitude,5) }}, {{ number_format($loc->longitude,5) }}
      </code>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;">
      <form method="POST" action="{{ route('pengelola.lokasi.approve', $loc->id) }}">
        @csrf @method('PATCH')
        <button class="btn btn-green btn-sm">✓ SETUJUI</button>
      </form>
      <a href="{{ route('pengelola.lokasi.show', $loc->id) }}" class="btn btn-ghost btn-sm">DETAIL</a>
    </div>
  </div>
  @empty
  <div style="padding:60px;text-align:center;">
    <div style="font-size:48px;margin-bottom:12px;">&#127881;</div>
    <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;">SEMUA BERSIH!</div>
    <div style="font-size:12px;color:#888;margin-top:4px;">Tidak ada antrian moderasi saat ini.</div>
  </div>
  @endforelse
</div>
@endsection
