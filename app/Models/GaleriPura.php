<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriPura extends Model
{
    use HasFactory;


    protected $table = 'galeri_puras';

    protected $fillable = [
        'pura_id',
        'foto',
    ];
}
