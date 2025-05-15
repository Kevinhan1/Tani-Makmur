<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'trekening';
    protected $primaryKey = 'koderekening';
    public $incrementing = false; // karena kode format R-001 bukan auto increment numerik
    protected $keyType = 'string';

    protected $fillable = [
        'koderekening',
        'namarekening',
        'saldo',
        'aktif',
    ];

    public $timestamps = false; // kalau tabel tidak ada kolom created_at dan updated_at
}
