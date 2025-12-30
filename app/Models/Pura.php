<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pura extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pura',
        'slug',
        'gambar_card',
        'deskripsi_singkat',
        'sejarah',
        'lokasi_iframe',
        'kabupaten_id',
        'kategori_pura_id'
    ];

    // Tambah properti ini untuk eager loading
    protected $with = ['kabupaten', 'kategoriPura', 'galeri'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pura) {
            if (empty($pura->slug)) {
                $pura->slug = Str::slug($pura->nama_pura);
            }
        });
        
        static::updating(function ($pura) {
            if ($pura->isDirty('nama_pura')) {
                $pura->slug = Str::slug($pura->nama_pura);
            }
        });
    }

    // Relasi
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kategoriPura()
    {
        return $this->belongsTo(KategoriPura::class, 'kategori_pura_id');
    }

    public function galeri()
    {
        return $this->hasMany(GaleriPura::class)->orderBy('created_at', 'asc');
    }
}