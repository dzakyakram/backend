@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'MANAJEMEN USER')

@section('content')

{{-- Toolbar --}}
<div class="toolbar-flex" style="display:flex;align-items:stretch;gap:0;margin-bottom:20px;border:3px solid var(--black);box-shadow:var(--shadow-lg);flex-wrap:wrap;">
  <form method="GET" style="display:flex;flex:1;align-items:stretch;min-width:0;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="CARI NAMA / EMAIL..."
           style="flex:1;min-width:140px;padding:12px 16px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:#fff;outline:none;text-transform:uppercase;letter-spacing:.5px;">
    <select name="role"
            style="padding:12px 14px;font-family:'Space Mono',monospace;font-size:12px;font-weight:700;border:none;border-right:3px solid var(--black);background:var(--white);outline:none;appearance:none;cursor:pointer;flex:1;min-width:0;">
      <option value="">SEMUA ROLE</option>
      <option value="admin"     {{ request('role') === 'admin'     ? 'selected' : '' }}>ADMIN</option>
      <option value="pengelola" {{ request('role') === 'pengelola' ? 'selected' : '' }}>PENGELOLA</option>
      <option value="pengguna"  {{ request('role') === 'pengguna'  ? 'selected' : '' }}>PENGGUNA</option>
    </select>
    <button type="submit" class="btn btn-yellow" style="border:none;border-right:3px solid var(--black);box-shadow:none;white-space:nowrap;">FILTER</button>
  </form>
  <a href="{{ route('admin.users.create') }}" class="btn btn-black" style="box-shadow:none;border:none;font-size:12px;letter-spacing:1px;white-space:nowrap;">+ TAMBAH</a>
</div>

{{-- Table --}}
<div class="brut-card" style="overflow:hidden;">
  <div style="overflow-x:auto;">
    <table class="brut-table">
      <thead>
        <tr>
          <th class="hide-sm">#</th>
          <th>NAMA</th>
          <th class="hide-sm">EMAIL</th>
          <th>ROLE</th>
          <th class="hide-xs">UPLOAD</th>
          <th class="hide-xs">STATUS</th>
          <th class="hide-md">BERGABUNG</th>
          <th>AKSI</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td class="hide-sm" style="font-size:11px;color:#888;font-weight:700;">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:32px;height:32px;background:var(--black);color:var(--yellow);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;border:2px solid var(--black);">
                {{ strtoupper(substr($user->nama, 0, 2)) }}
              </div>
              <div style="min-width:0;">
                <div style="font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->nama }}</div>
                <div class="show-sm" style="font-size:10px;color:#666;display:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td class="hide-sm" style="font-size:12px;color:#555;">{{ $user->email }}</td>
          <td><span class="badge badge-{{ $user->role }}">{{ strtoupper($user->role) }}</span></td>
          <td class="hide-xs" style="font-weight:700;text-align:center;">{{ $user->locations_count }}</td>
          <td class="hide-xs">
            <span class="badge" style="{{ $user->status === 'aktif' ? 'background:#00C853;color:#0a0a0a;' : 'background:#ddd;color:#0a0a0a;' }}">
              {{ strtoupper($user->status) }}
            </span>
          </td>
          <td class="hide-md" style="font-size:11px;color:#888;">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:4px;flex-wrap:wrap;">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">EDIT</a>
              <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $user->status === 'aktif' ? 'btn-ghost' : 'btn-green' }}" style="font-size:10px;">
                  {{ $user->status === 'aktif' ? 'OFF' : 'ON' }}
                </button>
              </form>
              @if($user->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="del-user-{{ $user->id }}">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-red btn-sm"
                  onclick="brutConfirm(document.getElementById('del-user-{{ $user->id }}'),'HAPUS USER','Hapus akun '+{{ Js::from($user->nama) }}+' ({{ $user->email }}) secara permanen?','👤')">DEL</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="padding:14px 18px;border-top:3px solid var(--black);background:var(--white);">
    {{ $users->links() }}
  </div>
</div>

@endsection
