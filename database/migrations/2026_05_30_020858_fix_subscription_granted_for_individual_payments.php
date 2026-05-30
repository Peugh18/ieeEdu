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
        // Fix enrollments linked to individual payments (not subscriptions)
        DB::table('enrollments')
            ->where('subscription_granted', true)
            ->whereIn('payment_id', function ($query) {
                $query->select('id')
                    ->from('payments')
                    ->whereNull('subscription_type');
            })
            ->update(['subscription_granted' => false]);

        // Fix enrollments with no payment (free enrollments)
        DB::table('enrollments')
            ->where('subscription_granted', true)
            ->whereNull('payment_id')
            ->update(['subscription_granted' => false]);
    }

    public function down(): void
    {
        // irreversible data fix
    }
};
