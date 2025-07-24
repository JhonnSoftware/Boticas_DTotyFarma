<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'codigo',
        'id_proveedor',
        'total',
        'igv',
        'fecha',
        'estado',
        'id_pago',
        'id_documento',
        'usuario_id',
        'archivo_factura',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'id_proveedor');
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
        return $this->hasMany(DetalleCompras::class, 'id_compra');
    }
}
