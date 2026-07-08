<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LeadScoringService
{
    private const SUCCESSFUL_ORDER_STATUSES = [
        Order::STATUS_DELIVERED,
        Order::STATUS_COMPLETED,
    ];

    public function profilesForUsers(Collection $users): Collection
    {
        if ($users->isEmpty()) {
            return collect();
        }

        $metricsByUserId = $this->metricsForUserIds(
            $users->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all()
        );

        return $users->mapWithKeys(function (User $user) use ($metricsByUserId) {
            $metrics = $metricsByUserId[$user->id] ?? $this->emptyMetrics();

            return [
                $user->id => $this->buildLeadProfile($user, $metrics),
            ];
        });
    }

    public function topCustomers(int $limit = 5): Collection
    {
        $customers = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', 'customer');
            })
            ->where('status', 'active')
            ->get();

        if ($customers->isEmpty()) {
            return collect();
        }

        $profiles = $this->profilesForUsers($customers);

        return $customers
            ->map(function (User $customer) use ($profiles) {
                return [
                    'user' => $customer,
                    'profile' => $profiles->get($customer->id, $this->buildLeadProfile($customer, $this->emptyMetrics())),
                ];
            })
            ->filter(fn (array $row) => $row['profile']['total_orders'] > 0)
            ->sort(function (array $left, array $right) {
                return [
                    $right['profile']['score'],
                    $right['profile']['total_spent'],
                    $right['profile']['successful_orders'],
                    $right['profile']['last_order_at']?->getTimestamp() ?? 0,
                    $right['user']->id,
                ] <=> [
                    $left['profile']['score'],
                    $left['profile']['total_spent'],
                    $left['profile']['successful_orders'],
                    $left['profile']['last_order_at']?->getTimestamp() ?? 0,
                    $left['user']->id,
                ];
            })
            ->take($limit)
            ->values();
    }

    public function scoreSnapshot(array $snapshot): array
    {
        $metrics = $this->normalizeSnapshot($snapshot);

        $daysSinceLastOrder = $metrics['last_order_at']?->diffInDays(now());
        $closedOrders = $metrics['successful_orders'] + $metrics['canceled_orders'];
        $paymentCompletionRate = $metrics['total_payments'] > 0
            ? $metrics['completed_payments'] / $metrics['total_payments']
            : 0;
        $cancellationRate = $closedOrders > 0
            ? $metrics['canceled_orders'] / $closedOrders
            : 0;

        // Fixed weights keep the score predictable without touching checkout logic.
        $orderScore = min($metrics['successful_orders'] * 6, 30);
        $spendScore = min((int) floor($metrics['total_spent'] / 500000), 25);
        $frequencyScore = min($metrics['recent_successful_orders'] * 5, 15);
        $paymentScore = (int) round($paymentCompletionRate * 10);
        $reviewScore = $metrics['review_count'] > 0
            ? min((int) round((($metrics['average_rating'] / 5) * 3) + min($metrics['review_count'], 2)), 5)
            : 0;
        $recencyScore = match (true) {
            $daysSinceLastOrder === null => 0,
            $daysSinceLastOrder <= 14 => 20,
            $daysSinceLastOrder <= 30 => 16,
            $daysSinceLastOrder <= 60 => 12,
            $daysSinceLastOrder <= 90 => 8,
            $daysSinceLastOrder <= 180 => 4,
            default => 0,
        };
        $cancellationPenalty = (int) round($cancellationRate * 20);

        $score = max(
            0,
            min(
                100,
                $orderScore
                + $spendScore
                + $frequencyScore
                + $paymentScore
                + $reviewScore
                + $recencyScore
                - $cancellationPenalty
            )
        );

        return array_merge(
            $this->segmentForScore($score),
            [
                'score' => $score,
                'days_since_last_order' => $daysSinceLastOrder,
                'payment_completion_rate' => (int) round($paymentCompletionRate * 100),
                'cancellation_rate' => (int) round($cancellationRate * 100),
                'components' => [
                    'order_score' => $orderScore,
                    'spend_score' => $spendScore,
                    'recency_score' => $recencyScore,
                    'frequency_score' => $frequencyScore,
                    'payment_score' => $paymentScore,
                    'review_score' => $reviewScore,
                    'cancellation_penalty' => $cancellationPenalty,
                ],
            ]
        );
    }

    private function buildLeadProfile(User $user, array $metrics): array
    {
        if (optional($user->role)->name !== 'customer') {
            return array_merge($this->emptyMetrics(), [
                'applicable' => false,
                'score' => 0,
                'segment_label' => 'Khong ap dung',
                'segment_class' => 'label-default',
                'priority_note' => 'Tai khoan noi bo',
                'days_since_last_order' => null,
                'payment_completion_rate' => 0,
                'cancellation_rate' => 0,
                'components' => [],
            ]);
        }

        return array_merge(
            $metrics,
            $this->scoreSnapshot($metrics),
            ['applicable' => true]
        );
    }

    private function metricsForUserIds(array $userIds): array
    {
        $metricsByUserId = collect($userIds)
            ->unique()
            ->mapWithKeys(fn ($userId) => [(int) $userId => $this->emptyMetrics()])
            ->all();

        if ($metricsByUserId === []) {
            return [];
        }

        $recentCutoff = now()->subDays(90)->toDateTimeString();

        Order::query()
            ->whereIn('user_id', array_keys($metricsByUserId))
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw(
                'SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as successful_orders',
                self::SUCCESSFUL_ORDER_STATUSES
            )
            ->selectRaw(
                'SUM(CASE WHEN status IN (?, ?) THEN total_price ELSE 0 END) as total_spent',
                self::SUCCESSFUL_ORDER_STATUSES
            )
            ->selectRaw(
                'MAX(CASE WHEN status IN (?, ?) THEN created_at ELSE NULL END) as last_order_at',
                self::SUCCESSFUL_ORDER_STATUSES
            )
            ->selectRaw(
                'SUM(CASE WHEN status IN (?, ?) AND created_at >= ? THEN 1 ELSE 0 END) as recent_successful_orders',
                [...self::SUCCESSFUL_ORDER_STATUSES, $recentCutoff]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as canceled_orders',
                [Order::STATUS_CANCELED]
            )
            ->groupBy('user_id')
            ->get()
            ->each(function ($row) use (&$metricsByUserId) {
                $metricsByUserId[(int) $row->user_id] = array_merge(
                    $metricsByUserId[(int) $row->user_id] ?? $this->emptyMetrics(),
                    [
                        'total_orders' => (int) $row->total_orders,
                        'successful_orders' => (int) $row->successful_orders,
                        'total_spent' => (float) $row->total_spent,
                        'last_order_at' => $row->last_order_at ? Carbon::parse($row->last_order_at) : null,
                        'recent_successful_orders' => (int) $row->recent_successful_orders,
                        'canceled_orders' => (int) $row->canceled_orders,
                    ]
                );
            });

        Payment::query()
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->whereIn('orders.user_id', array_keys($metricsByUserId))
            ->select('orders.user_id')
            ->selectRaw('COUNT(payments.id) as total_payments')
            ->selectRaw(
                'SUM(CASE WHEN payments.status = ? THEN 1 ELSE 0 END) as completed_payments',
                ['completed']
            )
            ->groupBy('orders.user_id')
            ->get()
            ->each(function ($row) use (&$metricsByUserId) {
                $metricsByUserId[(int) $row->user_id] = array_merge(
                    $metricsByUserId[(int) $row->user_id] ?? $this->emptyMetrics(),
                    [
                        'total_payments' => (int) $row->total_payments,
                        'completed_payments' => (int) $row->completed_payments,
                    ]
                );
            });

        Review::query()
            ->whereIn('user_id', array_keys($metricsByUserId))
            ->select('user_id')
            ->selectRaw('COUNT(*) as review_count')
            ->selectRaw('AVG(rating) as average_rating')
            ->groupBy('user_id')
            ->get()
            ->each(function ($row) use (&$metricsByUserId) {
                $metricsByUserId[(int) $row->user_id] = array_merge(
                    $metricsByUserId[(int) $row->user_id] ?? $this->emptyMetrics(),
                    [
                        'review_count' => (int) $row->review_count,
                        'average_rating' => (float) $row->average_rating,
                    ]
                );
            });

        return $metricsByUserId;
    }

    private function normalizeSnapshot(array $snapshot): array
    {
        return array_merge(
            $this->emptyMetrics(),
            [
                'total_orders' => (int) ($snapshot['total_orders'] ?? 0),
                'successful_orders' => (int) ($snapshot['successful_orders'] ?? 0),
                'total_spent' => (float) ($snapshot['total_spent'] ?? 0),
                'last_order_at' => empty($snapshot['last_order_at']) ? null : Carbon::parse($snapshot['last_order_at']),
                'recent_successful_orders' => (int) ($snapshot['recent_successful_orders'] ?? 0),
                'canceled_orders' => (int) ($snapshot['canceled_orders'] ?? 0),
                'total_payments' => (int) ($snapshot['total_payments'] ?? 0),
                'completed_payments' => (int) ($snapshot['completed_payments'] ?? 0),
                'review_count' => (int) ($snapshot['review_count'] ?? 0),
                'average_rating' => (float) ($snapshot['average_rating'] ?? 0),
            ]
        );
    }

    private function emptyMetrics(): array
    {
        return [
            'total_orders' => 0,
            'successful_orders' => 0,
            'total_spent' => 0.0,
            'last_order_at' => null,
            'recent_successful_orders' => 0,
            'canceled_orders' => 0,
            'total_payments' => 0,
            'completed_payments' => 0,
            'review_count' => 0,
            'average_rating' => 0.0,
        ];
    }

    private function segmentForScore(int $score): array
    {
        return match (true) {
            $score >= 80 => [
                'segment_label' => 'VIP',
                'segment_class' => 'label-success',
                'priority_note' => 'Uu tien ngay',
            ],
            $score >= 60 => [
                'segment_label' => 'Uu tien cao',
                'segment_class' => 'label-primary',
                'priority_note' => 'Nen cham soc som',
            ],
            $score >= 40 => [
                'segment_label' => 'Can cham soc',
                'segment_class' => 'label-info',
                'priority_note' => 'Theo doi sat',
            ],
            $score >= 20 => [
                'segment_label' => 'Theo doi',
                'segment_class' => 'label-warning',
                'priority_note' => 'Nhac lai dinh ky',
            ],
            default => [
                'segment_label' => 'It hoat dong',
                'segment_class' => 'label-default',
                'priority_note' => 'Nuoi duong them',
            ],
        };
    }
}
