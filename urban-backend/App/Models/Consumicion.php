<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumicion extends Model
{
    protected $table = 'consumicion'; // Nombre de la tabla
    public $timestamps = false; // No utiliza las columnas created_at y updated_at

    // No se define una clave primaria explícita, pero puedes personalizar métodos como `save` si es necesario.
    protected $primaryKey = null; // Se utiliza clave compuesta
    public $incrementing = false; // Claves primarias no autoincrementables

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'item',
        'id_agendamiento',
        'producto',
        'cantidad',
        'precio_venta'
    ];

    // Relación con la tabla de agendamientos
    public function agendamiento()
    {
        return $this->belongsTo(AgendamientoCab::class, 'id_agendamiento', 'id_agendamiento');
    }

    // Relación con la tabla de productos
    public function productoInfo()
    {
        return $this->belongsTo(Producto::class, 'producto', 'id_producto');
    }
}
