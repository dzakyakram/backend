@extends('layouts.app')
@section('title', 'Antrian Moderasi')
@section('page-title', 'ANTRIAN MODERASI')

@section('content')
<div class="detail-grid" style="display:grid;grid-template-columns:1fr 300px;gap:20px;">

  {{-- Antrian --}}
  <div class="brut-card" style="overflow:hidden;">
    <div style="background:var(--red);padding:14px 18px;border-bottom:3px solid var(--black);display:flex;align-items:center;justify-content:space-between;">
      <span style="font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#fff;">MENUNGGU VALIDASI</span>
      @if($antrian->total())
        <span class="badge" style="background:#fff;color:var(--red);">{{ $antrian->total() }} PENDING</span>
      @else
        <span class="badge" style="background:var(--green);color:var(--black);">SEMUA BERSIH ✓</span>
      @endif
    </div>

    @forelse($antrian as $loc)
    <div style="padding:20px;border-bottom:3px solid var(--black);" id="loc-{{ $loc->id }}">
      <div style="display:flex;gap:16px;">
        <div style="width:80px;height:80px;background:#e8e4d8;border:3px solid var(--black);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:28px;">
          @if($loc->foto_url)
            <img src="{{ $loc->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $loc->kategori === 'wisata' ? '🏔' : ($loc->kategori === 'kuliner' ? '🍜' : '🏨') }}
          @endif
        </div>

        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:6px;">
            <div style="min-width:0;">
              <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ strtoupper($loc->nama) }}</div>
              <div style="font-size:11px;color:#666;margin-top:2px;">&#9679; {{ $loc->alamat ?? '–' }}</div>
            </div>
            <span class="badge" style="{{ $loc->kategori === 'wisata' ? 'background:#0047FF;color:#fff;' : ($loc->kategori === 'kuliner' ? 'background:#FF6B35;color:#fff;' : 'background:#FFE135;') }}flex-shrink:0;">
              {{ strtoupper($loc->kategori) }}
            </span>
          </div>

          @if($loc->deskripsi)
            <p style="font-size:12px;color:#555;border-left:3px solid var(--black);padding-left:10px;margin-bottom:10px;line-height:1.6;">{{ Str::limit($loc->deskripsi, 120) }}</p>
          @endif

          <div style="display:flex;gap:12px;font-size:11px;color:#888;flex-wrap:wrap;margin-bottom:12px;">
            <span>&#128100; {{ $loc->user->nama ?? '–' }}</span>
            <code style="background:#e8e4d8;border:2px solid var(--black);padding:2px 8px;font-family:'Space Mono',monospace;font-size:10px;">
              {{ number_format($loc->latitude, 5) }}, {{ number_format($loc->longitude, 5) }}
            </code>
            <span>&#128336; {{ $loc->created_at->diffForHumans() }}</span>
          </div>

          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <form method="POST" action="{{ route('pengelola.moderasi.process', $loc->id) }}">
              @csrf
              <input type="hidden" name="aksi" value="approve">
              <button class="btn btn-green btn-sm">✓ SETUJUI</button>
            </form>
            <button onclick="toggleReject({{ $loc->id }})" class="btn btn-red btn-sm">✗ TOLAK</button>
            <a href="{{ route('pengelola.lokasi.show', $loc->id) }}" class="btn btn-ghost btn-sm">&#128269; DETAIL</a>
          </div>

          <div id="reject-form-{{ $loc->id }}" style="display:none;margin-top:12px;border:3px solid var(--red);padding:12px;background:#fff5f5;">
            <form method="POST" action="{{ route('pengelola.moderasi.process', $loc->id) }}">
              @csrf
              <input type="hidden" name="aksi" value="reject">
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
      <div style="font-size:48px;margin-bottom:12px;">&#127881;</div>
      <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;">SEMUA SUDAH BERSIH!</div>
      <div style="font-size:12px;color:#888;margin-top:4px;">Tidak ada lokasi yang menunggu validasi saat ini.</div>
    </div>
    @endforelse

    @if($antrian->hasPages())
      <div style="padding:14px 18px;border-top:3px solid var(--black);">{{ $antrian->links() }}</div>
    @endif
  </div>

  {{-- Sidebar --}}
  <div style="display:grid;gap:16px;align-content:start;">

    {{-- Statistik --}}
    <div class="brut-card" style="overflow:hidden;">
      <div style="background:var(--black);padding:12px 16px;border-bottom:3px solid var(--black);">
        <span style="font-size:11px;font-weight:700;color:var(--yellow);letter-spacing:1px;">STATISTIK</span>
      </div>
      <div style="padding:16px;display:grid;gap:10px;">
        @php
        $stats_items = [
          ['label'=>'MENUNGGU VALIDASI', 'value'=>$antrian->total(), 'color'=>'#FFE135'],
          ['label'=>'SUDAH DISETUJUI',   'value'=>\App\Models\Location::approved()->count(), 'color'=>'#00C853'],
          ['label'=>'PERNAH DITOLAK',    'value'=>\App\Models\Location::where('status','rejected')->count(), 'color'=>'#FF3B30'],
          ['label'=>'SAYA MODERASI',     'value'=>\App\Models\Location::where('dimoderasi_oleh', auth()->id())->count(), 'color'=>'#0047FF'],
        ];
        @endphp
        @foreach($stats_items as $s)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;border:2px solid var(--black);background:{{ $s['color'] }}20;">
          <span style="font-size:10px;font-weight:700;letter-spacing:1px;color:#555;">{{ $s['label'] }}</span>
          <span style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:var(--black);">{{ $s['value'] }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Panduan --}}
    <div style="border:3px solid var(--black);box-shadow:var(--shadow);overflow:hidden;">
      <div style="background:var(--yellow);padding:12px 16px;border-bottom:3px solid var(--black);">
        <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:800;">// PANDUAN MODERASI</div>
      </div>
      <div style="padding:16px;display:grid;gap:10px;">
        <div style="display:flex;gap:10px;font-size:12px;">
          <span style="color:var(--green);font-weight:700;flex-shrink:0;">✓</span>
          <span><strong>SETUJUI</strong> jika data lengkap, koordinat akurat, foto representatif.</span>
        </div>
        <div style="display:flex;gap:10px;font-size:12px;">
          <span style="color:var(--red);font-weight:700;flex-shrink:0;">✗</span>
          <span><strong>TOLAK</strong> jika koordinat salah, foto tidak sesuai, atau info tidak lengkap.</span>
        </div>
        <div style="display:flex;gap:10px;font-size:12px;">
          <span style="color:var(--blue);font-weight:700;flex-shrink:0;">&#128269;</span>
          <span>Gunakan <strong>DETAIL</strong> untuk lihat peta sebelum memutuskan.</span>
        </div>
      </div>
    </div>

    {{-- Pintasan --}}
    <div class="brut-card" style="overflow:hidden;">
      <div style="background:var(--black);padding:12px 16px;border-bottom:3px solid var(--black);">
        <span style="font-size:11px;font-weight:700;color:var(--yellow);letter-spacing:1px;">PINTASAN</span>
      </div>
      <div style="padding:8px;">
        <a href="{{ route('pengelola.lokasi.index') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:2px solid transparent;text-decoration:none;color:var(--black);font-size:12px;font-weight:700;transition:background .1s;"
           onmouseover="this.style.background='var(--yellow)';this.style.borderColor='var(--black)'"
           onmouseout="this.style.background='transparent';this.style.borderColor='transparent'">
          <span>&#128205;</span> SEMUA LOKASI <span style="margin-left:auto;">→</span>
        </a>
        <a href="{{ route('pengelola.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:2px solid transparent;text-decoration:none;color:var(--black);font-size:12px;font-weight:700;transition:background .1s;"
           onmouseover="this.style.background='var(--yellow)';this.style.borderColor='var(--black)'"
           onmouseout="this.style.background='transparent';this.style.borderColor='transparent'">
          <span>&#128202;</span> DASHBOARD <span style="margin-left:auto;">→</span>
        </a>
      </div>
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
