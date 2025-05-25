<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PindahSaldo extends Model
{
    use HasFactory;

    protected $table = 'tpindahbuku';

    protected $primaryKey = 'nopindahbuku';

    public $incrementing = false; // Karena primary key berupa string bukan auto increment integer
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nopindahbuku',
        'tanggal',
        'koderekeningasal',
        'koderekeningtujuan',
        'keterangan',
        'total',
        'kodepengguna',
    ];

    // Relasi ke Rekening Asal
    public function rekeningAsal()
    {
        return $this->belongsTo(Rekening::class, 'koderekeningasal', 'koderekening');
    }

    // Relasi ke Rekening Tujuan
    public function rekeningTujuan()
    {
        return $this->belongsTo(Rekening::class, 'koderekeningtujuan', 'koderekening');
    }

    // Relasi ke Pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'kodepengguna', 'kodepengguna');
    }
}
