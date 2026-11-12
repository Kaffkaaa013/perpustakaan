@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Pengelolaan Pengguna & Role</h2>
    <p>Kelola data akun pengguna serta tentukan hak akses (Role) masing-masing.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pengguna Terdaftar</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Tambah Pengguna Baru</a>
    </div>

    <div class="table-wrapper">
        <table class="table-buku">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th>Ubah Role Cepat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                    @php
                        $roleName = $u->roles->first()?->name ?? 'siswa';
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #718096;">{{ $users->firstItem() + $index }}</td>
                        <td><strong>{{ $u->name }}</strong></td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge-role badge-{{ $roleName }}">
                                {{ ucfirst($roleName) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.updateRole', $u->id) }}" style="display: flex; gap: 6px; align-items: center;">
                                @csrf
                                <select name="role" class="form-control" style="padding: 4px 8px; font-size: 12px; width: auto;">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}" {{ $roleName === $r->name ? 'selected' : '' }}>
                                            {{ ucfirst($r->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-secondary" style="padding: 4px 8px;">Simpan</button>
                            </form>
                        </td>
                        <td style="text-align: right;">
                            <div class="table-actions" style="justify-content: flex-end;">
                                <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-edit">Ubah</a>
                                @if($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Hapus akun pengguna {{ $u->name }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-delete">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-status-cell">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div style="margin-top: 16px;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

