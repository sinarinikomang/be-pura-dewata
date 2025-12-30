<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ArtikelController extends Controller
{
  

    public function index(Request $request)
    {
        // LOG: Untuk debugging
        Log::info('=== USER ARTIKEL ENDPOINT DIAKSES ===');
        Log::info('Query parameters:', $request->all());
        
        $query = Artikel::with('kategori')
            ->where('status', true)
            ->whereDate('tanggal_publikasi', '<=', now())
            ->orderBy('tanggal_publikasi', 'desc');

        // LOG: Jumlah artikel yang memenuhi kondisi
        $count = $query->count();
        Log::info("Artikel dengan status=true: {$count}");
        
        // LOG: Artikel IDs
        $artikelIds = $query->pluck('id')->toArray();
        Log::info("Artikel IDs: " . implode(', ', $artikelIds));

        $artikels = $query->paginate(6); // Ubah ke 6 agar sesuai frontend

        $transformed = $artikels->getCollection()->map(function($artikel) {
            $bulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            $tanggal = Carbon::parse($artikel->tanggal_publikasi);
            $formattedDate = $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n') - 1] . ' ' . $tanggal->format('Y');
            
            return [
                'id' => $artikel->id,
                'title' => $artikel->judul,
                'slug' => $artikel->slug,
                'excerpt' => $artikel->excerpt,
                'category' => $artikel->kategori->nama_kategori ?? 'Umum',
                'image' => $artikel->gambar ? asset('storage/' . $artikel->gambar) : null,
                'date' => $formattedDate,
                'status' => $artikel->status,
                'tanggal_publikasi' => $artikel->tanggal_publikasi,
            ];
        });

        Log::info('=== RESPONSE DIKIRIM ===');
        
        return response()->json([
            'success' => true,
            'data' => $transformed,
            'pagination' => [
                'current_page' => $artikels->currentPage(),
                'total_pages' => $artikels->lastPage(),
                'per_page' => $artikels->perPage(),
                'total' => $artikels->total(),
            ],
            'debug' => [ // Tambah debug info
                'total_with_status_true' => $count,
                'artikel_ids' => $artikelIds,
                'timestamp' => now()->toDateTimeString()
            ]
        ]);
    }

    public function show($slug)
    {
        $artikel = Artikel::with('kategori')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        // Format tanggal untuk detail
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tanggal = Carbon::parse($artikel->tanggal_publikasi);
        $formattedDate = $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n') - 1] . ' ' . $tanggal->format('Y');
        
        // Transform untuk detail
        $transformedArtikel = [
            'id' => $artikel->id,
            'title' => $artikel->judul,
            'slug' => $artikel->slug,
            'excerpt' => $artikel->excerpt,
            'content' => $artikel->konten,
            'category' => $artikel->kategori->nama_kategori ?? 'Umum',
            'image' => $artikel->gambar ? asset('storage/' . $artikel->gambar) : null,
            'date' => $formattedDate,
            'tampilkan_di_kalender' => $artikel->tampilkan_di_kalender,
            'tanggal_publikasi' => $artikel->tanggal_publikasi,
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedArtikel
        ]);
        
    }

    /* ================= ALL ARTICLES (UNTUK FILTERING) ================= */

public function all(Request $request)
{
    // LOG: Untuk debugging
    Log::info('=== ALL ARTIKEL (UNTUK FILTERING) ENDPOINT DIAKSES ===');
    
    $query = Artikel::with('kategori')
        ->where('status', true)
        ->whereDate('tanggal_publikasi', '<=', now())
        ->orderBy('tanggal_publikasi', 'desc');

    // LOG: Jumlah artikel yang memenuhi kondisi
    $count = $query->count();
    Log::info("Total artikel dengan status=true: {$count}");
    
    $artikels = $query->get(); // GET SEMUA tanpa pagination

    $transformed = $artikels->map(function($artikel) {
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tanggal = Carbon::parse($artikel->tanggal_publikasi);
        $formattedDate = $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n') - 1] . ' ' . $tanggal->format('Y');
        
        return [
            'id' => $artikel->id,
            'title' => $artikel->judul,
            'slug' => $artikel->slug,
            'excerpt' => $artikel->excerpt,
            'category' => $artikel->kategori->nama_kategori ?? 'Umum',
            'image' => $artikel->gambar ? asset('storage/' . $artikel->gambar) : null,
            'date' => $formattedDate,
            'status' => $artikel->status,
            'tanggal_publikasi' => $artikel->tanggal_publikasi,
        ];
    });

    Log::info('=== ALL ARTIKEL RESPONSE DIKIRIM ===');
    
    return response()->json([
        'success' => true,
        'data' => $transformed,
        'debug' => [
            'total_articles' => $count,
            'timestamp' => now()->toDateTimeString()
        ]
    ]);
}

    /* ================= ADMIN ================= */

    public function adminIndex()
    {
        $artikels = Artikel::with('kategori')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $artikels
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'kategori_artikel_id' => 'required',
        'judul' => 'required',
        'slug' => 'nullable',
        'excerpt' => 'nullable',
        'konten' => 'required',
        'gambar' => 'nullable|image',
        'tanggal_publikasi' => 'required|date',
        'tampilkan_di_kalender' => 'boolean',
    ]);
    
    if (!isset($validated['tampilkan_di_kalender'])) {
        $validated['tampilkan_di_kalender'] = false;
    }

    // PURE SLUG: Hanya dari judul, tanpa timestamp
    if (empty($validated['slug'])) {
        $baseSlug = Str::slug($request->judul);
        $slug = $baseSlug;
        $counter = 1;
        
        // Cek jika slug sudah ada, tambah angka urut
        while (Artikel::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;
    }

    if ($request->hasFile('gambar')) {
        $validated['gambar'] = $request->file('gambar')->store('artikel', 'public');
    }

    $validated['status'] = false;

    Artikel::create($validated);

    return response()->json(['success' => true]);
}

    public function update(Request $request, $id)
    {
        $artikel = Artikel::findOrFail($id);

        $data = $request->validate([
            'kategori_artikel_id' => 'sometimes|required',
            'judul' => 'sometimes|required',
            'excerpt' => 'nullable',
            'konten' => 'sometimes|required',
            'gambar' => 'nullable|image',
            'tanggal_publikasi' => 'sometimes|required|date',
            'tampilkan_di_kalender' => 'boolean',
        ]);
        if (!isset($data['tampilkan_di_kalender'])) {
            $data['tampilkan_di_kalender'] = false;
        }

        if ($request->hasFile('gambar')) {
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        $artikel->update($data);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $artikel = Artikel::findOrFail($id);
        if ($artikel->gambar) {
            Storage::disk('public')->delete($artikel->gambar);
        }
        $artikel->delete();

        return response()->json(['success' => true]);
    }

    /* ================= STATUS ================= */

       public function publish($id)
    {
        $artikel = Artikel::findOrFail($id);
        
        Log::info("=== PUBLISH ARTIKEL ID: {$id} ===");
        Log::info("Sebelum: status={$artikel->status}, tanggal={$artikel->tanggal_publikasi}");
        
        $artikel->update([
            'status' => true,
            'tanggal_publikasi' => now()
        ]);
        
        Log::info("Sesudah: status={$artikel->status}, tanggal={$artikel->tanggal_publikasi}");
        Log::info("=== PUBLISH BERHASIL ===");
        
        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dipublish'
        ]);
    }

    public function draft($id)
    {
        $artikel = Artikel::findOrFail($id);
        
        Log::info("=== DRAFT ARTIKEL ID: {$id} ===");
        Log::info("Sebelum: status={$artikel->status}");
        
        $artikel->update(['status' => false]);
        
        Log::info("Sesudah: status={$artikel->status}");
        Log::info("=== DRAFT BERHASIL ===");
        
        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dijadikan draft'
        ]);
    }
}