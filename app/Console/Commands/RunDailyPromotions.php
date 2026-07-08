<?php

namespace App\Console\Commands;

use App\Services\PromotionService;
use Illuminate\Console\Command;

class RunDailyPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotions:run-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign scheduled coupons and apply daily product promotions.';

    /**
     * Execute the console command.
     */
    public function handle(PromotionService $promotionService): int
    {
        $expiredProducts = $promotionService->clearExpiredProductPromotions();
        $assignedCoupons = $promotionService->assignScheduledCoupons();
        $promotedProducts = $promotionService->applyDailyProductPromotions();

        $this->info("Cleared expired product deals: {$expiredProducts}");
        $this->info("Assigned customer coupons: {$assignedCoupons}");
        $this->info("Promoted discounted products: {$promotedProducts}");

        return self::SUCCESS;
    }
}
