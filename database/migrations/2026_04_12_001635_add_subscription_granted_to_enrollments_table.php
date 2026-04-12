<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Marca que esta inscripción vino de una suscripción (nunca se borra)
            $table->boolean('subscription_granted')->default(false)->after('passed');
            // Controla si el acceso por suscripción está activo ahora mismo
            $table->boolean('subscription_active')->default(false)->after('subscription_granted');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['subscription_granted', 'subscription_active']);
        });
    }
};
