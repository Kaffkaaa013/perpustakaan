@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Detail Transaksi Peminjaman</h2>
    <p>Informasi lengkap transaksi sirkulasi peminjaman buku #{{ $peminjaman->id }}.</p>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h3>Informasi Peminjaman</h3>
        <span class="badge-role {{ $peminjaman->tanggal_kembali ? 'badge-siswa' : 'badge-petugas' }}">
            {{ $peminjaman->tanggal_kembali ? 'Selesai' : ucfirst($peminjaman->status) }}
        </span>
    </div>

    <table class="book-detail">
        <tr>
            <th>Peminjam</th>
            <td><strong>{{ $peminjaman->user?->name ?? ($peminjaman->anggota?->nama ?? 'Anggota #'.$peminjaman->anggota_id) }}</strong></td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $peminjaman->user?->email ?? ($peminjaman->anggota?->email ?? '-') }}</td>
        </tr>
        <tr>
            <th>Judul Buku</th>
            <td>{{ $peminjaman->buku?->judul ?? '-' }}</td>
        </tr>
        <tr>
            <th>Penulis</th>
            <td>{{ $peminjaman->buku?->penulis ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $peminjaman->buku?->kategori?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Pinjam</th>
            <td>{{ $peminjaman->tanggal_pinjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Kembali</th>
            <td>{{ $peminjaman->tanggal_kembali ?? 'Belum Dikembalikan' }}</td>
        </tr>
        <tr>
            <th>Status Peminjaman</th>
            <td>
                @if($peminjaman->status === 'menunggu')
                    <span class="badge-role badge-petugas">Menunggu Persetujuan</span>
                @elseif($peminjaman->tanggal_kembali || $peminjaman->status === 'dikembalikan')
                    <span class="badge-role badge-siswa">Sudah Dikembalikan</span>
                @else
                    <span class="badge-role badge-petugas">Sedang Dipinjam</span>
                @endif
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
        @if(Auth::user()->hasRole('siswa'))
            <a href="{{ route('peminjaman.riwayat') }}" class="back-link">&larr; Kembali ke Riwayat Saya</a>
        @else
            <a href="{{ route('peminjaman.index') }}" class="back-link">&larr; Kembali ke Daftar Sirkulasi</a>
        @endif

        @can('Mencatat pengembalian buku')
            @if(!$peminjaman->tanggal_kembali && $peminjaman->status !== 'dikembalikan')
                <form method="POST" action="{{ route('peminjaman.kembali', $peminjaman->id) }}" style="display: inline; margin-left: auto;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">Tandai Sudah Kembali</button>
                </form>
            @endif
        @endcan
    </div>
</div>
@endsection

