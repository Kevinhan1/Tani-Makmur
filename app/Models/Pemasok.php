<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    // Nama tabel
    protected $table = 'tpemasok';

    // Primary key jika bukan id dan bukan auto increment (kodepemasok varchar)
    protected $primaryKey = 'kodepemasok';

    // Karena primary key bukan auto increment integer, set false
    public $incrementing = false;

    // Jika primary key string, set tipe key ke string
    protected $keyType = 'string';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'kodepemasok',
        'namapemasok',
        'alamatpemasok',
        'aktif'
    ];

    // Jika pakai timestamp, defaultnya true
    public $timestamps = false;
}
