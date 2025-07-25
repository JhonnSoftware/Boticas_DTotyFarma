<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionesVentas extends Model
{
    use HasFactory;

    protected $table = 'devoluciones_ventas';

    protected $fillable = ['id_venta', 'id_producto', 'usuario_id', 'cantidad', 'motivo', 'fecha'];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'id_venta');
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
