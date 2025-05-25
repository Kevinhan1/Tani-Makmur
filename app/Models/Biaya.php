<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    protected $table = 'tbiaya';
    protected $primaryKey = 'nobiaya';
    public $incrementing = false; // karena primary key string
    protected $keyType = 'string';

    // Matikan fitur timestamps (created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'nobiaya',
        'tanggal',
        'koderekening',
        'keterangan',
        'total',
        'kodepengguna', 
    ];

    // Relasi ke rekening (jika ada)
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'koderekening', 'koderekening');
    }
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'kodepengguna', 'kodepengguna');
    }

}
