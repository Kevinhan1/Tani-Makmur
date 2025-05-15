<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'tpelanggan';
    protected $primaryKey = 'kodepelanggan';
    public $incrementing = false;
    protected $keyType = 'string';
				public $timestamps = false;
    protected $fillable = [
        'kodepelanggan', 'namapelanggan', 'namakios', 'alamat', 'kelurahan',
        'kecamatan', 'kota', 'ktp', 'npwp', 'nitku', 'aktif'
    ];
}
