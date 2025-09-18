<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\GenericoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\TipoPagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\DevolucionesVentasController;
use App\Http\Controllers\DevolucionesComprasController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\AlertaController;

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
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {


    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard.index');
        Route::get('datosEmpresa', 'datosEmpresa')->name('dashboard.datosEmpresa');
    });

    Route::middleware('permiso:usuarios')->controller(UsuarioController::class)->group(function () {
        Route::get('usuarios', 'index')->name('usuarios.index');
        Route::get('usuarios/buscar', 'buscar')->name('usuarios.buscar');
        Route::post('usuarios', 'store')->name('usuarios.store');
        Route::put('usuarios/{id}', 'actualizar')->name('usuarios.actualizar');
        Route::delete('usuarios/{id}', 'destroy')->name('usuarios.destroy');
        Route::get('permisos', 'permisos')->name('usuarios.permisos');
        Route::post('permisos', 'guardarPermisos')->name('usuarios.permisos.guardar');
    });

    Route::middleware('permiso:clientes')->controller(ClienteController::class)->group(function () {
        Route::get('clientes', 'index')->name('clientes.index');
        Route::post('clientes', 'store')->name('clientes.store');
        Route::get('clientes/buscar', 'buscar')->name('clientes.buscar');
        Route::put('clientes/{id}/desactivar', 'desactivar')->name('clientes.desactivar');
        Route::put('clientes/{id}/activar', 'activar')->name('clientes.activar');
        Route::put('clientes/{id}', 'actualizar')->name('clientes.actualizar');
        Route::get('clientes/exportar/{formato}', 'exportar')->name('clientes.exportar');
    });

    Route::middleware('permiso:categorias')->controller(CategoriaController::class)->group(function () {
        Route::get('categorias', 'index')->name('categorias.index');
        Route::post('categorias', 'store')->name('categorias.store');
        Route::get('categorias/buscar', 'buscar')->name('categorias.buscar');
        Route::put('categorias/{id}/desactivar', 'desactivar')->name('categorias.desactivar');
        Route::put('categorias/{id}/activar', 'activar')->name('categorias.activar');
        Route::put('categorias/{id}', 'actualizar')->name('categorias.actualizar');
    });

    Route::middleware('permiso:clases')->controller(ClaseController::class)->group(function () {
        Route::get('clases', 'index')->name('clases.index');
        Route::post('clases', 'store')->name('clases.store');
        Route::get('clases/buscar', 'buscar')->name('clases.buscar');
        Route::put('clases/{id}/desactivar', 'desactivar')->name('clases.desactivar');
        Route::put('clases/{id}/activar', 'activar')->name('clases.activar');
        Route::put('clases/{id}', 'actualizar')->name('clases.actualizar');
    });

    Route::middleware('permiso:genericos')->controller(GenericoController::class)->group(function () {
        Route::get('genericos', 'index')->name('genericos.index');
        Route::post('genericos', 'store')->name('genericos.store');
        Route::get('genericos/buscar', 'buscar')->name('genericos.buscar');
        Route::put('genericos/{id}/desactivar', 'desactivar')->name('genericos.desactivar');
        Route::put('genericos/{id}/activar', 'activar')->name('genericos.activar');
        Route::put('genericos/{id}', 'actualizar')->name('genericos.actualizar');
        Route::get('genericos/{id}/edit-partial', 'editPartial')->name('genericos.edit-partial');
    });

    Route::middleware('permiso:proveedores')->controller(ProveedorController::class)->group(function () {
        Route::get('proveedores', 'index')->name('proveedores.index');
        Route::post('proveedores', 'store')->name('proveedores.store');
        Route::get('proveedores/buscar', 'buscar')->name('proveedores.buscar');
        Route::put('proveedores/{id}/desactivar', 'desactivar')->name('proveedores.desactivar');
        Route::put('proveedores/{id}/activar', 'activar')->name('proveedores.activar');
        Route::put('proveedores/{id}', 'actualizar')->name('proveedores.actualizar');
        Route::get('proveedores/exportar/{formato}', 'exportar')->name('proveedores.exportar');
    });

    Route::middleware('permiso:documentos')->controller(DocumentoController::class)->group(function () {
        Route::get('documentos', 'index')->name('documentos.index');
        Route::post('documentos', 'store')->name('documentos.store');
        Route::get('documentos/buscar', 'buscar')->name('documentos.buscar');
        Route::put('documentos/{id}/desactivar', 'desactivar')->name('documentos.desactivar');
        Route::put('documentos/{id}/activar', 'activar')->name('documentos.activar');
        Route::put('documentos/{id}', 'actualizar')->name('documentos.actualizar');
    });

    Route::middleware('permiso:tipopagos')->controller(TipoPagoController::class)->group(function () {
        Route::get('tipopagos', 'index')->name('tipopagos.index');
        Route::post('tipopagos', 'store')->name('tipopagos.store');
        Route::get('tipopagos/buscar', 'buscar')->name('tipopagos.buscar');
        Route::put('tipopagos/{id}/desactivar', 'desactivar')->name('tipopagos.desactivar');
        Route::put('tipopagos/{id}/activar', 'activar')->name('tipopagos.activar');
        Route::put('tipopagos/{id}', 'actualizar')->name('tipopagos.actualizar');
    });

    Route::middleware('permiso:productos')->controller(ProductoController::class)->group(function () {
        Route::get('productos', 'index')->name('productos.index');
        Route::post('productos', 'store')->name('productos.store');
        Route::get('productos/buscar', 'buscar')->name('productos.buscar');
        Route::put('productos/{id}/desactivar', 'desactivar')->name('productos.desactivar');
        Route::put('productos/{id}/activar', 'activar')->name('productos.activar');
        Route::put('productos/{id}', 'actualizar')->name('productos.actualizar');
        Route::get('productos/{id}/edit-partial', 'editPartial')->name('productos.edit-partial');
        Route::get('detalleProductos', 'detalle')->name('productos.detalle');
        Route::get('tablaProductos', 'tablaProductos')->name('productos.tablaProductos');
        Route::get('productos/exportar/{formato}', 'exportar')->name('productos.exportar');
    });

    Route::middleware('permiso:ventas')->controller(VentasController::class)->group(function () {
        Route::get('ventas', 'index')->name('ventas.index');
        Route::post('ventas', 'store')->name('ventas.store');
        Route::get('historial_ventas', 'historial')->name('ventas.historial');
        Route::get('ventas/voucher/{id}', 'voucher')->name('ventas.voucher');
        Route::get('ventas/buscar', 'buscar')->name('ventas.buscar');
        Route::get('ventas/exportar/{formato}', 'exportar')->name('ventas.exportar');
    });

    Route::middleware('permiso:compras')->controller(ComprasController::class)->group(function () {
        Route::get('compras', 'index')->name('compras.index');
        Route::post('compras', 'store')->name('compras.store');
        Route::get('historial_compras', 'historial')->name('compras.historial');
        Route::get('compras/buscar', 'buscar')->name('compras.buscar');
        Route::get('compras/exportar/{formato}', 'exportar')->name('compras.exportar');
    });

    Route::middleware('permiso:devolucionesVentas')->controller(DevolucionesVentasController::class)->group(function () {
        Route::get('devoluciones', 'index')->name('devoluciones.index');
        Route::post('devoluciones', 'store')->name('devoluciones.store');
        Route::get('devoluciones/buscar', 'buscar')->name('devoluciones.buscar');
        Route::get('devoluciones/exportar/{formato}', 'exportar')->name('devoluciones.exportar');
    });

    Route::middleware('permiso:devolucionesCompras')->controller(DevolucionesComprasController::class)->group(function () {
        Route::get('devolucionesCompras', 'index')->name('devolucionesCompras.index');
        Route::post('devolucionesCompras', 'store')->name('devolucionesCompras.store');
        Route::get('devolucionesCompras/buscar', 'buscar')->name('devolucionesCompras.buscar');
        Route::get('devolucionesCompras/exportar/{formato}', 'exportar')->name('devolucionesCompras.exportar');
    });

    Route::middleware('permiso:movimientos')->controller(MovimientoController::class)->group(function () {
        Route::get('movimientos', 'index')->name('movimientos.index');
    });

    Route::middleware('permiso:cajas')->controller(CajaController::class)->group(function () {
        Route::get('apertura', 'aperturaForm')->name('caja.apertura.form');
        Route::post('apertura', 'aperturaStore')->name('caja.apertura.store');
        Route::post('cierre', 'cierreStore')->name('caja.cierre.store');
        Route::get('listarCajas', 'listarCajas')->name('caja.listado');
        Route::get('listarCajas/buscar', 'buscar')->name('caja.buscar');
        Route::put('caja/{id}', 'update')->name('caja.update');
    });

    Route::middleware('permiso:alertas')->controller(AlertaController::class)->group(function () {
        Route::get('alertas', 'index')->name('alertas.index');
        Route::post('alertas', 'store')->name('alertas.store');
        Route::put('alertas/{id}/leida', 'marcarComoLeida')->name('alertas.marcarLeida');
        Route::delete('alertas/{id}', 'destroy')->name('alertas.destroy');
        Route::get('alertas/generar', 'generarAlertas')->name('alertas.generar');
        Route::get('/generar-alertas', [AlertaController::class, 'generarAlertasManual'])->name('alertas.generar');
    });
});
