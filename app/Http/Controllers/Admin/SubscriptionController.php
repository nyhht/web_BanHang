<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with([
            'user',
            'shippingAddress',
            'items.product.firstImage',
            'orders' => function ($query) {
                $query->latest()->limit(3);
            },
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->input('frequency'));
        }

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->input('keyword'));

            $query->where(function ($query) use ($keyword) {
                $query->where('id', $keyword)
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('shippingAddress', function ($addressQuery) use ($keyword) {
                        $addressQuery->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('address', 'like', "%{$keyword}%");
                    });
            });
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        $statusCounts = Subscription::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.pages.subscriptions', [
            'subscriptions' => $subscriptions,
            'statusCounts' => $statusCounts,
            'statusLabels' => Subscription::statusLabels(),
            'frequencyLabels' => Subscription::frequencyLabels(),
            'weekDayLabels' => Subscription::weekDayLabels(),
            'filters' => $request->only(['status', 'frequency', 'keyword']),
        ]);
    }

    public function pause(Request $request)
    {
        $subscription = Subscription::findOrFail($request->integer('id'));

        if ($subscription->status !== Subscription::STATUS_ACTIVE) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể tạm dừng gói đang hoạt động.',
            ], 422);
        }

        $subscription->status = Subscription::STATUS_PAUSED;
        $subscription->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã tạm dừng gói định kỳ.',
        ]);
    }

    public function resume(Request $request)
    {
        $subscription = Subscription::findOrFail($request->integer('id'));

        if ($subscription->status !== Subscription::STATUS_PAUSED) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể tiếp tục gói đang tạm dừng.',
            ], 422);
        }

        $subscription->status = Subscription::STATUS_ACTIVE;
        $subscription->next_run_at = $subscription->calculateNextRunAt(now());

        while ($subscription->next_run_at && $subscription->next_run_at->lte(now())) {
            $subscription->next_run_at = $subscription->frequency === Subscription::FREQUENCY_DAILY
                ? $subscription->next_run_at->addDay()
                : $subscription->next_run_at->addWeek();
        }

        $subscription->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã tiếp tục gói định kỳ.',
        ]);
    }

    public function cancel(Request $request)
    {
        $subscription = Subscription::findOrFail($request->integer('id'));

        if (in_array($subscription->status, [Subscription::STATUS_CANCELED, Subscription::STATUS_EXPIRED], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Gói này đã kết thúc, không thể hủy lại.',
            ], 422);
        }

        $subscription->status = Subscription::STATUS_CANCELED;
        $subscription->next_run_at = null;
        $subscription->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã hủy gói định kỳ.',
        ]);
    }
}
