<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendamientoDet extends Model
{
    protected $table = 'agendamientos_det';
    protected $primaryKey = ['item', 'id_agendamiento']; // Clave primaria compuesta
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'item',
        'id_agendamiento',
        'jugador'
    ];
}
