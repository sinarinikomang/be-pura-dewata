<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriArtikel extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori', 'slug'];

    // Relasi: Satu kategori artikel memiliki banyak Artikel
    public function artikels()
    {
        return $this->hasMany(Artikel::class);
    }
}