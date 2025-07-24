<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use HasFactory;

    protected $table = 'permisos'; // Especificamos la tabla porque el modelo está en plural

    protected $fillable = ['usuario_id', 'modulo'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
