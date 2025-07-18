<?php

// app/Models/MutasiRekening.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiRekening extends Model
{
    protected $table = 'tmutasirekening';
    public $timestamps = false;

        protected $fillable = [
        'tanggal',
        'koderekening',
        'noreferensi',
        'nogenerate',
        'jenis',
        'keterangan',
        'masuk',
        'keluar',
    ];
    
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'koderekening', 'koderekening');
    }

}


