<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori')
            ->withCount(['peminjamans', 'peminjamans as peminjaman_count'])
            ->latest();

        // Pencarian berdasarkan judul atau penulis
        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                    ->orWhere('penulis', 'like', "%{$cari}%");
            });
        }

        // Filter berdasarkan kategori (bisa ID atau nama kategori)
        if ($request->filled('kategori')) {
            $kategori = $request->input('kategori');
            $query->where(function ($q) use ($kategori) {
                if (is_numeric($kategori)) {
                    $q->where('kategori_id', $kategori);
                } else {
                    $q->whereHas('kategori', function ($sub) use ($kategori) {
                        $sub->where('nama', 'like', "%{$kategori}%");
                    });
                }
            });
        } elseif ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->input('kategori_id'));
        }

        // Filter berdasarkan tahun terbit
        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->input('tahun'));
        } elseif ($request->filled('tahun_terbit')) {
            $query->where('tahun_terbit', $request->input('tahun_terbit'));
        }

        // Jika parameter all=true, kembalikan semua data terformat resource
        if ($request->boolean('all')) {
            return response()->json([
                'success' => true,
                'data' => BukuResource::collection($query->get()),
            ], 200);
        }

        // Pagination 10 data per halaman dengan mempertahankan query string
        $bukus = $query->paginate(10)->withQueryString();

        $bukus->through(function ($buku) {
            return (new BukuResource($buku))->resolve();
        });

        return response()->json($bukus, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuRequest $request)
    {
        $buku = Buku::create($request->validated());

        $buku->load('kategori')->loadCount(['peminjamans', 'peminjamans as peminjaman_count']);

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil ditambahkan.',
            'data' => (new BukuResource($buku))->resolve(),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku)
    {
        $buku->load('kategori')->loadCount(['peminjamans', 'peminjamans as peminjaman_count']);

        return response()->json([
            'success' => true,
            'message' => 'Detail data buku.',
            'data' => (new BukuResource($buku))->resolve(),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBukuRequest $request, Buku $buku)
    {
        $buku->update($request->validated());

        $buku->load('kategori')->loadCount(['peminjamans', 'peminjamans as peminjaman_count']);

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diperbarui.',
            'data' => (new BukuResource($buku))->resolve(),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku)
    {
        $buku->peminjamans()->delete();
        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil dihapus.',
        ], 200);
    }
}
