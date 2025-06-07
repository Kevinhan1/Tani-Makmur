<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BayarBeli extends Model
{
    protected $table = 'tdbayarbeli';
    public $timestamps = false;

    protected $fillable = [
        'notabeli',
        'no',
        'tanggal',
        'koderekening',
        'total',
        'kodepengguna'
    ];
}
