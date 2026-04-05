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
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->decimal('student_name_X', 8, 4)->default(0.5)->change();
            $table->decimal('student_name_Y', 8, 4)->default(0.4)->change();
            $table->decimal('course_title_X', 8, 4)->default(0.5)->change();
            $table->decimal('course_title_Y', 8, 4)->default(0.5)->change();
            $table->decimal('issue_date_X', 8, 4)->default(0.8)->change();
            $table->decimal('issue_date_Y', 8, 4)->default(0.8)->change();
            $table->decimal('certificate_code_X', 8, 4)->default(0.15)->change();
            $table->decimal('certificate_code_Y', 8, 4)->default(0.8)->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->integer('student_name_X')->default(550)->change();
            $table->integer('student_name_Y')->default(400)->change();
            $table->integer('course_title_X')->default(550)->change();
            $table->integer('course_title_Y')->default(500)->change();
            $table->integer('issue_date_X')->default(550)->change();
            $table->integer('issue_date_Y')->default(650)->change();
            $table->integer('certificate_code_X')->default(550)->change();
            $table->integer('certificate_code_Y')->default(700)->change();
        });
    }
};
