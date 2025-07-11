<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPagos extends Model
{
    use HasFactory;

    protected $table = 'tipopago'; // Define el nombre correcto de la tabla

    protected $fillable = [
        'nombre',
        'estado',
    ];
}
