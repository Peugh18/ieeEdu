<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'usuario'])->default('usuario')->after('email');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['activo', 'inactivo'])->default('activo')->after('role');
            }
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono')->nullable()->after('name');
            }
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('type', ['grabado', 'en vivo', 'evento'])->default('grabado');
            $table->enum('status', ['BORRADOR', 'PUBLICADO', 'OCULTO'])->default('BORRADOR');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('docente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->string('comprobante')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('passed')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });
    }
};