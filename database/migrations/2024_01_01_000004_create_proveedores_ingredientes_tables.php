<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // RF-034/035/036 · RI-034/035/036 · RN-020/021/022
        if (! Schema::hasTable('proveedores')) {
            Schema::create('proveedores', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 150);
                $table->string('identificacion', 30)->unique();
                $table->string('telefono', 20)->nullable();
                $table->string('direccion', 150)->nullable();
                $table->string('email', 100)->unique();
                $table->string('estado', 20)->default('activo');
                $table->timestamps();
            });
        }

        // Catálogo de ingredientes/insumos.
        if (! Schema::hasTable('ingredientes')) {
            Schema::create('ingredientes', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100);
                $table->string('unidad_medida', 20)->default('unidad');
                $table->decimal('stock_actual', 10, 2)->default(0);
                $table->decimal('stock_minimo', 10, 2)->default(0);
                $table->string('estado', 20)->default('activo');
                $table->timestamps();
            });
        }

        // RI-032: relación proveedor <-> ingrediente (qué proveedor surte qué ingrediente).
        if (! Schema::hasTable('proveedor_ingrediente')) {
            Schema::create('proveedor_ingrediente', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_proveedor');
                $table->integer('id_ingrediente');
                $table->boolean('disponible')->default(true);
                $table->decimal('precio_referencia', 10, 2)->nullable();
                $table->timestamp('fecha_actualizacion')->useCurrent();
                $table->foreign('id_proveedor')->references('id')->on('proveedores')->cascadeOnDelete();
                $table->foreign('id_ingrediente')->references('id')->on('ingredientes')->cascadeOnDelete();
                $table->unique(['id_proveedor', 'id_ingrediente']);
            });
        }

        // Relación producto <-> ingrediente (receta): permite saber qué insumos
        // usa cada producto del menú, para descontar inventario y personalizar el carrito.
        if (! Schema::hasTable('producto_ingrediente')) {
            Schema::create('producto_ingrediente', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_producto');
                $table->integer('id_ingrediente');
                $table->decimal('cantidad_requerida', 10, 2)->default(1);
                $table->foreign('id_producto')->references('id')->on('productos')->cascadeOnDelete();
                $table->foreign('id_ingrediente')->references('id')->on('ingredientes')->cascadeOnDelete();
                $table->unique(['id_producto', 'id_ingrediente']);
            });
        }

        // RF-031 · RI-031 · RN-019: registro de compras de ingredientes a un proveedor.
        if (! Schema::hasTable('compras_ingredientes')) {
            Schema::create('compras_ingredientes', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_ingrediente');
                $table->integer('id_proveedor');
                $table->decimal('cantidad', 10, 2);
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('total', 10, 2);
                $table->dateTime('fecha')->useCurrent();
                $table->foreign('id_ingrediente')->references('id')->on('ingredientes');
                $table->foreign('id_proveedor')->references('id')->on('proveedores');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('compras_ingredientes');
        Schema::dropIfExists('producto_ingrediente');
        Schema::dropIfExists('proveedor_ingrediente');
        Schema::dropIfExists('ingredientes');
        Schema::dropIfExists('proveedores');
    }
};
