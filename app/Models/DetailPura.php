<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPura extends Model
{
  protected $fillable = [
    'pura_id',
    'deskripsi_singkat',
    'sejarah',
    'lokasi_iframe'
];

    public function pura() {
        return $this->belongsTo(Pura::class);
    }
}