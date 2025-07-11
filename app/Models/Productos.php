<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    public function proveedor() {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
    }

    public function categoria() {
        return $this->belongsTo(Categorias::class, 'id_categoria');
    }

    protected $fillable = [
        'codigo', 
        'descripcion', 
        'presentacion',
        'laboratorio', 
        'lote', 
        'cantidad',
        'stock_minimo',
        'descuento',
        'fecha_vencimiento',
        'precio_compra', 
        'precio_venta', 
        'foto',
        'id_proveedor',
        'id_categoria',
        'estado'
    ];

}
