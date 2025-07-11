<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Define el nombre correcto de la tabla

    public function productos() {
        return $this->hasMany(Productos::class, 'id_categoria');
    }

    protected $fillable = [
        'nombre',
        'estado',
    ];
}
