<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cajas extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'monto_apertura',
        'fecha_apertura',
        'monto_cierre',
        'fecha_cierre',
        'estado',
        'usuario_id',
    ];
    
    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
