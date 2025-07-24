<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $fillable = [
        'id_producto',
        'tipo_movimiento',
        'origen',
        'documento_ref',
        'fecha',
        'cantidad',
        'stock_anterior',
        'stock_actual',
        'observacion',
        'usuario_id',
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id_producto');
    }

    /**
     * Relación con el modelo User
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
