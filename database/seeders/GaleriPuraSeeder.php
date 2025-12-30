<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GaleriPura;
use Illuminate\Support\Facades\DB;

class GaleriPuraSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        GaleriPura::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $fotos = ['besakih_1.jpg','besakih_2.jpg','besakih_3.jpg','besakih_4.jpg'];

        foreach ($fotos as $foto) {
            GaleriPura::create([
                'pura_id' => 1, // pastikan ID Pura sama dengan PuraSeeder
                'foto' => $foto,
            ]);
        }
    }
}

