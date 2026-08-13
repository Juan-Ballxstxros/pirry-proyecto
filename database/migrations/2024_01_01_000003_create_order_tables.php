<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pedidos')) {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_usuario')->nullable();
                $table->decimal('total', 10, 2)->default(0);
                $table->string('estado', 30)->default('en_proceso');
                $table->dateTime('fecha')->useCurrent();
                $table->foreign('id_usuario')->references('id')->on('usuarios')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('detalle_pedido')) {
            Schema::create('detalle_pedido', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_pedido');
                $table->integer('id_producto');
                $table->integer('cantidad')->default(1);
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->foreign('id_pedido')->references('id')->on('pedidos')->cascadeOnDelete();
                $table->foreign('id_producto')->references('id')->on('productos');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido');
        Schema::dropIfExists('pedidos');
    }
};
