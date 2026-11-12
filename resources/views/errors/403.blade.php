@extends('layouts.app')

@section('content')
    <div class="not-found">
        <h2>Akses Ditolak</h2>
        <p>Anda tidak memiliki hak akses untuk membuka halaman atau menjalankan operasi ini.</p>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="back-link">&larr; Kembali</a>
    </div>
@endsection
