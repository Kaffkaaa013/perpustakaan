<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpustakaan 250441100013</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? 'assets/app.css';
        $jsFile  = $manifest['resources/js/app.js']['file'] ?? 'assets/app.js';
    @endphp
    <link rel="stylesheet" href="/build/{{ $cssFile }}">
    <script src="/build/{{ $jsFile }}" defer></script>
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Perpustakaan 250441100013</h1>
        </div>
    </header>

    <nav class="navbar">
        <div class="container">
            <div class="nav-links">
                <!-- Public links -->
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.*') ? 'active' : '' }}">Daftar Buku</a>

                @auth
                    <!-- Dashboard direct link (role‑specific) -->
                    @role('admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    @endrole

                    @role('petugas')
                        <a href="{{ route('petugas.dashboard') }}" class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">Dashboard Petugas</a>
                    @endrole

                    @role('siswa')
                        <a href="{{ route('siswa.dashboard') }}" class="{{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">Dashboard Siswa</a>
                    @endrole

                    <!-- Management dropdown (Admin & Petugas) -->
                    @role('admin')
                        <div class="dropdown">
                            <button type="button" class="dropdown-button {{ request()->routeIs('kelola-buku.*') || request()->routeIs('admin.users.*') || request()->routeIs('peminjaman.index') || request()->routeIs('laporan.*') || request()->routeIs('profile.*') ? 'active' : '' }}">
                                Kelola
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('kelola-buku.index') }}" class="{{ request()->routeIs('kelola-buku.*') ? 'active' : '' }}">Kelola Buku</a>
                                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Kelola Pengguna</a>
                                <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.index') ? 'active' : '' }}">Peminjaman</a>
                                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('peminjaman.riwayat') }}" class="{{ request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}">Riwayat Pinjam</a>
                                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil</a>
                            </div>
                        </div>
                    @endrole

                    @role('petugas')
                        <div class="dropdown">
                            <button type="button" class="dropdown-button {{ request()->routeIs('kelola-buku.*') || request()->routeIs('peminjaman.index') || request()->routeIs('laporan.*') || request()->routeIs('profile.*') ? 'active' : '' }}">
                                Kelola
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('kelola-buku.index') }}" class="{{ request()->routeIs('kelola-buku.*') ? 'active' : '' }}">Kelola Buku</a>
                                <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.index') ? 'active' : '' }}">Peminjaman</a>
                                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('peminjaman.riwayat') }}" class="{{ request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}">Riwayat Pinjam</a>
                                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil</a>
                            </div>
                        </div>
                    @endrole

                    <!-- Siswa actions dropdown (only for siswa) -->
                    @role('siswa')
                        <div class="dropdown">
                            <button type="button" class="dropdown-button {{ request()->routeIs('peminjaman.create') || request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}">
                                Pinjaman
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                @can('Mengajukan peminjaman buku')
                                    <a href="{{ route('peminjaman.create') }}" class="{{ request()->routeIs('peminjaman.create') ? 'active' : '' }}">Ajukan Pinjam</a>
                                @endcan
                                @can('Melihat riwayat peminjaman sendiri')
                                    <a href="{{ route('peminjaman.riwayat') }}" class="{{ request()->routeIs('peminjaman.riwayat') ? 'active' : '' }}">Riwayat Pinjam</a>
                                @endcan
                            </div>
                        </div>
                    @endrole
                @endauth
            </div>

            <div class="user-section">
                @auth
                    @php
                        $userRole = Auth::user()->roles->first()?->name ?? 'User';
                    @endphp
                    <div class="user-info">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span class="badge-role badge-{{ $userRole }}">({{ ucfirst($userRole) }})</span>
                    </div>
                    @role('siswa')
                        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil</a>
                    @endrole
                    <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    @if (session('success'))
        <div class="container" style="margin-top: 15px;">
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container" style="margin-top: 15px;">
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <main class="content">
        <div class="container">
            @if (isset($slot) && trim((string)$slot) !== '')
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Perpustakaan 250441100013</p>
        </div>
    </footer>

</body>
</html>