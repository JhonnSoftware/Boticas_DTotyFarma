<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentas extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = ['id_venta', 'id_producto', 'cantidad', 'precio', 'sub_total'];

    public function venta() {
        return $this->belongsTo(Ventas::class, 'id_venta');
    }

    public function producto() {
        return $this->belongsTo(Productos::class, 'id_producto');
    }
}
