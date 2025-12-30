<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kalender;
use App\Models\Pura;
use App\Models\KategoriArtikel;
use App\Models\User;
use Carbon\Carbon;

class KalenderSeeder extends Seeder
{
    public function run(): void
    {
        echo "Memulai KalenderSeeder...\n";
        
        // Ambil pura (perhatikan: table name = puras, kolom = nama_pura)
        $puraBesakih = Pura::where('nama_pura', 'Pura Besakih')->first();
        if (!$puraBesakih) {
            echo "⚠ Pura Besakih tidak ditemukan. Membuat dummy...\n";
            // Buat pura dummy jika belum ada
            $puraBesakih = Pura::create([
                'nama_pura' => 'Pura Besakih',
                'slug' => 'pura-besakih',
                'gambar_card' => 'default.jpg',
                'deskripsi_singkat' => 'Pura terbesar di Bali',
                'sejarah' => 'Pura terbesar di Bali',
                'kabupaten_id' => 1,
                'kategori_pura_id' => 1,
            ]);
        }
        
        $puraTanahLot = Pura::where('nama_pura', 'Pura Tanah Lot')->first();
        if (!$puraTanahLot) {
            echo "⚠ Pura Tanah Lot tidak ditemukan. Membuat dummy...\n";
            $puraTanahLot = Pura::create([
                'nama_pura' => 'Pura Tanah Lot',
                'slug' => 'pura-tanah-lot',
                'gambar_card' => 'default.jpg',
                'deskripsi_singkat' => 'Pura di atas batu karang',
                'sejarah' => 'Pura di atas batu karang',
                'kabupaten_id' => 1,
                'kategori_pura_id' => 1,
            ]);
        }
        
        $puraUluwatu = Pura::where('nama_pura', 'Pura Uluwatu')->first();
        if (!$puraUluwatu) {
            echo "⚠ Pura Uluwatu tidak ditemukan. Membuat dummy...\n";
            $puraUluwatu = Pura::create([
                'nama_pura' => 'Pura Uluwatu',
                'slug' => 'pura-uluwatu',
                'gambar_card' => 'default.jpg',
                'deskripsi_singkat' => 'Pura di tebing',
                'sejarah' => 'Pura di tebing',
                'kabupaten_id' => 1,
                'kategori_pura_id' => 1,
            ]);
        }
        
        echo "📌 Pura siap:\n";
        echo "- Pura Besakih (ID: {$puraBesakih->id})\n";
        echo "- Pura Tanah Lot (ID: {$puraTanahLot->id})\n";
        echo "- Pura Uluwatu (ID: {$puraUluwatu->id})\n";
        
        // Ambil kategori Upacara & Odalan
        $kategoriUpacara = KategoriArtikel::where('nama_kategori', 'Upacara & Odalan')->first();
        if (!$kategoriUpacara) {
            echo "⚠ Kategori Upacara & Odalan tidak ditemukan. Membuat...\n";
            $kategoriUpacara = KategoriArtikel::create([
                'nama_kategori' => 'Upacara & Odalan',
                'slug' => 'upacara-odalan'
            ]);
        }
        echo "📌 Kategori: Upacara & Odalan (ID: {$kategoriUpacara->id})\n";
        
        // Ambil atau buat user
        $user = User::first();
        if (!$user) {
            echo "⚠ User tidak ditemukan. Membuat user dummy...\n";
            $user = \App\Models\User::create([
                'name' => 'Admin Pura Dewata',
                'email' => 'admin@puradewata.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);
        }
        echo "📌 User: {$user->name} (ID: {$user->id})\n";

        $events = [
            // Events yang sudah approved (tampil di kalender biru)
            [
                'pura_id' => $puraBesakih->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Piodalan Pura Besakih',
                'deskripsi' => 'Perayaan odalan tahunan di Pura Besakih, pura terbesar di Bali.',
                'tanggal_mulai' => '2025-12-15',
                'tanggal_selesai' => '2025-12-16',
                'wewaran' => 'Sinta, Umanis',
                'pawukon' => 'Wuku Dungulan',
                'status' => 'approved',
                'catatan_admin' => 'Event tahunan yang sudah rutin dilaksanakan',
                'approved_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'pura_id' => $puraTanahLot->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Odalan Pura Tanah Lot',
                'deskripsi' => 'Perayaan odalan di Pura Tanah Lot yang terletak di atas batu karang.',
                'tanggal_mulai' => '2025-12-20',
                'tanggal_selesai' => '2025-12-21',
                'wewaran' => 'Landep, Paing',
                'pawukon' => 'Wuku Kuningan',
                'status' => 'approved',
                'catatan_admin' => 'Event dengan pemandangan sunset yang indah',
                'approved_at' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now()->subDays(8),
            ],
            [
                'pura_id' => $puraUluwatu->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Upacara Pujawali Pura Uluwatu',
                'deskripsi' => 'Upacara pujawali di Pura Uluwatu yang terletak di tebing.',
                'tanggal_mulai' => '2025-12-25',
                'tanggal_selesai' => '2025-12-25',
                'wewaran' => 'Ukir, Pon',
                'pawukon' => 'Wuku Landep',
                'status' => 'approved',
                'catatan_admin' => 'Event dengan atraksi budaya menarik',
                'approved_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(7),
            ],

            // Events pending (menunggu approval admin)
            [
                'pura_id' => $puraTanahLot->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Upacara Melasti di Tanah Lot',
                'deskripsi' => 'Upacara penyucian diri dan pratima di Pantai Tanah Lot.',
                'tanggal_mulai' => '2025-12-30',
                'tanggal_selesai' => '2025-12-30',
                'wewaran' => 'Wage, Kliwon',
                'pawukon' => 'Wuku Langkir',
                'status' => 'pending',
                'catatan_admin' => null,
                'approved_at' => null,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'pura_id' => $puraBesakih->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Upacara Ngenteg Linggih Pura Besakih',
                'deskripsi' => 'Upacara pemasangan pratima baru di Pura Besakih.',
                'tanggal_mulai' => '2026-01-05',
                'tanggal_selesai' => '2026-01-07',
                'wewaran' => 'Pahing, Pon',
                'pawukon' => 'Wuku Medangsia',
                'status' => 'pending',
                'catatan_admin' => null,
                'approved_at' => null,
                'created_at' => Carbon::now()->subDays(1),
            ],

            // Events rejected (contoh)
            [
                'pura_id' => $puraTanahLot->id,
                'kategori_artikel_id' => $kategoriUpacara->id,
                'user_id' => $user->id,
                'nama_upacara' => 'Festival Musik di Tanah Lot',
                'deskripsi' => 'Acara festival musik di area Tanah Lot.',
                'tanggal_mulai' => '2025-11-30',
                'tanggal_selesai' => '2025-11-30',
                'wewaran' => 'Paing, Kliwon',
                'pawukon' => 'Wuku Dungulan',
                'status' => 'rejected',
                'catatan_admin' => 'Acara ini tidak sesuai dengan fungsi sakral pura.',
                'approved_at' => null,
                'rejected_at' => Carbon::now()->subDays(15),
                'created_at' => Carbon::now()->subDays(20),
            ],
        ];

        $approved = 0;
        $pending = 0;
        $rejected = 0;
        
        foreach ($events as $event) {
            $existing = Kalender::where('nama_upacara', $event['nama_upacara'])
                ->where('tanggal_mulai', $event['tanggal_mulai'])
                ->first();
            
            if (!$existing) {
                Kalender::create($event);
                
                // Hitung status
                if ($event['status'] == 'approved') $approved++;
                elseif ($event['status'] == 'pending') $pending++;
                elseif ($event['status'] == 'rejected') $rejected++;
                
                echo "✓ Event '{$event['nama_upacara']}' ({$event['status']}) berhasil dibuat.\n";
            } else {
                echo "⚠ Event '{$event['nama_upacara']}' sudah ada, dilewati.\n";
            }
        }

        echo "\n✅ KalenderSeeder selesai!\n";
        echo "📊 Total events: " . Kalender::count() . "\n";
        echo "🟢 Approved: $approved\n";
        echo "🟡 Pending: $pending\n";
        echo "🔴 Rejected: $rejected\n";
    }
}