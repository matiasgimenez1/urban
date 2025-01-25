<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumicion extends Model
{
    protected $table = 'consumicion'; // Nombre de la tabla
    public $timestamps = false; // No utiliza las columnas created_at y updated_at

    // No se define una clave primaria explícita, pero puedes personalizar métodos como `save` si es necesario.
    //protected $primaryKey = null; // Se utiliza clave compuesta
    public $incrementing = false; // Claves primarias no autoincrementables
    protected $primaryKey = ['item', 'id_agendamiento']; // Clave primaria compuesta

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'item',
        'id_agendamiento',
        'producto',
        'cantidad',
        'precio_venta',
        'id_item',
    ];

    // public function detalles()
    // {
    //     return $this->hasMany(Consumicion::class, 'id_agendamiento', 'item')
    //                 ->where('item', $this->item);
    // }
 
      // Relación con los detalles
      public function detalles()
      {
          return $this->hasMany(Consumicion::class, 'id_agendamiento', 'id_agendamiento');
      }
}
