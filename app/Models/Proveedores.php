<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;

    protected $table = 'proveedores'; // Define el nombre correcto de la tabla

    public function productos() {
        return $this->hasMany(Productos::class, 'id_proveedor');
    }

    protected $fillable = [
        'ruc',
        'nombre',
        'telefono',
        'correo',
        'direccion',
        'contacto',
        'estado',
    ];
}
