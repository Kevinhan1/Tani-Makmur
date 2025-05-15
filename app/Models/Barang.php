<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tbarang';
    protected $primaryKey = 'kodebarang';
    public $incrementing = false; // karena kode barang seperti B-001
    public $timestamps = false;

    protected $fillable = [
        'kodebarang', 'namabarang', 'hbeli', 'hjual', 'konversi', 'aktif'
    ];
}
