<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model {
    protected $table = 'productos';  // Asegúrate de que el nombre de la tabla sea correcto
    protected $primaryKey = 'id_producto';    // Especifica la clave primaria si es necesario
    public $timestamps = false;      // Si no usas columnas de timestamps, desactívalas

    // Lista de columnas que se pueden rellenar en el modelo
    protected $fillable = [
        'nombre', 'precio_venta', 
        'porcentaje_iva', 'estado', 'tipo' 
    ];
}
