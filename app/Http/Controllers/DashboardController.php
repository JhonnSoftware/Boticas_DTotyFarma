<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard'); // asegúrate de tener 'resources/views/dashboard.blade.php'
    }
}
