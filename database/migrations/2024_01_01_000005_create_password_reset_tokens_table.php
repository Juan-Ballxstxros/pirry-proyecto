<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RF-017/018 · RI-018 · RN-006.
     * Tabla estándar de Laravel para tokens de recuperación de contraseña.
     * El token se guarda hasheado (nunca en texto plano) y expira a los 15 minutos.
     * Sustituye a las columnas reset_token/token_expira de `usuarios`, que
     * guardaban el token en claro.
     */
    public function up(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
