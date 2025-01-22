<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';    // Especifica la clave primaria si es necesario
    public $timestamps = false; 
    
        // Lista de columnas que se pueden rellenar en el modelo
        protected $fillable = [
            'nombre', 'estado', 
            'contrasena' 
        ];
    
}
