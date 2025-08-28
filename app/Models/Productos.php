<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
    }

    public function categorias()
    {
        return $this->belongsToMany(
            \App\Models\Categorias::class,
            'categoria_producto',   // tabla pivote
            'id_producto',          // clave local en pivote
            'id_categoria'          // clave relacionada en pivote
        )->withTimestamps();
    }


    public function clase()
    {
        return $this->belongsTo(Clases::class, 'id_clase');
    }

    public function generico()
    {
        return $this->belongsTo(Genericos::class, 'id_generico');
    }

    public function alertas()
    {
        return $this->hasMany(\App\Models\Alertas::class, 'id_producto');
    }


    public function detalleVentas()
    {
        return $this->hasMany(DetalleVentas::class, 'id_producto'); // Asegúrate que 'id_producto' es la clave foránea en 'detalleventas'
    }

    protected $fillable = [
        'codigo',
        'descripcion',
        'presentacion',
        'laboratorio',
        'lote',
        'fecha_vencimiento',

        // conversión y stock (en UNIDADES)
        'unidades_por_blister',
        'unidades_por_caja',
        'cantidad',
        'cantidad_blister',
        'cantidad_caja',
        'stock_minimo',
        'stock_minimo_blister',
        'stock_minimo_caja',

        // precios y descuento
        'descuento',
        'descuento_blister',
        'descuento_caja',
        'precio_compra',
        'precio_compra_blister',
        'precio_compra_caja',
        'precio_venta',
        'precio_venta_blister',
        'precio_venta_caja',

        // medios
        'foto',

        // relaciones
        'id_proveedor',
        'id_clase',
        'id_generico',

        'estado',
    ];
}
