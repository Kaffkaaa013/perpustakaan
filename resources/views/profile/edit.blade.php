@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Profil Saya</h2>
    <p>Kelola data pribadi, informasi akun, dan keamanan kata sandi Anda.</p>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Informasi Akun</h3>
        @php
            $roleName = $user->roles->first()?->name ?? 'User';
        @endphp
        <span class="badge-role badge-{{ $roleName }}">Role: {{ ucfirst($roleName) }}</span>
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

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="name">Nama Lengkap <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="email">Alamat Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="password">Kata Sandi Baru (Kosongkan bila tidak ingin mengganti)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter">
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

