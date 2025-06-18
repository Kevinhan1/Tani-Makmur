<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dbayarbeli extends Model
{
    protected $table = 'tdbayarbeli';
    protected $primaryKey = 'no';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no', 'notabeli', 'tanggal', 'koderekening', 'total', 'kodepengguna'
    ];

    public function rekening()
    {
        return $this->belongsTo(rekening::class, 'koderekening', 'koderekening');
    }

    public function nota()
    {
        return $this->belongsTo(hbeli::class, 'notabeli', 'notabeli');
    }
}
