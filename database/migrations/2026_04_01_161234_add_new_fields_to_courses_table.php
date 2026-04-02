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
        Schema::table('courses', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('class_hours')->nullable();
            $table->string('whatsapp_link')->nullable();
            $table->text('objectives')->nullable();
            $table->text('requirements')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'start_time',
                'class_hours',
                'whatsapp_link',
                'objectives',
                'requirements'
            ]);
        });
    }
};
