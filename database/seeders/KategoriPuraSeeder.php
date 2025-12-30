<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPura;
use Illuminate\Support\Str; // Tambahkan ini

class KategoriPuraSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kahyangan Jagat',
            'Kahyangan Tiga',
            'Segara',
            'Dang Kahyangan',
            'Swagina',
            'Kawitan',
            'Beji / Tirtha',
        ];

        foreach ($categories as $cat) {
            KategoriPura::create([
                'nama_kategori' => $cat,
                'slug' => Str::slug($cat), // Menghasilkan "kahyangan-jagat", "beji-tirtha", dll
            ]);
        }
    }
}