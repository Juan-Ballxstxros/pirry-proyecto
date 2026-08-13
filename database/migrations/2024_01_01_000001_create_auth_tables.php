<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->integer('id_roles', true);
                $table->string('nombre', 25)->nullable();
                $table->text('descripcion')->nullable();
                $table->string('estado', 20)->default('Activo');
            });
        }

        if (! Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->integer('id_permisos', true);
                $table->string('nombre', 25)->nullable();
                $table->text('descripcion')->nullable();
            });
        }

        if (! Schema::hasTable('rol_permiso')) {
            Schema::create('rol_permiso', function (Blueprint $table) {
                $table->integer('id_rol_permiso', true);
                $table->integer('id_roles');
                $table->integer('id_permisos');
                $table->foreign('id_roles')->references('id_roles')->on('roles');
                $table->foreign('id_permisos')->references('id_permisos')->on('permisos');
            });
        }

        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100);
                $table->string('documento', 50);
                $table->string('correo', 100)->unique();
                $table->string('usuario', 50)->unique();
                $table->string('contrasena', 255);
                $table->integer('id_rol');
                $table->string('estado', 20)->default('activo');
                $table->string('reset_token', 255)->nullable();
                $table->dateTime('token_expira')->nullable();
                $table->foreign('id_rol')->references('id_roles')->on('roles');
            });
        } else {
            Schema::table('usuarios', function (Blueprint $table) {
                if (! Schema::hasColumn('usuarios', 'reset_token')) {
                    $table->string('reset_token', 255)->nullable();
                }
                if (! Schema::hasColumn('usuarios', 'token_expira')) {
                    $table->dateTime('token_expira')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};
