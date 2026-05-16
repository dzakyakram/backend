@extends('layouts.app')
@section('title', 'Kelola Lokasi')
@section('page-title', 'KELOLA LOKASI')

@section('content')

{{-- Filter --}}
<div style="width:100%;margin-bottom:20px;border:3px solid var(--black);background:#fff;box-shadow:var(--shadow);overflow:hidden;">
  <form method="GET" style="display:flex;align-items:stretch;width:100%;flex-wrap:wrap;">
    <select name="status" style="flex:1;min-width:0;padding:12px 14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:var(--white);outline:none;appearance:none;cursor:pointer;">
      <option value="">SEMUA STATUS</option>
      <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>PENDING</option>
      <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>APPROVED</option>
      <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>REJECTED</option>
    </select>
    <select name="kategori" style="flex:1;min-width:0;padding:12px 14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:var(--white);outline:none;appearance:none;cursor:pointer;">
      <option value="">SEMUA KATEGORI</option>
      <option value="wisata">WISATA</option>
      <option value="kuliner">KULINER</option>
      <option value="hotel">HOTEL</option>
    </select>
    <button type="submit" class="btn btn-yellow" style="border:none;box-shadow:none;padding:0 24px;font-weight:700;white-space:nowrap;">FILTER</button>
  </form>
</div>

{{-- Grid kartu lokasi --}}
<div class="lokasi-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
  @forelse($locations as $loc)
  <div class="brut-card" style="overflow:hidden;">
    {{-- Foto --}}
    <div style="height:180px;background:#e8e4d8;position:relative;border-bottom:3px solid var(--black);">
      @if($loc->foto_url)
        <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
      @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:56px;">
          {{ $loc->kategori === 'wisata' ? '🏔' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
        </div>
      @endif
      <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;">
        <span class="badge badge-{{ $loc->status }}">{{ strtoupper($loc->status) }}</span>
      </div>
    </div>

    <div style="padding:16px;">
      <span class="badge" style="{{ $loc->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($loc->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;') }}margin-bottom:8px;display:inline-block;">
        {{ strtoupper($loc->kategori) }}
      </span>
      <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:800;margin-top:6px;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ strtoupper($loc->nama) }}</div>
      <div style="font-size:11px;color:#666;margin-bottom:4px;">&#9679; {{ Str::limit($loc->alamat, 40) }}</div>
      <div style="font-size:11px;color:#888;margin-bottom:8px;">&#128100; {{ $loc->user->nama ?? '–' }} · {{ $loc->created_at->diffForHumans() }}</div>
      <code style="font-family:'Space Mono',monospace;font-size:10px;background:#e8e4d8;border:2px solid var(--black);padding:3px 8px;display:block;margin-bottom:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
        {{ number_format($loc->latitude,6) }}, {{ number_format($loc->longitude,6) }}
      </code>

      <div style="display:flex;gap:8px;">
        <a href="{{ route('pengelola.lokasi.show', $loc->id) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">DETAIL</a>
        @if($loc->status === 'pending')
        <form method="POST" action="{{ route('pengelola.lokasi.approve', $loc->id) }}" style="flex:1;">
          @csrf @method('PATCH')
          <button class="btn btn-green btn-sm" style="width:100%;justify-content:center;">✓ SETUJUI</button>
        </form>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div style="grid-column:span 3;padding:60px;text-align:center;">
    <div style="font-size:48px;margin-bottom:12px;">&#128237;</div>
    <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;">TIDAK ADA LOKASI</div>
    <div style="font-size:12px;color:#888;margin-top:4px;">Tidak ada lokasi yang ditemukan.</div>
  </div>
  @endforelse
</div>

<div style="margin-top:20px;">{{ $locations->links() }}</div>
@endsection

@push('styles')
<style>
  @media (max-width: 768px) {
    .lokasi-grid { grid-template-columns: repeat(2,1fr) !important; gap: 14px !important; }
    .lokasi-grid > div:last-child[style*="span 3"] { grid-column: span 2 !important; }
  }
  @media (max-width: 480px) {
    .lokasi-grid { grid-template-columns: 1fr !important; }
    .lokasi-grid > div:last-child[style*="span 3"] { grid-column: span 1 !important; }
  }
</style>
@endpush
