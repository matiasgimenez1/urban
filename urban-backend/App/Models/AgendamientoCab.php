<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendamientoCab extends Model
{
    protected $table = 'agendamientos_cab'; // Nombre de la tabla
    protected $primaryKey = 'id_agendamiento'; // Clave primaria
    public $timestamps = false; // Si no usas created_at y updated_at

    protected $fillable = [
        'id_agendamiento', // Agregado aquí
        'id_partida',
        'fecha',
        'hora_ini',
        'hora_fin',
        'estado',
        'usuario_agenda'
    ];
    
    // Relación con los detalles
    public function detalles()
    {
        return $this->hasMany(AgendamientoDet::class, 'id_agendamiento', 'id_agendamiento');
    }
}
