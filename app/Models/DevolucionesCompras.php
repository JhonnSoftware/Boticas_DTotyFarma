<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionesCompras extends Model
{
    use HasFactory;

    protected $table = 'devoluciones_compras';

    protected $fillable = ['id_compra', 'id_producto', 'usuario_id', 'cantidad', 'motivo', 'fecha'];

    public function compra()
    {
        return $this->belongsTo(Compras::class, 'id_compra');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id_producto');
    }
}
