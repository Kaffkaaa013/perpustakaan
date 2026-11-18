@extends('layouts.app')

@section('content')
    <div style="text-align: center; padding: 60px 20px; max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h1 style="font-size: 64px; font-weight: 800; color: #e53e3e; margin: 0 0 10px 0; line-height: 1;">403</h1>
        <h2 style="font-size: 24px; font-weight: 600; color: #2d3748; margin: 0 0 16px 0;">403 Forbidden</h2>
        <p style="color: #718096; font-size: 16px; margin: 0 0 28px 0; line-height: 1.6;">
            Akses Ditolak. Anda tidak memiliki hak akses (izin) untuk membuka halaman atau menjalankan operasi ini.
        </p>
        <a href="{{ auth()->check() ? route('home') : route('login') }}" class="btn btn-primary" style="display: inline-block; padding: 10px 24px; text-decoration: none;">
            &larr; Kembali ke Beranda
        </a>
    </div>
@endsection
