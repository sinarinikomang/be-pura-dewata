<?php

namespace Database\Seeders;

use App\Models\KategoriArtikel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        // Sesuai permintaan: Upacara & Odalan, Travel Guide, Sejarah & Budaya
        $kategoris = [
            'Upacara & Odalan', 
            'Travel Guide', 
            'Sejarah & Budaya'
        ];

        foreach ($kategoris as $nama) {
            KategoriArtikel::create([
                'nama_kategori' => $nama,
                'slug' => Str::slug($nama)
            ]);
        }
    }
}