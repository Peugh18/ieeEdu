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
            $table->integer('progress')->default(0)->after('course_id');
            $table->foreignId('last_lesson_id')->nullable()->after('progress')->constrained('course_lessons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['last_lesson_id']);
            $table->dropColumn(['progress', 'last_lesson_id']);
        });
    }
};
