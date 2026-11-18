@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;">
    <div class="card" style="width: 100%; max-width: 440px; margin-bottom: 0;">
        <div class="card-header">
            <h3>Masuk ke Akun - 250441100013</h3>
            <span class="badge-mode">Autentikasi</span>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="email">Alamat Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" required autofocus autocomplete="username">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="password">Kata Sandi <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required autocomplete="current-password">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #4a5568; text-transform: none; font-weight: normal; margin-bottom: 0;">
                    <input type="checkbox" name="remember" style="cursor: pointer;">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <div style="margin-bottom: 16px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-size: 14px;">
                    Masuk ke Sistem
                </button>
            </div>

            <div style="text-align: center; font-size: 13px; color: #718096; padding-top: 12px; border-top: 1px solid #edf2f7;">
                Belum memiliki akun? <a href="{{ route('register') }}" class="back-link">Daftar Akun Siswa</a>
            </div>
        </form>
    </div>
</div>
@endsection

