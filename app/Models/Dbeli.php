<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dbeli extends Model
{
    protected $table = 'tdbeli';
    protected $primaryKey = 'noref';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'noref',
        'notabeli',
        'kodebarang',
        'nodo',
        'qty',
        'qtyjual',
        'hargabeli'
    ];
    
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodebarang', 'kodebarang');
    }
    public function header()
    {
        return $this->belongsTo(Hbeli::class, 'notabeli', 'notabeli');
    }
}
