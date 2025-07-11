<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
    use HasFactory;

    protected $table = 'documento'; // Define el nombre correcto de la tabla

    protected $fillable = [
        'nombre',
        'estado',
    ];
}
