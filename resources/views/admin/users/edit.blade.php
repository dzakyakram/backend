@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'EDIT USER')

@section('content')
<div style="max-width:560px;">

  <a href="{{ route('admin.users.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:1px;color:var(--black);text-decoration:none;margin-bottom:20px;">
    ← KEMBALI KE DAFTAR
  </a>

  {{-- User identity strip --}}
  <div style="background:var(--black);border:3px solid var(--black);padding:16px 20px;margin-bottom:0;display:flex;align-items:center;gap:14px;">
    <div style="width:44px;height:44px;background:var(--yellow);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;color:var(--black);border:2px solid #555;flex-shrink:0;">
      {{ strtoupper(substr($user->nama, 0, 2)) }}
    </div>
    <div>
      <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:var(--yellow);">{{ strtoupper($user->nama) }}</div>
      <div style="font-size:11px;color:#888;font-family:'Space Mono',monospace;">{{ $user->email }}</div>
    </div>
    <div style="margin-left:auto;">
      <span class="badge badge-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
    </div>
  </div>

  <div style="border:3px solid var(--black);border-top:none;box-shadow:6px 6px 0 var(--black);">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" style="padding:24px;display:grid;gap:18px;">
      @csrf
      @method('PUT')

      {{-- Nama --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">NAMA LENGKAP *</label>
        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="brut-input"
               style="{{ $errors->has('nama') ? 'border-color:var(--red);' : '' }}">
        @error('nama')<div style="font-size:11px;color:var(--red);font-weight:700;margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>@enderror
      </div>

      {{-- Email --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">EMAIL *</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="brut-input"
               style="{{ $errors->has('email') ? 'border-color:var(--red);' : '' }}">
        @error('email')<div style="font-size:11px;color:var(--red);font-weight:700;margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>@enderror
      </div>

      {{-- Role --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">ROLE *</label>
        @if($user->id === auth()->id())
          <div style="padding:10px 14px;border:3px solid #ccc;background:#f5f5f5;font-size:13px;font-weight:700;color:#888;">
            {{ strtoupper($user->role) }} <span style="font-size:10px;font-weight:400;">(tidak dapat diubah)</span>
          </div>
          <input type="hidden" name="role" value="{{ $user->role }}">
        @else
          <div style="position:relative;">
            <select name="role" required class="brut-input brut-select">
              <option value="admin"     {{ old('role', $user->role) === 'admin'     ? 'selected' : '' }}>ADMIN</option>
              <option value="pengelola" {{ old('role', $user->role) === 'pengelola' ? 'selected' : '' }}>PENGELOLA</option>
              <option value="pengguna"  {{ old('role', $user->role) === 'pengguna'  ? 'selected' : '' }}>PENGGUNA</option>
            </select>
          </div>
        @endif
      </div>

      {{-- Password section --}}
      <div style="background:#f5f0e8;border:3px solid var(--black);padding:18px;display:grid;gap:16px;">
        <div style="font-size:9px;font-weight:700;letter-spacing:2px;color:#888;">// GANTI PASSWORD (OPSIONAL)</div>
        <div>
          <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">PASSWORD BARU</label>
          <input type="password" name="password" minlength="8"
                 placeholder="Kosongkan jika tidak ingin diubah" class="brut-input"
                 style="background:#fff;{{ $errors->has('password') ? 'border-color:var(--red);' : '' }}">
          @error('password')<div style="font-size:11px;color:var(--red);font-weight:700;margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">KONFIRMASI PASSWORD BARU</label>
          <input type="password" name="password_confirmation"
                 placeholder="Ulangi password baru" class="brut-input" style="background:#fff;">
        </div>
      </div>

      {{-- Info strip --}}
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0;border:3px solid var(--black);">
        <div style="padding:12px;border-right:2px solid var(--black);text-align:center;">
          <div style="font-size:9px;color:#888;letter-spacing:1px;margin-bottom:3px;">STATUS</div>
          <span class="badge" style="{{ $user->status === 'aktif' ? 'background:#00C853;' : 'background:#ddd;' }}">{{ strtoupper($user->status) }}</span>
        </div>
        <div style="padding:12px;border-right:2px solid var(--black);text-align:center;">
          <div style="font-size:9px;color:#888;letter-spacing:1px;margin-bottom:3px;">UPLOAD</div>
          <div style="font-size:20px;font-weight:800;font-family:'Syne',sans-serif;">{{ $user->locations()->count() }}</div>
        </div>
        <div style="padding:12px;text-align:center;">
          <div style="font-size:9px;color:#888;letter-spacing:1px;margin-bottom:3px;">BERGABUNG</div>
          <div style="font-size:11px;font-weight:700;">{{ $user->created_at->format('d M Y') }}</div>
        </div>
      </div>

      <div style="display:flex;gap:10px;padding-top:8px;border-top:3px solid var(--black);">
        <button type="submit" class="btn btn-black" style="font-size:12px;letter-spacing:1px;padding:12px 24px;">✓ SIMPAN PERUBAHAN</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" style="font-size:12px;letter-spacing:1px;">BATAL</a>
      </div>
    </form>
  </div>
</div>
@endsection