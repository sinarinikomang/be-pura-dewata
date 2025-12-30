<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KabupatenSeeder extends Seeder
{
    public function run(): void
    {
        $kabupatens = [
            'Badung', 'Bangli', 'Buleleng', 'Gianyar', 
            'Jembrana', 'Karangasem', 'Klungkung', 'Tabanan', 'Denpasar'
        ];

        foreach ($kabupatens as $nama) {
            Kabupaten::create([
                'nama_kabupaten' => $nama,
                'slug' => Str::slug($nama)
            ]);
        }
    }
}