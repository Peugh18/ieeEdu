<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionAccessService;

class SyncSubscriptionAccess extends Command
{
    protected $signature = 'subscription:sync {--backfill : Mark pre-migration enrollments with correct subscription_granted flag}';
    protected $description = 'Sync subscription access flags across all users';

    public function __construct(protected SubscriptionAccessService $accessService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('backfill')) {
            $this->backfillHistoricalData();
        }

        $this->syncAllUsers();
    }

    /**
     * Marca los enrollments pre-migración con el flag correcto.
     * Lógica: si el usuario NO tiene un Payment aprobado para ese curso_id,
     * entonces el enrollment vino de una suscripción.
     */
    private function backfillHistoricalData(): void
    {
        $this->info('🔄 Backfilling subscription_granted flags...');

        $userIds = DB::table('enrollments')->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $paidCourseIds = DB::table('payments')
                ->where('user_id', $userId)
                ->where('status', 'aprobado')
                ->whereNotNull('course_id')
                ->pluck('course_id')
                ->toArray();

            // Enrollments SIN payment individual → vinieron de suscripción
            DB::table('enrollments')
                ->where('user_id', $userId)
                ->whereNotIn('course_id', $paidCourseIds)
                ->update(['subscription_granted' => true]);
        }

        $this->info('✅ Backfill complete.');
    }

    /**
     * Sincroniza subscription_active para todos los usuarios.
     */
    private function syncAllUsers(): void
    {
        $this->info('🔄 Syncing subscription_active for all users...');

        $userIds = DB::table('enrollments')->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $this->accessService->sync($userId);
        }

        $this->info('✅ Sync complete for ' . count($userIds) . ' users.');
    }
}
