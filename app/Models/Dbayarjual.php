<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dbayarjual extends Model
{
    // Nama tabel secara eksplisit
    protected $table = 'tdbayarjual';

    // Primary key
    protected $primaryKey = 'no';

    // Tidak menggunakan timestamps (created_at & updated_at)
    public $timestamps = false;

    // Agar primary key bertipe string dan bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi
    protected $fillable = [
        'no',
        'notajual',
        'tanggal',
        'koderekening',
        'total',
        'kodepengguna'
    ];

    // Relasi ke header penjualan (hjual)
    public function penjualan()
    {
        return $this->belongsTo(Hjual::class, 'notajual', 'notajual');
    }

    // Relasi ke rekening
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'koderekening', 'koderekening');
    }

    // Relasi ke pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'kodepengguna', 'kodepengguna');
    }
}
