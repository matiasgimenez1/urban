<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model {
    protected $table = 'solicitudes';  // Asegúrate de que el nombre de la tabla sea correcto
    protected $primaryKey = 'id_solicitud';    // Especifica la clave primaria si es necesario
    public $timestamps = false;      // Si no usas columnas de timestamps, desactívalas

    // Lista de columnas que se pueden rellenar en el modelo
    protected $fillable = [
        'nombre_cliente', 'fecha_agenda', 
        'hora_inicio', 'hora_fin', 
        'cant_horas', 'telefono', 
        'fecha_registro', 'estado_reserva'
    ];
}
