@extends('layouts.app')
@section('title', 'Semua Lokasi')
@section('page-title', 'SEMUA LOKASI')

@section('content')
{{-- Filter toolbar --}}
<div class="toolbar-flex" style="display:flex;align-items:stretch;gap:0;margin-bottom:20px;border:3px solid var(--black);box-shadow:var(--shadow-lg);flex-wrap:wrap;">
  <form method="GET" style="display:flex;flex:1;align-items:stretch;min-width:0;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="CARI NAMA LOKASI..."
           style="flex:1;min-width:140px;padding:12px 16px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);border-bottom:0;background:#fff;outline:none;text-transform:uppercase;letter-spacing:.5px;">
    <select name="kategori"
            style="padding:12px 14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:var(--white);outline:none;appearance:none;cursor:pointer;min-width:0;flex:1;">
      <option value="">SEMUA KATEGORI</option>
      <option value="wisata"  {{ request('kategori') === 'wisata'  ? 'selected' : '' }}>WISATA</option>
      <option value="kuliner" {{ request('kategori') === 'kuliner' ? 'selected' : '' }}>KULINER</option>
      <option value="hotel"   {{ request('kategori') === 'hotel'   ? 'selected' : '' }}>HOTEL</option>
    </select>
    <select name="status"
            style="padding:12px 14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:var(--white);outline:none;appearance:none;cursor:pointer;min-width:0;flex:1;">
      <option value="">SEMUA STATUS</option>
      <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>PENDING</option>
      <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>APPROVED</option>
      <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>REJECTED</option>
    </select>
    <button type="submit" class="btn btn-yellow" style="border:none;border-right:3px solid var(--black);box-shadow:none;white-space:nowrap;">FILTER</button>
  </form>
</div>

<div class="brut-card" style="overflow:hidden;">
  <div style="overflow-x:auto;">
    <table class="brut-table">
      <thead>
        <tr>
          <th class="hide-sm">FOTO</th>
          <th>NAMA</th>
          <th>KATEGORI</th>
          <th class="hide-md">KOORDINAT GPS</th>
          <th class="hide-sm">UPLOADER</th>
          <th>STATUS</th>
          <th class="hide-md">TANGGAL</th>
          <th>AKSI</th>
        </tr>
      </thead>
      <tbody>
        @foreach($locations as $loc)
        <tr>
          <td class="hide-sm">
            <div style="width:48px;height:48px;border:2px solid var(--black);overflow:hidden;background:#e8e4d8;display:flex;align-items:center;justify-content:center;font-size:20px;">
              @if($loc->foto_url)
                <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
              @else
                {{ $loc->kategori === 'wisata' ? '🏔️' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
              @endif
            </div>
          </td>
          <td style="font-weight:700;">{{ $loc->nama }}</td>
          <td>
            <span class="badge" style="{{ $loc->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($loc->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;') }}">
              {{ strtoupper($loc->kategori) }}
            </span>
          </td>
          <td class="hide-md">
            <code style="font-family:'Space Mono',monospace;font-size:10px;background:#e8e4d8;border:2px solid var(--black);padding:3px 8px;display:inline-block;white-space:nowrap;">
              {{ number_format($loc->latitude,5) }}, {{ number_format($loc->longitude,5) }}
            </code>
          </td>
          <td class="hide-sm" style="font-size:12px;color:#666;">{{ $loc->user->nama ?? '–' }}</td>
          <td><span class="badge badge-{{ $loc->status }}">{{ strtoupper($loc->status) }}</span></td>
          <td class="hide-md" style="font-size:11px;color:#888;">{{ $loc->created_at->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:4px;flex-wrap:wrap;">
              <a href="{{ route('admin.locations.show', $loc) }}" class="btn btn-ghost btn-sm">DETAIL</a>
              @if($loc->status === 'pending')
              <form method="POST" action="{{ route('admin.locations.approve', $loc) }}">
                @csrf @method('PATCH')
                <button class="btn btn-green btn-sm">✓</button>
              </form>
              <form method="POST" action="{{ route('admin.locations.reject', $loc) }}">
                @csrf @method('PATCH')
                <button class="btn btn-red btn-sm">✗</button>
              </form>
              @endif
              <form method="POST" action="{{ route('admin.locations.destroy', $loc) }}" id="del-loc-{{ $loc->id }}">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-red btn-sm"
                  onclick="brutConfirm(document.getElementById('del-loc-{{ $loc->id }}'),'HAPUS LOKASI','Hapus lokasi '+{{ Js::from($loc->nama) }}+' secara permanen?','📍')">&#128465;</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="padding:14px 18px;border-top:3px solid var(--black);">{{ $locations->links() }}</div>
</div>
@endsection
