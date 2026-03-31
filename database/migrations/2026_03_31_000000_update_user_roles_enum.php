<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert any existing docente users to usuario.
        DB::table('users')->where('role', 'docente')->update(['role' => 'usuario']);

        // Adapt enum type for MySQL.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','usuario') NOT NULL DEFAULT 'usuario'");
        }

        // For SQLite we may require table recreation in migrations, so assume initial creation uses new schema.
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','docente','estudiante') NOT NULL DEFAULT 'usuario'");
        }
    }
};