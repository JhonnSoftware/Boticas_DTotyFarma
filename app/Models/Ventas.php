<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'codigo',
        'id_cliente',
        'total',
        'igv',
        'descuento_total',
        'fecha',
        'estado',
        'id_pago',
        'id_documento',
        'usuario_id',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function documento()
    {
        return $this->belongsTo(Documentos::class, 'id_documento');
    }

    public function pago()
    {
        return $this->belongsTo(TipoPagos::class, 'id_pago');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVentas::class, 'id_venta');
    }
}
