<?php

namespace Tests\Unit;

use App\Services\LeadScoringService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class LeadScoringServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_scores_vip_customers_high_when_purchase_history_is_strong(): void
    {
        Carbon::setTestNow('2026-07-01 10:00:00');

        $service = new LeadScoringService();

        $profile = $service->scoreSnapshot([
            'successful_orders' => 7,
            'total_spent' => 6200000,
            'last_order_at' => '2026-06-24 08:00:00',
            'recent_successful_orders' => 4,
            'canceled_orders' => 0,
            'total_payments' => 7,
            'completed_payments' => 7,
            'review_count' => 3,
            'average_rating' => 5,
        ]);

        $this->assertSame('VIP', $profile['segment_label']);
        $this->assertGreaterThanOrEqual(80, $profile['score']);
        $this->assertSame(100, $profile['payment_completion_rate']);
        $this->assertSame(0, $profile['cancellation_rate']);
    }

    public function test_penalizes_customers_with_cancellations_and_stale_activity(): void
    {
        Carbon::setTestNow('2026-07-01 10:00:00');

        $service = new LeadScoringService();

        $profile = $service->scoreSnapshot([
            'successful_orders' => 1,
            'total_spent' => 300000,
            'last_order_at' => '2025-12-01 08:00:00',
            'recent_successful_orders' => 0,
            'canceled_orders' => 3,
            'total_payments' => 4,
            'completed_payments' => 1,
            'review_count' => 0,
            'average_rating' => 0,
        ]);

        $this->assertSame('It hoat dong', $profile['segment_label']);
        $this->assertLessThan(20, $profile['score']);
        $this->assertSame(25, $profile['payment_completion_rate']);
        $this->assertSame(75, $profile['cancellation_rate']);
    }
}
