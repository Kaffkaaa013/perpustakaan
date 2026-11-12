@extends('layouts.app')

@section('content')
    <div style="margin-bottom: 24px;">
        <h2>Manajemen Data Buku</h2>
        <p>Konsumsi RESTful API menggunakan JavaScript Fetch API dan async/await pada Laravel.</p>
    </div>

    <!-- Alert Notifikasi Global -->
    <div id="alert-container">
        <div id="alert-success" class="alert alert-success" style="display: none;" role="alert">
            <div class="alert-content">
                <strong>Berhasil!</strong> <span id="alert-success-text"></span>
            </div>
            <button type="button" class="alert-close" onclick="hideAlert('success')" aria-label="Tutup">&times;</button>
        </div>

        <div id="alert-error" class="alert alert-danger" style="display: none;" role="alert">
            <div class="alert-content">
                <strong>Terjadi Kesalahan!</strong> <span id="alert-error-text"></span>
                <ul id="alert-error-list" style="display: none;"></ul>
            </div>
            <button type="button" class="alert-close" onclick="hideAlert('error')" aria-label="Tutup">&times;</button>
        </div>
    </div>

    <!-- Card Formulir Tambah / Ubah Buku -->
    @can('kelola-buku')
    <div class="card" id="form-card">
        <div class="card-header">
            <h3 id="form-title">Tambah Buku Baru</h3>
            <span id="form-mode-badge" class="badge-mode">MODE TAMBAH</span>
        </div>

        <form id="form-buku" onsubmit="handleFormSubmit(event)" novalidate>
            <input type="hidden" id="buku-id" value="">

            <div class="form-grid">
                <!-- Kolom Kategori -->
                <div class="form-group">
                    <label for="kategori_id">Kategori Buku <span class="required">*</span></label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @if(isset($kategoris) && count($kategoris) > 0)
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Tidak ada kategori tersedia</option>
                        @endif
                    </select>
                    <div id="error-kategori_id" class="invalid-feedback"></div>
                </div>

                <!-- Kolom Judul -->
                <div class="form-group">
                    <label for="judul">Judul Buku <span class="required">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: Laskar Pelangi" required>
                    <div id="error-judul" class="invalid-feedback"></div>
                </div>

                <!-- Kolom Penulis -->
                <div class="form-group">
                    <label for="penulis">Penulis <span class="required">*</span></label>
                    <input type="text" id="penulis" name="penulis" class="form-control" placeholder="Contoh: Andrea Hirata" required>
                    <div id="error-penulis" class="invalid-feedback"></div>
                </div>

                <!-- Kolom Tahun Terbit -->
                <div class="form-group">
                    <label for="tahun_terbit">Tahun Terbit <span class="required">*</span></label>
                    <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" placeholder="Contoh: 2005" min="1900" max="2099" required>
                    <div id="error-tahun_terbit" class="invalid-feedback"></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="btn-submit" class="btn btn-primary">
                    <span id="btn-submit-text">Simpan Buku</span>
                </button>
                <button type="button" id="btn-cancel" class="btn btn-secondary" style="display: none;" onclick="cancelEdit()">
                    Batal Ubah
                </button>
                <button type="button" id="btn-reset" class="btn btn-secondary" onclick="resetForm()">
                    Bersihkan Form
                </button>
            </div>
        </form>
    </div>
    @endcan

    <!-- Card Tabel Daftar Buku -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Buku (Data Real-Time API)</h3>
            <span id="book-count-badge" class="badge-mode">{{ count($buku ?? []) }} Buku</span>
        </div>

        <div class="table-toolbar">
            <div class="search-input-wrapper">
                <input type="text" id="search-input" class="form-control" placeholder="Cari judul / penulis..." oninput="handleSearch(this.value)">
            </div>
            <div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="fetchBuku()">
                    Muat Ulang Data
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-buku">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Kategori</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th style="width: 120px;">Tahun Terbit</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="buku-table-body">
                    @if(isset($buku) && count($buku) > 0)
                        @foreach ($buku as $index => $item)
                            <tr>
                                <td style="text-align: center; color: #718096;">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge-kategori">{{ $item->kategori->nama ?? '-' }}</span>
                                </td>
                                <td>
                                    <strong style="color: #1c2b3a;">{{ $item->judul }}</strong>
                                </td>
                                <td>{{ $item->penulis }}</td>
                                <td>{{ $item->tahun_terbit }}</td>
                                <td style="text-align: center;">
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="{{ route('buku.show', $item->id) }}" class="btn btn-sm btn-secondary">
                                            Lihat Detail
                                        </a>
                                        @can('kelola-buku')
                                            <button type="button" class="btn btn-sm btn-edit" data-id="{{ $item->id }}" onclick="startEdit({{ $item->id }})">
                                                Ubah
                                            </button>
                                            <form method="POST" action="{{ route('buku.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-delete">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="table-status-cell">
                                Memuat data buku dari endpoint GET /api/buku...
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // State lokal aplikasi
        let editId = null;
        let booksCache = [];
        let searchDebounceTimeout = null;
        const canManageBuku = @json(auth()->user()?->can('Kelola data buku') ?? false);

        // Inisialisasi saat DOM siap
        document.addEventListener('DOMContentLoaded', () => {
            ensureCategoriesLoaded();
            @if(!isset($buku) || count($buku) === 0)
                fetchBuku();
            @else
                fetch('/api/buku?all=true', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(res => { if (res.data) booksCache = res.data; })
                    .catch(() => {});
            @endif
        });

        /**
         * Pastikan pilihan kategori tersedia di dropdown
         */
        async function ensureCategoriesLoaded() {
            const select = document.getElementById('kategori_id');
            if (!select) {
                return;
            }
            if (select.options.length <= 1) {
                try {
                    const response = await fetch('/api/kategori', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        const result = await response.json();
                        const categories = result.data || [];
                        select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
                        categories.forEach(cat => {
                            const opt = document.createElement('option');
                            opt.value = cat.id;
                            opt.textContent = cat.nama;
                            select.appendChild(opt);
                        });
                    }
                } catch (e) {
                    console.warn('Tidak dapat memuat kategori via API:', e);
                }
            }
        }

        /**
         * A. Menampilkan Data Buku (GET /api/buku)
         * Menggunakan Fetch API dengan async/await dan API Resource
         */
        async function fetchBuku(searchQuery = '') {
            const tbody = document.getElementById('buku-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="table-status-cell">
                        Memuat data buku dari API...
                    </td>
                </tr>
            `;

            let url = '/api/buku?all=true';
            if (searchQuery && searchQuery.trim() !== '') {
                url += `&cari=${encodeURIComponent(searchQuery.trim())}`;
            }

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Gagal mengambil data buku`);
                }

                const result = await response.json();
                
                // Mendukung format respon API Resource: { success: true, data: [...] } atau { data: [...] } atau [...]
                let books = [];
                if (Array.isArray(result)) {
                    books = result;
                } else if (result.data && Array.isArray(result.data)) {
                    books = result.data;
                }

                booksCache = books;
                renderTable(books);
                document.getElementById('book-count-badge').textContent = `${books.length} Buku`;
            } catch (error) {
                console.error('Fetch Buku Error:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="table-status-cell" style="color: #c0392b;">
                            Gagal memuat data dari server (${error.message}). Pastikan API dapat diakses.
                        </td>
                    </tr>
                `;
                showErrorAlert('Gagal memuat data buku dari API. Silakan coba kembali.');
            }
        }

        /**
         * Render baris tabel buku
         */
        function renderTable(books) {
            const tbody = document.getElementById('buku-table-body');
            tbody.innerHTML = '';

            if (books.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="table-status-cell">
                            Belum ada data buku yang ditemukan.
                        </td>
                    </tr>
                `;
                return;
            }

            books.forEach((book, index) => {
                // Pemetaan atribut sesuai implementasi database & BukuResource
                const id = book.id;
                const judul = book.judul || '-';
                const penulis = book.penulis || '-';
                const tahun = book.tahun_terbit || book.tahun || '-';
                const kategoriNama = book.kategori?.nama || book.kategori_nama || (typeof book.kategori === 'string' ? book.kategori : '-');

                const tr = document.createElement('tr');
                tr.id = `row-buku-${id}`;
                tr.innerHTML = `
                    <td style="text-align: center; color: #718096;">${index + 1}</td>
                    <td>
                        <span class="badge-kategori">${escapeHtml(kategoriNama)}</span>
                    </td>
                    <td>
                        <strong style="color: #1c2b3a;">${escapeHtml(judul)}</strong>
                    </td>
                    <td>${escapeHtml(penulis)}</td>
                    <td>${escapeHtml(String(tahun))}</td>
                    <td style="text-align: center;">
                        <div class="table-actions" style="justify-content: center;">
                            <a href="/buku/${id}" class="btn btn-sm btn-secondary">Detail</a>
                            ${canManageBuku ? `
                            <button type="button" class="btn btn-sm btn-edit" data-id="${id}" onclick="startEdit(${id})">
                                Ubah
                            </button>
                            <button type="button" class="btn btn-sm btn-delete" data-id="${id}" onclick="deleteBuku(${id})">
                                Hapus
                            </button>` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        /**
         * Handler pencarian dengan debounce
         */
        function handleSearch(keyword) {
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => {
                fetchBuku(keyword);
            }, 300);
        }

        /**
         * B & C. Handler Simpan (POST /api/buku) & Update (PUT /api/buku/{id})
         */
        async function handleFormSubmit(event) {
            event.preventDefault();
            clearValidationErrors();

            const kategoriId = document.getElementById('kategori_id').value;
            const judul = document.getElementById('judul').value.trim();
            const penulis = document.getElementById('penulis').value.trim();
            const tahunTerbit = document.getElementById('tahun_terbit').value.trim();

            const payload = {
                kategori_id: kategoriId ? parseInt(kategoriId, 10) : null,
                judul: judul,
                penulis: penulis,
                tahun_terbit: tahunTerbit ? parseInt(tahunTerbit, 10) : null
            };

            const submitBtn = document.getElementById('btn-submit');
            const submitBtnText = document.getElementById('btn-submit-text');
            const originalText = submitBtnText.textContent;

            submitBtn.disabled = true;
            submitBtnText.textContent = editId ? 'Memperbarui...' : 'Menyimpan...';

            try {
                const isUpdating = Boolean(editId);
                const url = isUpdating ? `/api/buku/${editId}` : '/api/buku';
                const method = isUpdating ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    // Operasi berhasil
                    const message = isUpdating
                        ? (data.message || 'Data buku berhasil diperbarui.')
                        : (data.message || 'Data buku baru berhasil ditambahkan.');

                    showSuccessAlert(message);
                    resetForm();
                    await fetchBuku(); // Perbarui tabel tanpa reload manual
                } else if (response.status === 422) {
                    // Validasi gagal (Laravel 422 Unprocessable Entity)
                    handleValidationErrors(data.errors || {});
                    showErrorAlert(data.message || 'Harap periksa kembali isian formulir yang ditandai merah.');
                } else {
                    // Kesalahan HTTP lainnya
                    const errorMsg = data.message || `Terjadi kesalahan pada server (Status ${response.status}).`;
                    showErrorAlert(errorMsg);
                }
            } catch (error) {
                console.error('Submit Error:', error);
                showErrorAlert('Gagal mengirim data ke server. Pastikan server Laravel aktif dan terhubung.');
            } finally {
                submitBtn.disabled = false;
                submitBtnText.textContent = originalText;
            }
        }

        /**
         * C. Mengubah Data Buku (PUT)
         * Mengisi formulir dengan data buku yang dipilih
         */
        function startEdit(id) {
            clearValidationErrors();
            const book = booksCache.find(b => b.id === id);

            if (!book) {
                showErrorAlert('Data buku tidak ditemukan di cache tampilan.');
                return;
            }

            editId = id;
            document.getElementById('buku-id').value = id;
            document.getElementById('kategori_id').value = book.kategori_id || (book.kategori?.id || '');
            document.getElementById('judul').value = book.judul || '';
            document.getElementById('penulis').value = book.penulis || '';
            document.getElementById('tahun_terbit').value = book.tahun_terbit || book.tahun || '';

            // Perbarui UI form ke mode ubah
            document.getElementById('form-title').textContent = 'Ubah Data Buku';
            const modeBadge = document.getElementById('form-mode-badge');
            modeBadge.textContent = `Mode Ubah (ID: ${id})`;
            modeBadge.classList.add('edit-mode');

            document.getElementById('btn-submit-text').textContent = 'Perbarui Buku';
            document.getElementById('btn-cancel').style.display = 'inline-flex';

            // Scroll ke form
            document.getElementById('form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        /**
         * Batalkan mode edit dan kembalikan ke mode tambah
         */
        function cancelEdit() {
            resetForm();
        }

        let isDeleting = false;

        /**
         * D. Menghapus Data Buku (DELETE /api/buku/{id})
         * Menampilkan konfirmasi dan menghapus melalui Fetch API
         */
        async function deleteBuku(id) {
            if (isDeleting) return;

            const numId = Number(id);
            const book = booksCache.find(b => Number(b.id) === numId);
            const judul = book ? book.judul : 'buku ini';

            const confirmed = window.confirm(`Apakah Anda yakin ingin menghapus buku "${judul}"?`);
            if (!confirmed) {
                return; // Batalkan jika pengguna menolak konfirmasi
            }

            isDeleting = true;

            try {
                const response = await fetch(`/api/buku/${numId}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    data = {};
                }

                if (response.ok) {
                    showSuccessAlert(data.message || `Buku "${judul}" berhasil dihapus.`);
                    
                    // Jika sedang mengedit buku yang dihapus, reset form
                    if (Number(editId) === numId) {
                        resetForm();
                    }

                    // Perbarui tabel tanpa reload halaman
                    await fetchBuku();
                } else {
                    const errorMsg = data.message || `Gagal menghapus buku (Status ${response.status}).`;
                    showErrorAlert(errorMsg);
                }
            } catch (error) {
                console.error('Delete Error:', error);
                showErrorAlert('Gagal menghubungi server untuk menghapus buku: ' + error.message);
            } finally {
                isDeleting = false;
            }
        }

        // Expose fungsi ke window agar selalu dapat diakses
        window.deleteBuku = deleteBuku;
        window.startEdit = startEdit;

        /**
         * Mengosongkan form dan mereset status edit
         */
        function resetForm() {
            editId = null;
            document.getElementById('form-buku').reset();
            document.getElementById('buku-id').value = '';

            document.getElementById('form-title').textContent = 'Tambah Buku Baru';
            const modeBadge = document.getElementById('form-mode-badge');
            modeBadge.textContent = 'Mode Tambah';
            modeBadge.classList.remove('edit-mode');

            document.getElementById('btn-submit-text').textContent = 'Simpan Buku';
            document.getElementById('btn-cancel').style.display = 'none';

            clearValidationErrors();
        }

        /**
         * E. Penanganan Kesalahan Validasi (HTTP 422)
         */
        function handleValidationErrors(errors) {
            clearValidationErrors();
            const errorList = document.getElementById('alert-error-list');
            errorList.innerHTML = '';

            let hasErrors = false;
            const errorEntries = Object.entries(errors);
            const errorCount = errorEntries.length;

            for (const [field, messages] of errorEntries) {
                hasErrors = true;
                const message = messages[0];

                // Tambahkan ke daftar error global
                const li = document.createElement('li');
                li.textContent = message;
                errorList.appendChild(li);

                // Tambahkan pesan error inline pada input yang relevan
                const inputElement = document.getElementById(field);
                const errorContainer = document.getElementById(`error-${field}`);

                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                }

                if (errorContainer) {
                    errorContainer.textContent = message;
                    errorContainer.style.display = 'block';
                }
            }

            if (hasErrors) {
                errorList.style.display = 'block';

                // Update text utama dengan jumlah error
                const firstError = errorEntries[0][1][0];
                const moreCount = errorCount - 1;
                let errorText = firstError;
                if (moreCount > 0) {
                    errorText += ` (dan ${moreCount} error lainnya)`;
                }
                document.getElementById('alert-error-text').textContent = errorText;
            }
        }

        /**
         * Bersihkan semua visual error validasi
         */
        function clearValidationErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            const errorList = document.getElementById('alert-error-list');
            if (errorList) {
                errorList.innerHTML = '';
                errorList.style.display = 'none';
            }
        }

        /**
         * Tampilkan notifikasi sukses
         */
        function showSuccessAlert(message) {
            hideAlert('error');
            const alert = document.getElementById('alert-success');
            document.getElementById('alert-success-text').textContent = message;
            alert.style.display = 'flex';

            // Scroll ke atas agar alert terlihat
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Auto dismiss setelah 5 detik
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        /**
         * Tampilkan notifikasi error
         */
        function showErrorAlert(message) {
            hideAlert('success');
            const alert = document.getElementById('alert-error');
            document.getElementById('alert-error-text').textContent = message;
            alert.style.display = 'flex';

            // Scroll ke atas agar alert terlihat
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /**
         * Sembunyikan alert tertentu
         */
        function hideAlert(type) {
            const alert = document.getElementById(`alert-${type}`);
            if (alert) {
                alert.style.display = 'none';
            }
        }

        /**
         * Utility escape string untuk mencegah XSS
         */
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeJs(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }
    </script>
@endsection