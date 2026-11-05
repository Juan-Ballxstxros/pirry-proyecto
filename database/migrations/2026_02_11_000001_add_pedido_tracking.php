<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seguimiento de pedidos (El Pirry):
 *  - codigo: identificador legible del pedido (ej. PIR-00001) para que el
 *    cliente y el administrador puedan referenciarlo sin exponer el id.
 *  - nombre_cliente: las ventas manuales ya lo pedían en el formulario pero
 *    la columna no existía en la base de datos (se descartaba silenciosamente).
 *  - historial_pedidos: registra cada cambio de estado con fecha y usuario.
 *  - Los estados existentes se mapean al nuevo flujo SIN perder información:
 *    en_proceso -> pendiente, preparando -> en_preparacion.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pedidos', 'codigo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->string('codigo', 20)->nullable()->unique()->after('id');
            });

            // Rellenar los pedidos existentes (no se borra ni altera nada más).
            DB::table('pedidos')->orderBy('id')->get()->each(function ($pedido) {
                DB::table('pedidos')->where('id', $pedido->id)->update([
                    'codigo' => 'PIR-'.str_pad((string) $pedido->id, 5, '0', STR_PAD_LEFT),
                ]);
            });
        }

        if (! Schema::hasColumn('pedidos', 'nombre_cliente')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->string('nombre_cliente', 100)->nullable()->after('id_usuario');
            });
        }

        // Mapeo de los estados antiguos al nuevo flujo de seguimiento.
        DB::table('pedidos')->where('estado', 'en_proceso')->update(['estado' => 'pendiente']);
        DB::table('pedidos')->where('estado', 'preparando')->update(['estado' => 'en_preparacion']);

        if (! Schema::hasTable('historial_pedidos')) {
            Schema::create('historial_pedidos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('id_pedido');
                $table->string('estado_anterior', 30)->nullable();
                $table->string('estado_nuevo', 30);
                $table->integer('id_usuario')->nullable();
                $table->dateTime('fecha')->useCurrent();
                $table->foreign('id_pedido')->references('id')->on('pedidos')->cascadeOnDelete();
                $table->foreign('id_usuario')->references('id')->on('usuarios')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_pedidos');

        if (Schema::hasColumn('pedidos', 'codigo')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('codigo');
            });
        }

        if (Schema::hasColumn('pedidos', 'nombre_cliente')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('nombre_cliente');
            });
        }
    }
};
