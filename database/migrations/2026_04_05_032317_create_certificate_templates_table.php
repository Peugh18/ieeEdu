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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('template_image')->nullable(); // Path to PNG/JPG
            
            // X and Y coordinates in pixels/points
            $table->integer('student_name_X')->default(550);
            $table->integer('student_name_Y')->default(400);
            $table->integer('student_name_font_size')->default(32);
            
            $table->integer('course_title_X')->default(550);
            $table->integer('course_title_Y')->default(500);
            $table->integer('course_title_font_size')->default(24);
            
            $table->integer('issue_date_X')->default(550);
            $table->integer('issue_date_Y')->default(650);
            $table->integer('issue_date_font_size')->default(14);
            
            $table->integer('certificate_code_X')->default(550);
            $table->integer('certificate_code_Y')->default(700);
            $table->integer('certificate_code_font_size')->default(10);

            $table->string('font_color')->default('#57572A'); // Accent color IEE
            $table->string('font_family')->default('serif');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
