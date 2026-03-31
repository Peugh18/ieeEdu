<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
            $table->integer('duration_weeks')->nullable()->after('price');
            $table->boolean('is_featured')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['slug', 'duration_weeks', 'is_featured']);
        });
    }
};
