<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CompraIngredienteController;
use App\Http\Controllers\Admin\IngredienteController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OperacionesController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Tienda pública
Route::get('/', [StoreController::class, 'index'])->name('home');

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recuperación de contraseña (RF-017/018) — envío vía API externa de correo
Route::get('/recuperar-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/recuperar-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Productos API — el catálogo completo solo se pide desde el menú cuando
// el usuario ya inició sesión (ver resources/views/store/index.blade.php).
Route::get('/productos', [ProductController::class, 'index'])->name('productos.index');
Route::get('/productos/categorias', [ProductController::class, 'categorias'])->name('productos.categorias');
Route::post('/productos', [ProductController::class, 'store'])->middleware('auth.session')->name('productos.store');
Route::put('/productos/{producto}', [ProductController::class, 'update'])->middleware('auth.session')->name('productos.update');
Route::delete('/productos/{producto}/imagen', [ProductController::class, 'eliminarImagen'])->middleware('auth.session')->name('productos.imagen.destroy');
Route::delete('/productos/{producto}', [ProductController::class, 'destroy'])->middleware('auth.session')->name('productos.destroy');

// Carrito
Route::prefix('carrito')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('carrito.show');
    Route::post('/agregar', [CartController::class, 'add'])->name('carrito.add');
    Route::post('/actualizar', [CartController::class, 'update'])->name('carrito.update');
    Route::post('/eliminar', [CartController::class, 'remove'])->name('carrito.remove');
    Route::post('/confirmar', [CartController::class, 'confirm'])->middleware('auth.session')->name('carrito.confirm');
});

// Panel administrador
Route::prefix('admin')->middleware(['auth.session', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/estadisticas', [StatsController::class, 'view'])->name('admin.estadisticas.index');
    Route::get('/api/estadisticas', [StatsController::class, 'index'])->name('admin.api.estadisticas');

    // Usuarios
    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('admin.usuarios.toggle');
    Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::patch('/roles/{role}/estado', [RoleController::class, 'toggleEstado'])->name('admin.roles.toggle');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    // Permisos
    Route::get('/permisos', [PermissionController::class, 'index'])->name('admin.permisos.index');
    Route::get('/permisos/crear', [PermissionController::class, 'create'])->name('admin.permisos.create');
    Route::post('/permisos', [PermissionController::class, 'store'])->name('admin.permisos.store');
    Route::get('/permisos/{permiso}/editar', [PermissionController::class, 'edit'])->name('admin.permisos.edit');
    Route::put('/permisos/{permiso}', [PermissionController::class, 'update'])->name('admin.permisos.update');
    Route::delete('/permisos/{permiso}', [PermissionController::class, 'destroy'])->name('admin.permisos.destroy');
    Route::get('/permisos/asignar', [PermissionController::class, 'assignForm'])->name('admin.permisos.assign');
    Route::post('/permisos/asignar', [PermissionController::class, 'assign'])->name('admin.permisos.assign.post');

    // Proveedores (RF-034/035/036)
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('admin.proveedores.index');
    Route::get('/proveedores/crear', [ProveedorController::class, 'create'])->name('admin.proveedores.create');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('admin.proveedores.store');
    Route::get('/proveedores/{proveedor}/editar', [ProveedorController::class, 'edit'])->name('admin.proveedores.edit');
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('admin.proveedores.update');
    Route::patch('/proveedores/{proveedor}/estado', [ProveedorController::class, 'toggleEstado'])->name('admin.proveedores.toggle');
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('admin.proveedores.destroy');

    // Ingredientes (relacionados con proveedores y productos)
    Route::get('/ingredientes', [IngredienteController::class, 'index'])->name('admin.ingredientes.index');
    Route::get('/ingredientes/crear', [IngredienteController::class, 'create'])->name('admin.ingredientes.create');
    Route::post('/ingredientes', [IngredienteController::class, 'store'])->name('admin.ingredientes.store');
    Route::get('/ingredientes/{ingrediente}/editar', [IngredienteController::class, 'edit'])->name('admin.ingredientes.edit');
    Route::put('/ingredientes/{ingrediente}', [IngredienteController::class, 'update'])->name('admin.ingredientes.update');
    Route::patch('/ingredientes/{ingrediente}/estado', [IngredienteController::class, 'toggleEstado'])->name('admin.ingredientes.toggle');
    Route::post('/ingredientes/{ingrediente}/proveedores', [IngredienteController::class, 'asociarProveedor'])->name('admin.ingredientes.proveedores.asociar');
    Route::delete('/ingredientes/{ingrediente}/proveedores/{proveedor}', [IngredienteController::class, 'quitarProveedor'])->name('admin.ingredientes.proveedores.quitar');
    Route::post('/ingredientes/{ingrediente}/productos', [IngredienteController::class, 'asociarProducto'])->name('admin.ingredientes.productos.asociar');
    Route::delete('/ingredientes/{ingrediente}/productos/{producto}', [IngredienteController::class, 'quitarProducto'])->name('admin.ingredientes.productos.quitar');

    // Compras de insumos / abastecimiento (RF-031/033)
    Route::get('/compras', [CompraIngredienteController::class, 'index'])->name('admin.compras.index');
    Route::get('/compras/crear', [CompraIngredienteController::class, 'create'])->name('admin.compras.create');
    Route::post('/compras', [CompraIngredienteController::class, 'store'])->name('admin.compras.store');

    // Operativo (pedidos/ventas/historial): las mismas funciones que tiene
    // el panel de empleados, disponibles también para el administrador,
    // sin que el admin tenga que entrar al panel de empleados.
    Route::get('/pedidos', [OperacionesController::class, 'pedidos'])->name('admin.pedidos.index');
    Route::patch('/pedidos/{pedido}/estado', [OperacionesController::class, 'actualizarEstadoPedido'])->name('admin.pedidos.estado');
    Route::get('/ventas/crear', [OperacionesController::class, 'crearVenta'])->name('admin.ventas.create');
    Route::post('/ventas', [OperacionesController::class, 'registrarVenta'])->name('admin.ventas.store');
    Route::get('/historial', [OperacionesController::class, 'historial'])->name('admin.historial');
});

// Panel de empleados (RF-029/030): SOLO accesible para usuarios con rol
// "Empleado" (ni administradores ni clientes). Registrar ventas, procesar
// pedidos y consultar el historial e inventario.
Route::prefix('empleado')->middleware(['auth.session', 'empleado'])->group(function () {
    Route::get('/', [OperacionesController::class, 'dashboard'])->name('empleado.dashboard');
    Route::get('/pedidos', [OperacionesController::class, 'pedidos'])->name('empleado.pedidos.index');
    Route::patch('/pedidos/{pedido}/estado', [OperacionesController::class, 'actualizarEstadoPedido'])->name('empleado.pedidos.estado');
    Route::get('/ventas/crear', [OperacionesController::class, 'crearVenta'])->name('empleado.ventas.create');
    Route::post('/ventas', [OperacionesController::class, 'registrarVenta'])->name('empleado.ventas.store');
    Route::get('/historial', [OperacionesController::class, 'historial'])->name('empleado.historial');
    Route::get('/inventario', [OperacionesController::class, 'inventario'])->name('empleado.inventario');
});