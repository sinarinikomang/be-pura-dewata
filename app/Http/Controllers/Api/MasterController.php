<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\KategoriPura;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    // Ambil semua data Kabupaten untuk Dropdown
    public function getKabupaten()
    {
        $data = Kabupaten::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Kabupaten',
            'data'    => $data
        ]);
    }

    // Ambil semua data Kategori Pura untuk Dropdown
    public function getKategoriPura()
    {
        $data = KategoriPura::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori Pura',
            'data'    => $data
        ]);
    }

    // Ambil semua data Kategori Artikel
    public function getKategoriArtikel()
    {
        $data = KategoriArtikel::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori Artikel',
            'data'    => $data
        ]);
    }
}