<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alertas extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'titulo',
        'mensaje',
        'leido',
    ];
}
