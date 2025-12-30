<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_artikel_id',
        'judul',
        'slug',
        'excerpt',
        'konten',
        'gambar',
        'tanggal_publikasi',
        'tampilkan_di_kalender',
        'status'
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'tampilkan_di_kalender' => 'boolean',
        'status' => 'boolean'
    ];

    // RELASI
    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_artikel_id');
    }

    // ACCESSOR: formatted_date
    public function getFormattedDateAttribute()
    {
        if (!$this->tanggal_publikasi) return '';
        
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tanggal = Carbon::parse($this->tanggal_publikasi);
        return $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n') - 1] . ' ' . $tanggal->format('Y');
    }

    // ACCESSOR: date_for_calender
    public function getDateForCalendarAttribute()
    {
        if (!$this->tanggal_publikasi) return '';
        
        $tanggal = Carbon::parse($this->tanggal_publikasi);
        return $tanggal->format('d-m-Y');
    }

    // METHOD: Artikel terkait
    public function artikelTerkait($limit = 3)
    {
        return Artikel::with('kategori')
            ->where('kategori_artikel_id', $this->kategori_artikel_id)
            ->where('id', '!=', $this->id)
            ->where('status', true)
            ->latest()
            ->limit($limit)
            ->get();
    }
}