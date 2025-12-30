<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\PuraController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\KalenderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================
// ROUTES UNTUK GAMBAR (PUBLIC ACCESS)
// ============================================

// Route 1: Serve gambar via API dengan folder spesifik
Route::get('/images/{folder}/{filename}', function ($folder, $filename) {
    $validFolders = ['artikel', 'galeri', 'pura', 'default'];
    
    if (!in_array($folder, $validFolders)) {
        return response()->json([
            'success' => false,
            'message' => 'Folder tidak valid. Gunakan: artikel, galeri, pura, atau default',
            'valid_folders' => $validFolders
        ], 400);
    }
    
    $path = "{$folder}/{$filename}";
    
    if (!Storage::disk('public')->exists($path)) {
        // Coba di root folder
        if (Storage::disk('public')->exists($filename)) {
            $path = $filename;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan',
                'searched_path' => $path,
                'suggestion' => 'Pastikan file ada di storage/app/public/' . $folder . '/'
            ], 404);
        }
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return response($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Cache-Control', 'public, max-age=31536000')
        ->header('Access-Control-Allow-Origin', '*');
});

// Route 2: Auto-detect folder untuk gambar
Route::get('/image/{filename}', function ($filename) {
    $folders = ['artikel', 'galeri', 'pura', 'default'];
    
    foreach ($folders as $folder) {
        $path = "{$folder}/{$filename}";
        
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Access-Control-Allow-Origin', '*');
        }
    }
    
    // Coba di root
    if (Storage::disk('public')->exists($filename)) {
        $file = Storage::disk('public')->get($filename);
        $mimeType = Storage::disk('public')->mimeType($filename);
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Access-Control-Allow-Origin', '*');
    }
    
    return response()->json([
        'success' => false,
        'message' => 'File tidak ditemukan di folder manapun',
        'filename' => $filename,
        'searched_folders' => $folders
    ], 404);
});

// Route 3: Test endpoint untuk gambar
Route::get('/test-image/{filename}', function ($filename) {
    $folders = ['artikel', 'galeri', 'pura'];
    
    $results = [];
    
    foreach ($folders as $folder) {
        $path = "{$folder}/{$filename}";
        $exists = Storage::disk('public')->exists($path);
        
        $results[$folder] = [
            'exists' => $exists,
            'path' => $path,
            'url' => $exists ? url("/api/images/{$folder}/{$filename}") : null
        ];
    }
    
    return response()->json([
        'success' => true,
        'filename' => $filename,
        'results' => $results,
        'direct_urls' => [
            'api_specific' => url("/api/images/artikel/{$filename}"),
            'api_auto' => url("/api/image/{$filename}"),
            'web_route' => url("/storage/artikel/{$filename}"),
            'web_auto' => url("/file/{$filename}")
        ]
    ]);
});

// ============================================
// EXISTING ROUTES (TETAP PERTAHANKAN)
// ============================================

// Public Routes
Route::get('/kabupaten', [MasterController::class, 'getKabupaten']);
Route::get('/kategori-pura', [MasterController::class, 'getKategoriPura']);
Route::get('/kategori-artikel', [MasterController::class, 'getKategoriArtikel']);
Route::get('/dropdown-data', [MasterController::class, 'getDropdownData']);

// Pura Routes - Public
Route::get('/pura', [PuraController::class, 'index']);
Route::get('/pura/{identifier}', [PuraController::class, 'show']);

// Artikel Routes - Public
Route::get('/artikel/all', [ArtikelController::class, 'all']);
Route::get('/artikel', [ArtikelController::class, 'index']);
Route::get('/artikel/{slug}', [ArtikelController::class, 'show']);

// Kalender Routes - Public
Route::get('/kalender', [KalenderController::class, 'index']);
Route::get('/kalender/events-by-date', [KalenderController::class, 'getEventsByDate']);
Route::post('/kalender/pengajuan', [KalenderController::class, 'store']);

// Admin Routes with Basic Auth
Route::middleware('auth.basic')->group(function () {
    // Pura Admin
    Route::post('/pura', [PuraController::class, 'store']);
    Route::put('/pura/{id}', [PuraController::class, 'update']); 
    Route::delete('/pura/{id}', [PuraController::class, 'destroy']);
    
    // Artikel Admin
    Route::get('/artikel/admin/list', [ArtikelController::class, 'adminIndex']);
    Route::post('/artikel', [ArtikelController::class, 'store']);
    Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
    Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);
    Route::patch('/artikel/{id}/publish', [ArtikelController::class, 'publish']);
    Route::patch('/artikel/{id}/draft', [ArtikelController::class, 'draft']);

    // Kalender Admin
    Route::get('/kalender/pengajuan-pending', [KalenderController::class, 'getPengajuanPending']);
    Route::post('/kalender/admin/create', [KalenderController::class, 'storeAdmin']);
    Route::put('/kalender/pengajuan/{id}/status', [KalenderController::class, 'updateStatus']);
    Route::get('/kalender/riwayat-validasi', [KalenderController::class, 'getRiwayatValidasi']);
    Route::delete('/kalender/pengajuan/{id}', [KalenderController::class, 'destroy']);
});

// Test route untuk cek API status
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'API is running',
        'timestamp' => now(),
        'image_endpoints' => [
            '/api/images/{folder}/{filename}',
            '/api/image/{filename}',
            '/api/test-image/{filename}'
        ]
    ]);
});