<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('enrollments')
            ->whereNull('subscription_granted')
            ->update(['subscription_granted' => false]);
    }

    public function down(): void
    {
        // irreversible data fix
    }
};
