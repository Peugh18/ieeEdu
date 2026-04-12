<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Iniciando limpieza de usuarios...\n";

// Identificar usuarios que NO son admins
$nonAdminIds = User::where('role', '!=', 'admin')->pluck('id')->toArray();

if (empty($nonAdminIds)) {
    echo "No se encontraron usuarios que no sean administradores.\n";
    exit;
}

echo "Se eliminarán " . count($nonAdminIds) . " usuarios y sus datos asociados.\n";

$tablesWithUserId = [
    'certificates',
    'course_exam_attempts',
    'enrollments',
    'lesson_progress',
    'payments',
    'subscriptions',
    'lesson_comments',
    'comment_likes',
    'lesson_user',
    'whatsapp_leads'
];

try {
    DB::beginTransaction();

    // Desactivar restricciones de llaves foráneas para mayor seguridad en la limpieza
    Schema::disableForeignKeyConstraints();

    foreach ($tablesWithUserId as $table) {
        if (Schema::hasTable($table)) {
            $deleted = DB::table($table)->whereIn('user_id', $nonAdminIds)->delete();
            echo "Eliminados $deleted registros de la tabla $table.\n";
        }
    }

    // Finalmente eliminar los usuarios
    $deletedUsers = User::whereIn('id', $nonAdminIds)->delete();
    echo "Eliminados $deletedUsers usuarios.\n";

    Schema::enableForeignKeyConstraints();
    DB::commit();

    echo "Limpieza completada con éxito.\n";
} catch (\Exception $e) {
    DB::rollBack();
    Schema::enableForeignKeyConstraints();
    echo "Error durante la limpieza: " . $e->getMessage() . "\n";
}
