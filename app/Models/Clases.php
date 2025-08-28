<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clases extends Model
{
    use HasFactory;

    protected $table = 'clases';

    public function productos() {
        return $this->hasMany(Productos::class, 'id_clase');
    }

    protected $fillable = [
        'nombre',
        'estado',
    ];
}
