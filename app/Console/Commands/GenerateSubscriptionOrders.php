<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionOrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSubscriptionOrders extends Command
{
    protected $signature = 'subscriptions:generate-orders {--subscription_id=} {--dry-run}';

    protected $description = 'Generate due orders from active meal kit subscriptions.';

    public function handle(SubscriptionOrderService $subscriptionOrderService): int
    {
        $query = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now())
            ->orderBy('next_run_at');

        if ($this->option('subscription_id')) {
            $query->where('id', (int) $this->option('subscription_id'));
        }

        $subscriptions = $query->get();

        if ($this->option('dry-run')) {
            $this->info("Due subscriptions: {$subscriptions->count()}");

            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $order = $subscriptionOrderService->createDueOrder($subscription);

                if ($order) {
                    $created++;
                    $this->info("Generated order #{$order->id} for subscription #{$subscription->id}");
                } else {
                    $skipped++;
                }
            } catch (\Throwable $exception) {
                $failed++;
                $this->error("Subscription #{$subscription->id} failed: {$exception->getMessage()}");
                Log::error('Failed to generate subscription order', [
                    'subscription_id' => $subscription->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $this->info("Created: {$created}; skipped: {$skipped}; failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
