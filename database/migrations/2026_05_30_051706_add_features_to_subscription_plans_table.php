<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function defaultFeatures(): array
    {
        return [
            'Acceso ilimitado a TODOS los cursos grabados (asíncronos)',
            'Acceso a clases en vivo exclusivas',
            'Certificaciones incluidas sin costo adicional',
            'Acceso a comunidad privada activa',
            'Actualizaciones constantes de contenido',
            'Contenido orientado al mercado laboral',
        ];
    }

    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->json('features')->nullable()->after('description');
        });

        DB::table('subscription_plans')->update([
            'features' => json_encode($this->defaultFeatures(), JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
