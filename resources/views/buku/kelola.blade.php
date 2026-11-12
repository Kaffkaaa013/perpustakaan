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
            <span id="form-mode-badge" class="badge-mode">Mode Tambah</span>
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
            <span id="book-count-badge" class="badge-mode">0 Buku</span>
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
                    <tr>
                        <td colspan="6" class="table-status-cell">
                            Memuat data buku dari endpoint GET /api/buku...
                        </td>
                    </tr>
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
            fetchBuku();
            ensureCategoriesLoaded();
        });

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

        function handleSearch(keyword) {
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => {
                fetchBuku(keyword);
            }, 300);
        }

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
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.status === 422) {
                    displayValidationErrors(data.errors || {});
                    showErrorAlert(data.message || 'Data yang dikirim tidak valid.');
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.message || `Gagal memproses data (${response.status})`);
                }

                showSuccessAlert(data.message || (isUpdating ? 'Data buku berhasil diperbarui!' : 'Data buku berhasil disimpan!'));
                resetForm();
                fetchBuku();
            } catch (error) {
                console.error('Submit Buku Error:', error);
                showErrorAlert(error.message || 'Terjadi kesalahan sistem saat mengirim data.');
            } finally {
                submitBtn.disabled = false;
                submitBtnText.textContent = editId ? 'Simpan Perubahan' : 'Simpan Buku';
            }
        }

        function startEdit(id) {
            const book = booksCache.find(b => b.id === id);
            if (!book) {
                showErrorAlert('Data buku tidak ditemukan dalam memori lokal.');
                return;
            }

            editId = id;
            document.getElementById('buku-id').value = id;
            document.getElementById('kategori_id').value = book.kategori_id || book.kategori?.id || '';
            document.getElementById('judul').value = book.judul || '';
            document.getElementById('penulis').value = book.penulis || '';
            document.getElementById('tahun_terbit').value = book.tahun_terbit || book.tahun || '';

            document.getElementById('form-title').textContent = 'Ubah Data Buku';
            const badge = document.getElementById('form-mode-badge');
            badge.textContent = 'Mode Ubah';
            badge.classList.add('edit-mode');

            document.getElementById('btn-submit-text').textContent = 'Simpan Perubahan';
            document.getElementById('btn-cancel').style.display = 'inline-flex';

            document.getElementById('form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            clearValidationErrors();
        }

        function cancelEdit() {
            resetForm();
            showSuccessAlert('Mode pengubahan buku dibatalkan.');
        }

        function resetForm() {
            editId = null;
            document.getElementById('buku-id').value = '';
            document.getElementById('form-buku').reset();

            document.getElementById('form-title').textContent = 'Tambah Buku Baru';
            const badge = document.getElementById('form-mode-badge');
            badge.textContent = 'Mode Tambah';
            badge.classList.remove('edit-mode');

            document.getElementById('btn-submit-text').textContent = 'Simpan Buku';
            document.getElementById('btn-cancel').style.display = 'none';

            clearValidationErrors();
        }

        async function deleteBuku(id) {
            const book = booksCache.find(b => b.id === id);
            const judul = book ? book.judul : `ID #${id}`;

            if (!confirm(`Apakah Anda yakin ingin menghapus buku "${judul}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/buku/${id}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus data buku.');
                }

                showSuccessAlert(data.message || 'Data buku berhasil dihapus.');

                if (editId === id) {
                    resetForm();
                }

                fetchBuku();
            } catch (error) {
                console.error('Delete Buku Error:', error);
                showErrorAlert(error.message || 'Terjadi kesalahan sistem saat menghapus data.');
            }
        }

        function displayValidationErrors(errors) {
            for (const [field, messages] of Object.entries(errors)) {
                const input = document.getElementById(field);
                const errorDiv = document.getElementById(`error-${field}`);

                if (input) {
                    input.classList.add('is-invalid');
                }
                if (errorDiv) {
                    errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
                    errorDiv.style.display = 'block';
                }
            }
        }

        function clearValidationErrors() {
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            document.querySelectorAll('.form-control').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }

        function showSuccessAlert(message) {
            const container = document.getElementById('alert-success');
            const text = document.getElementById('alert-success-text');
            text.textContent = message;
            container.style.display = 'flex';
            document.getElementById('alert-error').style.display = 'none';
        }

        function showErrorAlert(message, errorList = []) {
            const container = document.getElementById('alert-error');
            const text = document.getElementById('alert-error-text');
            const list = document.getElementById('alert-error-list');

            text.textContent = message;
            if (errorList.length > 0) {
                list.innerHTML = '';
                errorList.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = err;
                    list.appendChild(li);
                });
                list.style.display = 'block';
            } else {
                list.style.display = 'none';
            }

            container.style.display = 'flex';
            document.getElementById('alert-success').style.display = 'none';
        }

        function hideAlert(type) {
            const container = document.getElementById(`alert-${type}`);
            if (container) {
                container.style.display = 'none';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endsection
