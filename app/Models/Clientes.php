<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    public function ventas()
    {
        return $this->hasMany(Ventas::class, 'id_cliente'); 
    }

    protected $fillable = ['dni', 'nombre', 'apellidos', 'telefono', 'direccion', 'estado'];

    protected $table = 'clientes';
    
}
