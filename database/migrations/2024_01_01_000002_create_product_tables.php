<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100);
            });
        }

        if (! Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 150);
                $table->text('descripcion')->nullable();
                $table->decimal('precio', 10, 2);
                $table->string('imagen', 500)->nullable();
                $table->integer('id_categoria');
                $table->boolean('disponible')->default(true);
                $table->string('estado', 20)->default('activo');
                $table->dateTime('fecha_eliminacion')->nullable();
                $table->integer('eliminado_por')->nullable();
                $table->foreign('id_categoria')->references('id')->on('categorias');
            });
        } else {
            Schema::table('productos', function (Blueprint $table) {
                if (! Schema::hasColumn('productos', 'imagen')) {
                    $table->string('imagen', 500)->nullable()->after('precio');
                }
                if (! Schema::hasColumn('productos', 'descripcion')) {
                    $table->text('descripcion')->nullable()->after('nombre');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
    }
};
