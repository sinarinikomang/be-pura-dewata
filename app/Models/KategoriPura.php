<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPura extends Model
{
    use HasFactory;

    
    protected $table = 'kategori_puras';

    protected $fillable = ['nama_kategori', 'slug'];

    public function pura()
    {
        return $this->hasMany(Pura::class, 'kategori_pura_id');
    }
}

