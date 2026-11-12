@extends('layouts.app')

@section('content')
    @if ($buku)
        <h2>Detail Buku</h2>
        <table class="book-detail">
            <tr>
                <th>Judul</th>
                <td>{!! $buku['judul'] ?? $buku->judul !!}</td>
            </tr>
            <tr>
                <th>Penulis</th>
                <td>{{ $buku['penulis'] ?? $buku->penulis }}</td>
            </tr>
            <tr>
                <th>Tahun Terbit</th>
                <td>{{ $buku['tahun'] ?? $buku->tahun_terbit }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ is_array($buku['kategori']) ? ($buku['kategori']['nama'] ?? '') : ($buku->kategori->nama ?? $buku['kategori']) }}</td>
            </tr>
        </table>

        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 16px;">
            @can('Mengajukan peminjaman buku')
                <a href="{{ route('peminjaman.create', ['buku_id' => $buku->id]) }}" class="btn btn-primary">Ajukan Peminjaman</a>
            @endcan
            <a href="{{ route('buku.index') }}" class="back-link">&larr; Kembali ke Daftar Buku</a>
        </div>
    @else
        <div class="not-found">
            <h2>Buku Tidak Ditemukan</h2>
            <p>Maaf, buku dengan ID tersebut tidak ada dalam data kami.</p>
            <a href="{{ route('buku.index') }}" class="back-link">&larr; Kembali ke Daftar Buku</a>
        </div>
    @endif
@endsection