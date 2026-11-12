@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Formulir Pengajuan Peminjaman</h2>
    <p>Pilih buku yang ingin Anda pinjam dari koleksi perpustakaan.</p>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3>Detail Pengajuan</h3>
        <a href="{{ url()->previous() }}" class="back-link">&larr; Kembali</a>
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

    <form method="POST" action="{{ route('peminjaman.store') }}">
        @csrf

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="buku_id">Pilih Judul Buku <span class="required">*</span></label>
            <select id="buku_id" name="buku_id" class="form-control" required>
                <option value="">-- Pilih Koleksi Buku --</option>
                @foreach($bukus as $b)
                    <option value="{{ $b->id }}" {{ (string)$selectedBukuId === (string)$b->id ? 'selected' : '' }}>
                        {{ $b->judul }} (Penulis: {{ $b->penulis }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="tanggal_pinjam">Tanggal Peminjaman <span class="required">*</span></label>
            <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" class="form-control" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Kirim Permohonan Pinjam</button>
            <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

