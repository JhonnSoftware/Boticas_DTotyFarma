<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Define el nombre correcto de la tabla

    public function productos()
    {
        return $this->belongsToMany(
            \App\Models\Productos::class,
            'categoria_producto',   // tabla pivote
            'id_categoria',         // clave local en pivote
            'id_producto'           // clave relacionada en pivote
        )->withTimestamps();
    }


    protected $fillable = [
        'nombre',
        'estado',
    ];
}
