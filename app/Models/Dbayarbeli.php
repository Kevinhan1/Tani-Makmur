<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dbayarbeli extends Model // D besar!
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
        return $this->belongsTo(Rekening::class, 'koderekening', 'koderekening');
    }

    public function nota()
    {
        return $this->belongsTo(Hbeli::class, 'notabeli', 'notabeli');
    }
}
