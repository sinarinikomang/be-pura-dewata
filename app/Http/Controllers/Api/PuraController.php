<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PuraController extends Controller
{
    // GET: List semua pura
    public function index()
    {
        try {
            $pura = Pura::with(['kabupaten', 'kategoriPura'])
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pura berhasil diambil',
                'data' => $pura
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET: Detail pura by ID atau Slug
    public function show($identifier)
    {
        try {
            $pura = Pura::with(['kabupaten', 'kategoriPura', 'galeri'])
                        ->where(function($query) use ($identifier) {
                            if (is_numeric($identifier)) {
                                $query->where('id', $identifier);
                            } else {
                                $query->where('slug', $identifier);
                            }
                        })
                        ->first();

            if (!$pura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pura tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pura berhasil diambil',
                'data' => $pura
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST: Tambah pura baru - FIXED
    public function store(Request $request)
    {
        $request->validate([
            'nama_pura' => 'required|string|max:255',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kategori_pura_id' => 'required|exists:kategori_puras,id',
            'deskripsi_singkat' => 'required|string',
            'sejarah' => 'required|string',
            'lokasi_iframe' => 'nullable|string',
            'fotos' => 'required|array|min:1',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $pura = Pura::create([
                'nama_pura' => $request->nama_pura,
                'slug' => Str::slug($request->nama_pura),
                'kabupaten_id' => $request->kabupaten_id,
                'kategori_pura_id' => $request->kategori_pura_id,
                'deskripsi_singkat' => $request->deskripsi_singkat,
                'sejarah' => $request->sejarah,
                'lokasi_iframe' => $request->lokasi_iframe,
                'gambar_card' => 'default.jpg'
            ]);

            // Simpan SEMUA foto ke galeri
            foreach ($request->file('fotos') as $index => $file) {
                $fileName = time() . '-' . Str::slug($pura->nama_pura) . '-' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('galeri', $fileName, 'public');

                if ($index === 0) {
                    $pura->update(['gambar_card' => $path]);
                    $pura->galeri()->create([
                        'foto' => $path,
                        'is_thumbnail' => true
                    ]);
                } else {
                    $pura->galeri()->create([
                        'foto' => $path,
                        'is_thumbnail' => false
                    ]);
                }
            }

            DB::commit();

            Log::info('Pura created', ['id' => $pura->id, 'name' => $pura->nama_pura]);

            return response()->json([
                'success' => true,
                'message' => 'Pura berhasil ditambahkan',
                'data' => $pura->load(['kabupaten', 'kategoriPura', 'galeri'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create pura', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pura',
                'error' => $e->getMessage()
            ], 500);
        }
    }
// PUT: Update pura - VERSI FIXED TANPA is_thumbnail
public function update(Request $request, $id)
{
    try {
        $pura = Pura::with('galeri')->findOrFail($id);
        
        // Validasi SIMPLE - tidak wajib semua field
        $validatedData = $request->validate([
            'nama_pura' => 'sometimes|string|max:255',
            'kabupaten_id' => 'sometimes|exists:kabupatens,id',
            'kategori_pura_id' => 'sometimes|exists:kategori_puras,id',
            'deskripsi_singkat' => 'sometimes|string',
            'sejarah' => 'sometimes|string',
            'lokasi_iframe' => 'nullable|string',
            'gambar_existing' => 'nullable|string', // untuk keep existing thumbnail
            'galeri_existing' => 'nullable|array', // untuk keep existing gallery
            'fotos' => 'nullable|array',
            'fotos.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        // Update data dasar - hanya yang dikirim
        $updateData = [];
        
        if ($request->has('nama_pura')) {
            $updateData['nama_pura'] = $request->nama_pura;
            $updateData['slug'] = Str::slug($request->nama_pura);
        }
        
        if ($request->has('kabupaten_id')) $updateData['kabupaten_id'] = $request->kabupaten_id;
        if ($request->has('kategori_pura_id')) $updateData['kategori_pura_id'] = $request->kategori_pura_id;
        if ($request->has('deskripsi_singkat')) $updateData['deskripsi_singkat'] = $request->deskripsi_singkat;
        if ($request->has('sejarah')) $updateData['sejarah'] = $request->sejarah;
        if ($request->has('lokasi_iframe')) $updateData['lokasi_iframe'] = $request->lokasi_iframe;

        // HANDLE THUMBNAIL - SIMPLE LOGIC TANPA is_thumbnail
        if ($request->has('gambar_existing') && $request->gambar_existing) {
            // Jika ada gambar_existing, pakai itu
            $updateData['gambar_card'] = $request->gambar_existing;
        } elseif ($request->hasFile('fotos') && count($request->file('fotos')) > 0) {
            // Jika upload file baru, upload yang pertama sebagai thumbnail
            $firstFile = $request->file('fotos')[0];
            $fileName = time() . '-' . Str::slug($pura->nama_pura) . '-thumb.' . $firstFile->getClientOriginalExtension();
            $path = $firstFile->storeAs('galeri', $fileName, 'public');
            
            $updateData['gambar_card'] = $path;
        }
        // Jika tidak ada keduanya, biarkan thumbnail yang lama

        // Update pura
        if (!empty($updateData)) {
            $pura->update($updateData);
        }

        // HANDLE GALLERY - SIMPLE LOGIC TANPA is_thumbnail
        // 1. Keep existing gallery items if provided
        if ($request->has('galeri_existing') && is_array($request->galeri_existing)) {
            // Hapus semua galeri yang ada di database
            $pura->galeri()->delete();
            
            // Tambahkan thumbnail sebagai foto pertama
            if ($updateData['gambar_card'] ?? $pura->gambar_card) {
                $thumbnailPath = $updateData['gambar_card'] ?? $pura->gambar_card;
                $pura->galeri()->create([
                    'foto' => $thumbnailPath
                ]);
            }
            
            // Tambahkan kembali yang existing (kecuali yang sudah jadi thumbnail)
            $thumbnailPath = $updateData['gambar_card'] ?? $pura->gambar_card;
            foreach ($request->galeri_existing as $path) {
                // Skip jika ini adalah thumbnail (sudah ditambahkan di atas)
                if ($path !== $thumbnailPath) {
                    $pura->galeri()->create([
                        'foto' => $path
                    ]);
                }
            }
        } elseif (!$request->has('galeri_existing') && $request->hasFile('fotos')) {
            // Jika tidak ada galeri_existing tapi ada file baru, hapus semua galeri lama
            $pura->galeri()->delete();
            
            // Tambahkan thumbnail (jika ada)
            if ($updateData['gambar_card'] ?? $pura->gambar_card) {
                $thumbnailPath = $updateData['gambar_card'] ?? $pura->gambar_card;
                $pura->galeri()->create([
                    'foto' => $thumbnailPath
                ]);
            }
        }

        // 2. Add new gallery files (skip file pertama jika sudah jadi thumbnail)
        if ($request->hasFile('fotos')) {
            $files = $request->file('fotos');
            
            // Mulai dari index 1 jika file pertama sudah jadi thumbnail
            $startIndex = (isset($updateData['gambar_card']) && $updateData['gambar_card'] !== ($request->gambar_existing ?? null)) ? 1 : 0;
            
            for ($i = $startIndex; $i < count($files); $i++) {
                $file = $files[$i];
                $fileName = time() . '-' . Str::slug($pura->nama_pura) . '-' . $i . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('galeri', $fileName, 'public');
                
                $pura->galeri()->create([
                    'foto' => $path
                ]);
            }
        }

        DB::commit();

        // Load ulang data
        $pura->load(['kabupaten', 'kategoriPura', 'galeri']);

        return response()->json([
            'success' => true,
            'message' => 'Pura berhasil diupdate',
            'data' => $pura
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate pura: ' . $e->getMessage()
        ], 500);
    }
}

    // DELETE: Hapus pura
    public function destroy($id)
    {
        $pura = Pura::with('galeri')->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            if ($pura->gambar_card && $pura->gambar_card !== 'default.jpg') {
                Storage::disk('public')->delete($pura->gambar_card);
            }
            
            foreach ($pura->galeri as $galeri) {
                Storage::disk('public')->delete($galeri->foto);
                $galeri->delete();
            }
            
            $pura->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pura berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pura',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}