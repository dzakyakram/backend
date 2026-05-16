@extends('layouts.app')
@section('title', 'Moderasi Konten')
@section('page-title', 'MODERASI KONTEN')

@section('content')
<div class="detail-grid" style="display:grid;grid-template-columns:1fr 340px;gap:20px;">

  {{-- Antrian Pending --}}
  <div class="brut-card" style="overflow:hidden;">
    <div style="background:var(--red);padding:14px 18px;border-bottom:3px solid var(--black);display:flex;align-items:center;justify-content:space-between;">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#fff;">MENUNGGU VALIDASI GPS</span>
      <span class="badge" style="background:#fff;color:var(--red);">{{ $pending->total() }} PENDING</span>
    </div>

    @forelse($pending as $loc)
    <div style="padding:20px;border-bottom:3px solid var(--black);" id="loc-{{ $loc->id }}">
      <div style="display:flex;gap:16px;">
        {{-- Foto --}}
        <div style="width:88px;height:88px;background:#e8e4d8;border:3px solid var(--black);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:32px;">
          @if($loc->foto_url)
            <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $loc->kategori === 'wisata' ? '🏔' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
          @endif
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:6px;">
            <div style="min-width:0;">
              <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800;letter-spacing:-0.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ strtoupper($loc->nama) }}</div>
              <div style="font-size:11px;color:#666;margin-top:2px;">&#9679; {{ $loc->alamat }}</div>
            </div>
            <span class="badge" style="{{ $loc->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($loc->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;color:#0a0a0a;') }}flex-shrink:0;">
              {{ strtoupper($loc->kategori) }}
            </span>
          </div>

          <p style="font-size:12px;color:#555;line-height:1.6;border-left:3px solid var(--black);padding-left:10px;margin-bottom:10px;">{{ Str::limit($loc->deskripsi, 120) }}</p>

          <div style="display:flex;gap:12px;font-size:11px;color:#888;flex-wrap:wrap;margin-bottom:12px;">
            <span>&#128100; {{ $loc->user->nama ?? '–' }}</span>
            <code style="background:#e8e4d8;border:2px solid var(--black);padding:2px 8px;font-family:'Space Mono',monospace;font-size:10px;">
              {{ number_format($loc->latitude,6) }}, {{ number_format($loc->longitude,6) }}
            </code>
            <span>&#128336; {{ $loc->created_at->diffForHumans() }}</span>
          </div>

          {{-- Actions --}}
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <form method="POST" action="{{ route('admin.moderasi.approve', $loc->id) }}">
              @csrf @method('PATCH')
              <button class="btn btn-green btn-sm">✓ SETUJUI</button>
            </form>
            <button onclick="toggleReject({{ $loc->id }})" class="btn btn-red btn-sm">✗ TOLAK</button>
            <a href="{{ route('admin.locations.show', $loc->id) }}" class="btn btn-ghost btn-sm">&#128269; DETAIL</a>
          </div>

          {{-- Reject form --}}
          <div id="reject-form-{{ $loc->id }}" style="display:none;margin-top:12px;border:3px solid var(--red);padding:12px;background:#fff5f5;">
            <form method="POST" action="{{ route('admin.moderasi.reject', $loc->id) }}">
              @csrf @method('PATCH')
              <label style="font-size:10px;font-weight:700;letter-spacing:2px;display:block;margin-bottom:6px;">ALASAN PENOLAKAN</label>
              <textarea name="catatan" rows="2" placeholder="Tulis alasan penolakan (opsional)..."
                style="width:100%;font-family:'Space Mono',monospace;font-size:12px;border:2px solid var(--black);padding:8px;resize:none;outline:none;background:#fff;"></textarea>
              <div style="display:flex;gap:6px;margin-top:8px;">
                <button type="submit" class="btn btn-red btn-sm">KONFIRMASI TOLAK</button>
                <button type="button" onclick="toggleReject({{ $loc->id }})" class="btn btn-ghost btn-sm">BATAL</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div style="padding:60px;text-align:center;">
      <div style="font-size:48px;margin-bottom:12px;">&#10003;</div>
      <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;">SEMUA BERSIH!</div>
      <div style="font-size:12px;color:#888;margin-top:4px;">Tidak ada lokasi yang menunggu validasi.</div>
    </div>
    @endforelse

    <div style="padding:14px 18px;border-top:3px solid var(--black);">{{ $pending->links() }}</div>
  </div>

  {{-- Riwayat Moderasi --}}
  <div class="brut-card" style="overflow:hidden;align-self:start;">
    <div style="background:var(--black);padding:14px 18px;border-bottom:3px solid var(--black);">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--yellow);">RIWAYAT MODERASI</span>
    </div>
    <div style="max-height:600px;overflow-y:auto;">
      @foreach($riwayat as $r)
      <div style="padding:14px 16px;border-bottom:2px solid var(--black);display:flex;align-items:center;gap:10px;">
        <span class="badge badge-{{ $r->status }}" style="flex-shrink:0;">{{ $r->status === 'approved' ? '✓' : '✗' }}</span>
        <div style="flex:1;min-width:0;">
          <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $r->nama }}</div>
          <div style="font-size:10px;color:#888;margin-top:2px;">oleh {{ $r->moderator->nama ?? '–' }} · {{ $r->dimoderasi_at?->diffForHumans() }}</div>
          @if($r->catatan_moderasi)
            <div style="font-size:10px;color:var(--red);margin-top:2px;font-style:italic;">{{ $r->catatan_moderasi }}</div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleReject(id) {
  const el = document.getElementById('reject-form-' + id);
  el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush
