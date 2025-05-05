<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'tpengguna';
    public $timestamps = false;

    protected $fillable = [
        'namapengguna',
        'katakunci',
        'status',
        'tipe',
    ];
}
