<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\TipoPagoController;
use App\Http\Controllers\ProductoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/plantilla', function () {
    return view('home');
});

Route::controller(UsuarioController::class)->group(function () {
    Route::get('usuarios', 'index')->name('usuarios.index');
    Route::get('usuarios/buscar', 'buscar')->name('usuarios.buscar');
    Route::post('usuarios', 'store')->name('usuarios.store');
    Route::put('usuarios/{id}', 'actualizar')->name('usuarios.actualizar');
    Route::delete('usuarios/{id}', 'destroy')->name('usuarios.destroy');
});

Route::controller(ClienteController::class)->group(function () {
    Route::get('clientes', 'index')->name('clientes.index');
    Route::post('clientes', 'store')->name('clientes.store');
    Route::get('clientes/buscar', 'buscar')->name('clientes.buscar');
    Route::put('clientes/{id}/desactivar', 'desactivar')->name('clientes.desactivar');
    Route::put('clientes/{id}/activar', 'activar')->name('clientes.activar');
    Route::put('clientes/{id}', 'actualizar')->name('clientes.actualizar');
});

Route::controller(CategoriaController::class)->group(function () {
    Route::get('categorias', 'index')->name('categorias.index');
    Route::post('categorias', 'store')->name('categorias.store');
    Route::get('categorias/buscar', 'buscar')->name('categorias.buscar');
    Route::put('categorias/{id}/desactivar', 'desactivar')->name('categorias.desactivar');
    Route::put('categorias/{id}/activar', 'activar')->name('categorias.activar');
    Route::put('categorias/{id}', 'actualizar')->name('categorias.actualizar');
});

Route::controller(ProveedorController::class)->group(function () {
    Route::get('proveedores', 'index')->name('proveedores.index');
    Route::post('proveedores', 'store')->name('proveedores.store');
    Route::get('proveedores/buscar', 'buscar')->name('proveedores.buscar');
    Route::put('proveedores/{id}/desactivar', 'desactivar')->name('proveedores.desactivar');
    Route::put('proveedores/{id}/activar', 'activar')->name('proveedores.activar');
    Route::put('proveedores/{id}', 'actualizar')->name('proveedores.actualizar');
});

Route::controller(DocumentoController::class)->group(function () {
    Route::get('documentos', 'index')->name('documentos.index');
    Route::post('documentos', 'store')->name('documentos.store');
    Route::get('documentos/buscar', 'buscar')->name('documentos.buscar');
    Route::put('documentos/{id}/desactivar', 'desactivar')->name('documentos.desactivar');
    Route::put('documentos/{id}/activar', 'activar')->name('documentos.activar');
    Route::put('documentos/{id}', 'actualizar')->name('documentos.actualizar');
});

Route::controller(TipoPagoController::class)->group(function () {
    Route::get('tipopagos', 'index')->name('tipopagos.index');
    Route::post('tipopagos', 'store')->name('tipopagos.store');
    Route::get('tipopagos/buscar', 'buscar')->name('tipopagos.buscar');
    Route::put('tipopagos/{id}/desactivar', 'desactivar')->name('tipopagos.desactivar');
    Route::put('tipopagos/{id}/activar', 'activar')->name('tipopagos.activar');
    Route::put('tipopagos/{id}', 'actualizar')->name('tipopagos.actualizar');
});

Route::controller(ProductoController::class)->group(function () {
    Route::get('productos', 'index')->name('productos.index');
    Route::post('productos', 'store')->name('productos.store');
    Route::get('productos/buscar', 'buscar')->name('productos.buscar');
    Route::put('productos/{id}/desactivar', 'desactivar')->name('productos.desactivar');
    Route::put('productos/{id}/activar', 'activar')->name('productos.activar');
    Route::put('productos/{id}', 'actualizar')->name('productos.actualizar');
});




