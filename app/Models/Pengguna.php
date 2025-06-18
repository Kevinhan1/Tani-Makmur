<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'tpengguna';
    protected $primaryKey = 'kodepengguna';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'aktif',
        'kodepengguna',
        'namapengguna',
        'katakunci',
        'status',
    ];
}
