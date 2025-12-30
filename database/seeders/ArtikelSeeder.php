<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Support\Str;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        echo "Memulai ArtikelSeeder...\n";
        
        // Ambil atau buat kategori artikel
        $kategoriUpacara = KategoriArtikel::where('nama_kategori', 'Upacara & Odalan')->first();
        if (!$kategoriUpacara) {
            echo "Membuat kategori 'Upacara & Odalan'...\n";
            $kategoriUpacara = KategoriArtikel::create([
                'nama_kategori' => 'Upacara & Odalan',
                'slug' => 'upacara-odalan'
            ]);
        }
        
        $kategoriTravel = KategoriArtikel::where('nama_kategori', 'Travel Guide')->first();
        if (!$kategoriTravel) {
            echo "Membuat kategori 'Travel Guide'...\n";
            $kategoriTravel = KategoriArtikel::create([
                'nama_kategori' => 'Travel Guide',
                'slug' => 'travel-guide'
            ]);
        }
        
        $kategoriSejarah = KategoriArtikel::where('nama_kategori', 'Sejarah & Budaya')->first();
        if (!$kategoriSejarah) {
            echo "Membuat kategori 'Sejarah & Budaya'...\n";
            $kategoriSejarah = KategoriArtikel::create([
                'nama_kategori' => 'Sejarah & Budaya',
                'slug' => 'sejarah-budaya'
            ]);
        }
        
        $kategoriUmum = KategoriArtikel::where('nama_kategori', 'Umum')->first();
        if (!$kategoriUmum) {
            echo "Membuat kategori 'Umum'...\n";
            $kategoriUmum = KategoriArtikel::create([
                'nama_kategori' => 'Umum',
                'slug' => 'umum'
            ]);
        }
        
        echo "Kategori siap:\n";
        echo "- Upacara & Odalan (ID: {$kategoriUpacara->id})\n";
        echo "- Travel Guide (ID: {$kategoriTravel->id})\n";
        echo "- Sejarah & Budaya (ID: {$kategoriSejarah->id})\n";
        echo "- Umum (ID: {$kategoriUmum->id})\n";

        $artikels = [
            // Upacara & Odalan (tampil di kalender)
            [
                'kategori_artikel_id' => $kategoriUpacara->id,
                'judul' => 'Galungan, Kemenangan Dharma Melawan Adharma',
                'slug' => 'galungan-kemenangan-dharma-melawan-adharma',
                'excerpt' => 'Hari Raya Galungan merupakan simbol kemenangan Dharma (kebenaran) melawan Adharma (kejahatan). Perayaan ini dirayakan setiap 210 hari berdasarkan kalender Pawukon Bali.',
                'konten' => '<h2>Makna Galungan</h2>
<p>Hari Raya Galungan merupakan simbol kemenangan Dharma (kebenaran) melawan Adharma (kejahatan). Perayaan ini dirayakan setiap 210 hari berdasarkan kalender Pawukon Bali, yang artinya setahun dalam kalender Bali terdiri dari 210 hari.</p>

<h3>Rangkaian Upacara</h3>
<p>Rangkaian upacara Galungan dimulai dari:</p>
<ul>
<li><strong>Sugihan Jawa</strong> - Penyucian diri lahir batin</li>
<li><strong>Sugihan Bali</strong> - Penyucian alam semesta</li>
<li><strong>Penampahan Galungan</strong> - Penyembelihan hewan kurban</li>
<li><strong>Galungan</strong> - Puncak perayaan</li>
<li><strong>Manis Galungan</strong> - Hari untuk bersilaturahmi</li>
</ul>

<p>Galungan bukan sekadar hari libur, tetapi momentum untuk introspeksi diri dan memperkuat keyakinan akan kemenangan kebenaran atas kejahatan.</p>',
                'gambar' => 'artikel/galungan.jpg',
                'tanggal_publikasi' => '2025-12-05',
                'tampilkan_di_kalender' => true,
                'status' => true
            ],
            
            [
                'kategori_artikel_id' => $kategoriUpacara->id,
                'judul' => 'Kuningan: Upacara Penyucian dan Peleburan Dosa',
                'slug' => 'kuningan-upacara-penyucian-dan-peleburan-dosa',
                'excerpt' => 'Hari Raya Kuningan dirayakan 10 hari setelah Galungan sebagai simbol penyucian dan peleburan dosa.',
                'konten' => '<h2>Makna Kuningan</h2>
<p>Hari Raya Kuningan dirayakan 10 hari setelah Galungan. Kata "Kuningan" berasal dari kata "Kuning" yang melambangkan kemakmuran dan kesucian. Pada hari ini, umat Hindu melakukan persembahan kepada leluhur dan dewa-dewa.</p>

<h3>Waktu Pelaksanaan</h3>
<p>Kuningan dirayakan setiap:</p>
<ul>
<li>Hari: Saniscara (Sabtu)</li>
<li>Wuku: Kuningan</li>
<li>Tilem: Sasih Kepitu</li>
<li>Tanggal: 10 hari setelah Galungan</li>
</ul>

<p>Kuningan mengajarkan tentang pentingnya penyucian diri dan hubungan harmonis dengan Tuhan, sesama, dan alam.</p>',
                'gambar' => 'artikel/kuningan.jpg',
                'tanggal_publikasi' => '2025-12-15',
                'tampilkan_di_kalender' => true,
                'status' => true
            ],
            
            [
                'kategori_artikel_id' => $kategoriUpacara->id,
                'judul' => 'Piodalan Pura Besakih: Puncak Karya Agung',
                'slug' => 'piodalan-pura-besakih-puncak-karya-agung',
                'excerpt' => 'Piodalan Pura Besakih merupakan perayaan terbesar di pura terpenting Bali yang dirayakan setiap 210 hari.',
                'konten' => '<h2>Piodalan Pura Besakih</h2>
<p>Piodalan Pura Besakih adalah perayaan odalan (hari ulang tahun pura) di Pura Besakih, pura terbesar dan terpenting di Bali. Perayaan ini diadakan setiap 210 hari sekali berdasarkan kalender Pawukon.</p>

<h3>Rangkaian Upacara</h3>
<p>Piodalan Pura Besakih meliputi:</p>
<ul>
<li><strong>Pemelastian</strong> - Penyucian pratima</li>
<li><strong>Penyineban</strong> - Puncak upacara</li>
<li><strong>Penyineban Agung</strong> - Upacara tingkat tinggi</li>
<li><strong>Ngelukat</strong> - Penyucian diri</li>
</ul>

<p>Ribuan umat Hindu dari seluruh Bali dan dunia berkumpul untuk merayakan Piodalan ini.</p>',
                'gambar' => 'artikel/pura-besakih.jpg',
                'tanggal_publikasi' => '2025-12-20',
                'tampilkan_di_kalender' => true,
                'status' => true
            ],

            // Travel Guide
            [
                'kategori_artikel_id' => $kategoriTravel->id,
                'judul' => 'Mengunjungi Pura-Pura Besar di Bali yang Penuh Cahaya Suci',
                'slug' => 'mengunjungi-pura-pura-besar-di-bali-yang-penuh-cahaya-suci',
                'excerpt' => 'Lupakan sejenak keramaian pantai Kuta dan Seminyak. Jelajahi pura-pura besar di Bali yang penuh dengan kedamaian dan cahaya suci.',
                'konten' => '<h2>Pura-Pura Besar di Bali</h2>
<p>Bali tidak hanya terkenal dengan pantainya yang indah, tetapi juga dengan pura-pura yang megah dan penuh makna spiritual. Berikut beberapa pura besar yang wajib dikunjungi:</p>

<h3>1. Pura Besakih</h3>
<p>Terletak di lereng Gunung Agung, Pura Besakih adalah pura terbesar dan tersuci di Bali.</p>

<h3>2. Pura Tanah Lot</h3>
<p>Pura yang terletak di atas batu karang di tengah laut. Pemandangan sunset di Pura Tanah Lot sangat terkenal.</p>

<h3>3. Pura Uluwatu</h3>
<p>Berada di tepi tebing setinggi 70 meter di atas permukaan laut.</p>

<p>Setiap pura memiliki energi spiritual yang berbeda. Rasakan kedamaian dan ketenangan saat mengunjungi tempat-tempat suci ini.</p>',
                'gambar' => 'artikel/pura-besar.jpg',
                'tanggal_publikasi' => '2025-11-12',
                'tampilkan_di_kalender' => false,
                'status' => true
            ],

            // Sejarah & Budaya
            [
                'kategori_artikel_id' => $kategoriSejarah->id,
                'judul' => 'Makna Tri Hita Karana dalam Sistem Irigasi Tradisional Bali',
                'slug' => 'makna-tri-hita-karana-dalam-sistem-irigasi-tradisional-bali',
                'excerpt' => 'Di balik keindahan sawah hijau terasering yang memukau, tersembunyi sebuah sistem irigasi kolektif Subak yang telah diakui UNESCO.',
                'konten' => '<h2>Sistem Subak Bali</h2>
<p>Subak adalah sistem irigasi tradisional Bali yang telah berusia lebih dari 1000 tahun. Sistem ini tidak hanya mengatur pembagian air, tetapi juga mencerminkan filosofi hidup masyarakat Bali.</p>

<h3>Tri Hita Karana</h3>
<p>Subak berlandaskan konsep Tri Hita Karana yang berarti tiga penyebab kebahagiaan:</p>
<ol>
<li><strong>Parahyangan</strong> - Hubungan harmonis dengan Tuhan</li>
<li><strong>Pawongan</strong> - Hubungan harmonis dengan sesama manusia</li>
<li><strong>Palemahan</strong> - Hubungan harmonis dengan alam</li>
</ol>

<p>Sistem Subak adalah contoh nyata bagaimana teknologi, agama, dan sosial budaya bisa bersinergi menciptakan keberlanjutan.</p>',
                'gambar' => 'artikel/subak.jpg',
                'tanggal_publikasi' => '2025-11-01',
                'tampilkan_di_kalender' => false,
                'status' => true
            ],

            [
                'kategori_artikel_id' => $kategoriSejarah->id,
                'judul' => 'Sejarah Pura di Bali: Dari Masa Kerajaan Hingga Kini',
                'slug' => 'sejarah-pura-di-bali-dari-masa-kerajaan-hingga-kini',
                'excerpt' => 'Pura-pura di Bali memiliki sejarah panjang yang terkait dengan perkembangan kerajaan-kerajaan Hindu di Nusantara.',
                'konten' => '<h2>Perkembangan Pura di Bali</h2>
<p>Sejarah pura di Bali dapat dibagi menjadi beberapa periode:</p>

<h3>1. Periode Pra-Hindu (Abad 1-4 M)</h3>
<p>Pada masa ini, masyarakat Bali menganut animisme dan dinamisme.</p>

<h3>2. Periode Hindu-Buddha (Abad 5-15 M)</h3>
<p>Pengaruh Hindu-Buddha dari India masuk ke Bali melalui Jawa.</p>

<h3>3. Periode Kerajaan Bali (Abad 10-19 M)</h3>
<p>Masa kejayaan kerajaan-kerajaan Bali seperti Warmadewa, Majapahit Bali, dan Gelgel.</p>

<p>Setiap pura menyimpan cerita dan nilai sejarah yang memperkaya warisan budaya Bali.</p>',
                'gambar' => 'artikel/sejarah-pura.jpg',
                'tanggal_publikasi' => '2025-10-15',
                'tampilkan_di_kalender' => false,
                'status' => true
            ],

            // Umum
            [
                'kategori_artikel_id' => $kategoriUmum->id,
                'judul' => 'Tata Cara Berpakaian Saat Berkunjung ke Pura',
                'slug' => 'tata-cara-berpakaian-saat-berkunjung-ke-pura',
                'excerpt' => 'Menghormati aturan berpakaian saat berkunjung ke pura adalah bentuk penghormatan terhadap budaya dan agama setempat.',
                'konten' => '<h2>Aturan Berpakaian di Pura</h2>
<p>Berkunjung ke pura memerlukan perhatian khusus terhadap pakaian yang dikenakan.</p>

<h3>Untuk Pria:</h3>
<ul>
<li>Kemeja atau baju berkerah</li>
<li>Celana panjang</li>
<li>Sarung yang dililitkan di pinggang</li>
</ul>

<h3>Untuk Wanita:</h3>
<ul>
<li>Kebaya atau baju sopan</li>
<li>Kain panjang</li>
<li>Selendang diselempangkan di bahu</li>
</ul>

<p>Dengan berpakaian yang tepat, kita menunjukkan rasa hormat dan ikut melestarikan tradisi.</p>',
                'gambar' => 'artikel/pakaian-pura.jpg',
                'tanggal_publikasi' => '2025-10-01',
                'tampilkan_di_kalender' => false,
                'status' => true
            ]
        ];

        $count = 0;
        foreach ($artikels as $artikel) {
            $existing = Artikel::where('slug', $artikel['slug'])->first();
            if (!$existing) {
                Artikel::create($artikel);
                $count++;
                echo "✓ Artikel '{$artikel['judul']}' berhasil dibuat.\n";
            } else {
                echo "⚠ Artikel '{$artikel['judul']}' sudah ada, dilewati.\n";
            }
        }

        echo "\n✅ ArtikelSeeder selesai!\n";
        echo "📊 Total artikel dibuat: $count\n";
        echo "📊 Total artikel di database: " . Artikel::count() . "\n";
    }
}