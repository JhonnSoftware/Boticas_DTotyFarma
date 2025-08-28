<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alertas extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'referencia',
        'id_producto',
        'titulo',
        'mensaje',
        'leido',
    ];

    public function producto()
    {
        return $this->belongsTo(\App\Models\Productos::class, 'id_producto');
    }
}
