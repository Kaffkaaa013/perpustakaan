@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Tambah Pengguna Baru</h2>
    <p>Buat akun baru dan tentukan peran (role) aksesnya.</p>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Formulir Pengguna</h3>
        <a href="{{ route('admin.users.index') }}" class="back-link">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="name">Nama Lengkap <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="Contoh: Rahmat Hidayat" required>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="email">Alamat Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="rahmat@perpustakaan.test" required>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="password">Kata Sandi <span class="required">*</span></label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="role">Pilih Role Akses <span class="required">*</span></label>
            <select id="role" name="role" class="form-control" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

