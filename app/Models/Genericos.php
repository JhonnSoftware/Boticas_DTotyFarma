<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genericos extends Model
{
    use HasFactory;

    protected $table = 'genericos';

    public function productos() {
        return $this->hasMany(Productos::class, 'id_generico');
    }

    protected $fillable = [
        'nombre',
        'estado',
    ];
}
