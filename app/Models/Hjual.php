<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hjual extends Model
{
    protected $table = 'thjual';
    protected $primaryKey = 'notajual';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'notajual',
        'kodepelanggan',
        'tanggal',
        'total',
        'totalbayar',
        'nopol',
        'supir',
        'kodepengguna',
    ];

    public function detail()
    {
        return $this->hasMany(Djual::class, 'notajual', 'notajual');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kodepelanggan', 'kodepelanggan');
    }
}
