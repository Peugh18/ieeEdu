<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionAccessService;
use Illuminate\Console\Command;

class SyncExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:sync-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks expired subscriptions as inactive and syncs user access';

    public function __construct(protected SubscriptionAccessService $accessService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredSubscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where('end_date', '<', now())
            ->get();

        $count = $expiredSubscriptions->count();
        if ($count === 0) {
            $this->info('No expired subscriptions found.');

            return;
        }

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => Subscription::STATUS_EXPIRED]);
            $this->accessService->sync($subscription->user_id);
            $this->info("Synced expired subscription ID: {$subscription->id} for User ID: {$subscription->user_id}");
        }

        $this->info("Successfully synced {$count} expired subscriptions.");
    }
}
