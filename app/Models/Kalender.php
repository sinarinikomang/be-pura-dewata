<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Kalender extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pura_id',
        'kategori_artikel_id',
        'nama_upacara',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'wewaran',
        'pawukon',
        'status',
        'catatan_admin'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    // RELASI
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pura()
    {
        return $this->belongsTo(Pura::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_artikel_id');
    }

    // ACCESSOR: formatted_date
    public function getFormattedDateAttribute()
    {
        if (!$this->tanggal_mulai) return '';
        
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $tanggal = Carbon::parse($this->tanggal_mulai);
        return $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n') - 1] . ' ' . $tanggal->format('Y');
    }

    // ACCESSOR: date_for_calender
    public function getDateForCalendarAttribute()
    {
        if (!$this->tanggal_mulai) return '';
        
        $tanggal = Carbon::parse($this->tanggal_mulai);
        return $tanggal->format('d-m-Y');
    }
}