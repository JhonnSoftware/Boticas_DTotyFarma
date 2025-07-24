<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimientos;

class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimientos::with('producto')->orderBy('fecha', 'desc')->get();
        return view('movimientos.index', compact('movimientos'));
    }
}
