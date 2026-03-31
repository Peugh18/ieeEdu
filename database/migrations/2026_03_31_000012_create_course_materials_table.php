<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('course_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->enum('kind', ['pdf', 'excel', 'url', 'other'])->default('pdf');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_materials');
    }
};
