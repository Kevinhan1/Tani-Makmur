<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Djual extends Model
{
    protected $table = 'tdjual';
    public $timestamps = false;

    protected $fillable = [
        'notajual',
        'no',
        'noref',
        'qty',
        'hargajual',
    ];

    public function header()
    {
        return $this->belongsTo(Hjual::class, 'notajual', 'notajual');
    }

        public function detailBeli()
    {
        return $this->belongsTo(Dbeli::class, 'noref', 'noref');
    }
}
