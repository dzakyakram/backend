@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'TAMBAH USER BARU')

@section('content')
<div style="max-width:560px;">

  {{-- Back --}}
  <a href="{{ route('admin.users.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:1px;color:var(--black);text-decoration:none;margin-bottom:20px;">
    ← KEMBALI KE DAFTAR
  </a>

  <div class="brut-card-lg" style="overflow:hidden;">
    <div style="background:var(--black);padding:16px 20px;border-bottom:3px solid var(--black);">
      <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:var(--yellow);letter-spacing:0;">FORM TAMBAH USER</div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" style="padding:24px;display:grid;gap:20px;">
      @csrf

      {{-- Nama --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
          NAMA LENGKAP <span style="color:var(--red);">*</span>
        </label>
        <input type="text" name="nama" value="{{ old('nama') }}" required
               placeholder="Masukkan nama lengkap"
               class="brut-input @error('nama') error @enderror"
               style="{{ $errors->has('nama') ? 'border-color:var(--red);background:#fff5f5;' : '' }}">
        @error('nama')
          <div style="font-size:11px;font-weight:700;color:var(--red);margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>
        @enderror
      </div>

      {{-- Email --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
          EMAIL <span style="color:var(--red);">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email') }}" required
               placeholder="email@contoh.com"
               class="brut-input"
               style="{{ $errors->has('email') ? 'border-color:var(--red);background:#fff5f5;' : '' }}">
        @error('email')
          <div style="font-size:11px;font-weight:700;color:var(--red);margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>
        @enderror
      </div>

      {{-- Role --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
          ROLE <span style="color:var(--red);">*</span>
        </label>
        <div style="position:relative;">
          <select name="role" required class="brut-input brut-select"
                  style="{{ $errors->has('role') ? 'border-color:var(--red);' : '' }}">
            <option value="">-- PILIH ROLE --</option>
            <option value="admin"     {{ old('role') === 'admin'     ? 'selected' : '' }}>ADMIN</option>
            <option value="pengelola" {{ old('role') === 'pengelola' ? 'selected' : '' }}>PENGELOLA</option>
            <option value="pengguna"  {{ old('role') === 'pengguna'  ? 'selected' : '' }}>PENGGUNA</option>
          </select>
        </div>
        @error('role')
          <div style="font-size:11px;font-weight:700;color:var(--red);margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>
        @enderror
      </div>

      {{-- Divider --}}
      <div style="border-top:2px solid var(--black);padding-top:4px;">
        <div style="font-size:9px;font-weight:700;letter-spacing:2px;color:#888;margin-bottom:16px;">// PASSWORD</div>
      </div>

      {{-- Password --}}
      <div style="margin-top:-16px;">
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
          PASSWORD <span style="color:var(--red);">*</span>
        </label>
        <input type="password" name="password" required minlength="8"
               placeholder="Min. 8 karakter"
               class="brut-input"
               style="{{ $errors->has('password') ? 'border-color:var(--red);background:#fff5f5;' : '' }}">
        @error('password')
          <div style="font-size:11px;font-weight:700;color:var(--red);margin-top:4px;border-left:3px solid var(--red);padding-left:8px;">{{ $message }}</div>
        @enderror
      </div>

      {{-- Konfirmasi --}}
      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
          KONFIRMASI PASSWORD <span style="color:var(--red);">*</span>
        </label>
        <input type="password" name="password_confirmation" required
               placeholder="Ulangi password"
               class="brut-input">
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:10px;padding-top:8px;border-top:3px solid var(--black);margin-top:4px;">
        <button type="submit" class="btn btn-black" style="font-size:12px;letter-spacing:1px;padding:12px 24px;">
          ✓ SIMPAN USER
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" style="font-size:12px;letter-spacing:1px;">BATAL</a>
      </div>
    </form>
  </div>
</div>
@endsection