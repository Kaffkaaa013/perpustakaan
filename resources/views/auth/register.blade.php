@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;">
    <div class="card" style="width: 100%; max-width: 480px; margin-bottom: 0;">
        <div class="card-header">
            <h3>Pendaftaran Akun Baru</h3>
            <span class="badge-mode">Role Siswa</span>
        </div>

        <div style="margin-bottom: 16px; padding: 10px 14px; background-color: #ebf8ff; border-left: 3px solid #3182ce; font-size: 13px; color: #2b6cb0; border-radius: 4px;">
            Pendaftaran publik secara otomatis menetapkan hak akses akun sebagai <strong>Siswa</strong>.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="name">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="Contoh: Budi Santoso" required autofocus autocomplete="name">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="email">Alamat Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" required autocomplete="username">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="password">Kata Sandi <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password_confirmation">Konfirmasi Kata Sandi <span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required autocomplete="new-password">
            </div>

            <div style="margin-bottom: 16px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-size: 14px;">
                    Daftar Sebagai Siswa
                </button>
            </div>

            <div style="text-align: center; font-size: 13px; color: #718096; padding-top: 12px; border-top: 1px solid #edf2f7;">
                Sudah memiliki akun? <a href="{{ route('login') }}" class="back-link">Masuk ke Sistem</a>
            </div>
        </form>
    </div>
</div>
@endsection

