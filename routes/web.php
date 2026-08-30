<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
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

// Productos API
Route::get('/productos', [ProductController::class, 'index'])->name('productos.index');
Route::get('/productos/categorias', [ProductController::class, 'categorias'])->name('productos.categorias');
Route::post('/productos', [ProductController::class, 'store'])->middleware('auth.session')->name('productos.store');
Route::put('/productos/{producto}', [ProductController::class, 'update'])->middleware('auth.session')->name('productos.update');
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
    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('admin.usuarios.toggle');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::patch('/roles/{role}/estado', [RoleController::class, 'toggleEstado'])->name('admin.roles.toggle');
    Route::get('/permisos', [PermissionController::class, 'index'])->name('admin.permisos.index');
    Route::get('/permisos/crear', [PermissionController::class, 'create'])->name('admin.permisos.create');
    Route::post('/permisos', [PermissionController::class, 'store'])->name('admin.permisos.store');
    Route::get('/permisos/{permiso}/editar', [PermissionController::class, 'edit'])->name('admin.permisos.edit');
    Route::put('/permisos/{permiso}', [PermissionController::class, 'update'])->name('admin.permisos.update');
    Route::get('/permisos/asignar', [PermissionController::class, 'assignForm'])->name('admin.permisos.assign');
    Route::post('/permisos/asignar', [PermissionController::class, 'assign'])->name('admin.permisos.assign.post');
});
