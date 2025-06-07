<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hbeli extends Model
{
    protected $table = 'thbeli';
    protected $primaryKey = 'notabeli';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'notabeli',
        'tanggal',
        'kodepemasok',
        'total',
        'totalbayar',
        'kodepengguna'
    ];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'kodepemasok', 'kodepemasok');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'kodepengguna', 'kodepengguna');
    }

    public function detail()
    {
        return $this->hasMany(Dbeli::class, 'notabeli', 'notabeli');
    }
}
