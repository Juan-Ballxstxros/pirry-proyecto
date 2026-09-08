<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RF-029 · RI-029 · RN-017: toda venta debe registrar una forma de pago
     * (una venta solo es válida si incluye al menos un método de pago).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pedidos', 'forma_pago')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->string('forma_pago', 30)->default('efectivo')->after('estado');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'forma_pago')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('forma_pago');
            });
        }
    }
};
