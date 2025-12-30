<?php

namespace Database\Seeders;

use App\Models\Pura;
use App\Models\GaleriPura;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuraSeeder extends Seeder
{
    public function run()
    {
        // 🔥 HAPUS DATA LAMA DULU
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('galeri_puras')->truncate();
        DB::table('puras')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data pura contoh
        $puraData = [
            [
                'nama_pura' => 'Pura Besakih',
                'gambar_card' => 'pura/besakih.jpg',
                'deskripsi_singkat' => 'Pura Besakih dikenal sebagai "Pura Agung Besakih" dan merupakan pura terbesar serta tersuci di Bali. Terletak di lereng Gunung Agung, pura ini adalah pusat dari seluruh kegiatan keagamaan Hindu di Bali.',
                'sejarah' => 'Pura Besakih diperkirakan telah ada sejak masa Bali Kuno sekitar abad ke-8. Pura ini disebutkan dalam beberapa prasasti kuno dan telah mengalami beberapa kali pemugaran. Pada tahun 1963, letusan Gunung Agung menghancurkan sebagian besar bangunan di sekitarnya, namun secara ajaib Pura Besakih selamat.',
                'lokasi_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.091066168896!2d115.45054557583327!3d-8.374530291725947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd218f3f7c2c8a5%3A0x6b2f0e7b6e7d5e6f!2sPura%20Besakih!5e0!3m2!1sen!2sid!4v1630000000000!5m2!1sen!2sid',
                'kabupaten_id' => 6, // Karangasem
                'kategori_pura_id' => 1, // Kahyangan Jagat
            ],
            [
                'nama_pura' => 'Pura Tanah Lot',
                'gambar_card' => 'pura/tanah-lot.jpg',
                'deskripsi_singkat' => 'Pura Tanah Lot adalah pura laut yang terletak di atas batu karang besar di tepi pantai. Salah satu pura paling ikonik di Bali dengan pemandangan sunset yang spektakuler.',
                'sejarah' => 'Pura Tanah Lot dibangun oleh Dang Hyang Nirartha, seorang pendeta dari Jawa pada abad ke-16. Konon, ia melakukan perjalanan spiritual dan merasa tempat ini memiliki aura spiritual yang kuat.',
                'lokasi_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.091066168896!2d115.45054557583327!3d-8.374530291725947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd218f3f7c2c8a5%3A0x6b2f0e7b6e7d5e6f!2sPura%20Tanah%20Lot!5e0!3m2!1sen!2sid!4v1630000000000!5m2!1sen!2sid',
                'kabupaten_id' => 1, // Badung
                'kategori_pura_id' => 3, // Segara
            ],
            [
                'nama_pura' => 'Pura Uluwatu',
                'gambar_card' => 'pura/uluwatu.jpg',
                'deskripsi_singkat' => 'Pura Uluwatu terletak di tebing tinggi sekitar 70 meter di atas permukaan laut. Selain sebagai tempat suci, pura ini juga terkenal dengan pertunjukan tari Kecak di waktu sunset.',
                'sejarah' => 'Pura Uluwatu dibangun pada abad ke-11 oleh Empu Kuturan. Pura ini merupakan salah satu dari enam pura kahyangan jagat yang dianggap sebagai penyangga spiritual Bali.',
                'lokasi_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.091066168896!2d115.45054557583327!3d-8.374530291725947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd218f3f7c2c8a5%3A0x6b2f0e7b6e7d5e6f!2sPura%20Uluwatu!5e0!3m2!1sen!2sid!4v1630000000000!5m2!1sen!2sid',
                'kabupaten_id' => 1, // Badung
                'kategori_pura_id' => 1, // Kahyangan Jagat
            ],
            [
                'nama_pura' => 'Pura Ulun Danu Beratan',
                'gambar_card' => 'pura/ulun-danu.jpg',
                'deskripsi_singkat' => 'Pura Ulun Danu Beratan adalah pura air yang terletak di tepi Danau Beratan. Pura ini dipersembahkan untuk Dewi Danu, dewi air, danau, dan sungai.',
                'sejarah' => 'Pura Ulun Danu Beratan dibangun pada abad ke-17 sebagai tempat pemujaan Dewi Danu. Pura ini merupakan bagian dari kompleks pura di sekitar Danau Beratan.',
                'lokasi_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.091066168896!2d115.45054557583327!3d-8.374530291725947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd218f3f7c2c8a5%3A0x6b2f0e7b6e7d5e6f!2sPura%20Ulun%20Danu%20Beratan!5e0!3m2!1sen!2sid!4v1630000000000!5m2!1sen!2sid',
                'kabupaten_id' => 2, // Bangli
                'kategori_pura_id' => 7, // Beji / Tirtha
            ],
        ];

        foreach ($puraData as $data) {
            $pura = Pura::create($data);
            
        
        }
    }
}