<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\Subscription;
use App\Services\ShippingFeeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function __construct(private ShippingFeeService $shippingFeeService)
    {
    }

    public function index()
    {
        $subscriptions = Subscription::with(['items.product.firstImage', 'shippingAddress', 'orders' => function ($query) {
            $query->latest()->limit(3);
        }])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('clients.pages.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'statusLabels' => Subscription::statusLabels(),
            'frequencyLabels' => Subscription::frequencyLabels(),
            'weekDayLabels' => Subscription::weekDayLabels(),
        ]);
    }

    public function create()
    {
        $products = Product::with('firstImage')
            ->where('status', 'in_stock')
            ->orderBy('name')
            ->get();

        $addresses = ShippingAddress::where('user_id', Auth::id())
            ->orderByDesc('default')
            ->latest()
            ->get();

        return view('clients.pages.subscriptions.create', [
            'products' => $products,
            'addresses' => $addresses,
            'weekDayLabels' => Subscription::weekDayLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shipping_address_id' => [
                'required',
                'integer',
                Rule::exists('shipping_addresses', 'id')->where('user_id', Auth::id()),
            ],
            'frequency' => ['required', Rule::in([Subscription::FREQUENCY_DAILY, Subscription::FREQUENCY_WEEKLY])],
            'week_day' => ['nullable', 'integer', 'between:1,7', 'required_if:frequency,' . Subscription::FREQUENCY_WEEKLY],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'preferred_delivery_time' => ['required', 'date_format:H:i'],
            'payment_method' => ['required', Rule::in(['cash', 'vietqr'])],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:99'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $selectedQuantities = collect($data['quantities'])
            ->map(fn($quantity) => (int) $quantity)
            ->filter(fn($quantity) => $quantity > 0);

        if ($selectedQuantities->isEmpty()) {
            return back()
                ->withErrors(['quantities' => 'Vui long chon it nhat mot san pham cho goi dinh ky.'])
                ->withInput();
        }

        $products = Product::whereIn('id', $selectedQuantities->keys())
            ->where('status', 'in_stock')
            ->get()
            ->keyBy('id');

        if ($products->count() !== $selectedQuantities->count()) {
            return back()
                ->withErrors(['quantities' => 'Mot so san pham khong con kha dung.'])
                ->withInput();
        }

        foreach ($selectedQuantities as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (!$product || $product->stock < $quantity) {
                return back()
                    ->withErrors(['quantities' => "San pham {$product?->name} khong du ton kho."])
                    ->withInput();
            }
        }

        $address = ShippingAddress::where('id', $data['shipping_address_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $shippingQuote = $this->shippingFeeService->quoteForAddress($address);
        } catch (RuntimeException $exception) {
            return back()
                ->withErrors(['shipping_address_id' => $exception->getMessage()])
                ->withInput();
        }

        DB::transaction(function () use ($data, $selectedQuantities, $products, $shippingQuote) {
            $subtotal = $selectedQuantities->reduce(function ($total, $quantity, $productId) use ($products) {
                return $total + ($quantity * $products->get((int) $productId)->current_price);
            }, 0);

            $subscription = new Subscription([
                'user_id' => Auth::id(),
                'shipping_address_id' => $data['shipping_address_id'],
                'frequency' => $data['frequency'],
                'week_day' => $data['frequency'] === Subscription::FREQUENCY_WEEKLY ? $data['week_day'] : null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'preferred_delivery_time' => $data['preferred_delivery_time'] . ':00',
                'status' => Subscription::STATUS_ACTIVE,
                'payment_method' => $data['payment_method'],
                'estimated_subtotal' => $subtotal,
                'estimated_shipping_fee' => $shippingQuote['shipping_fee'],
                'estimated_total' => $subtotal + $shippingQuote['shipping_fee'],
                'note' => $data['note'] ?? null,
            ]);

            $subscription->next_run_at = $subscription->calculateNextRunAt();
            $subscription->save();

            foreach ($selectedQuantities as $productId => $quantity) {
                $product = $products->get((int) $productId);

                $subscription->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_snapshot' => $product->current_price,
                ]);
            }
        });

        toastr()->success('Da tao goi dat lich dinh ky.');

        return redirect()->route('subscriptions.index');
    }

    public function pause(int $id)
    {
        $subscription = $this->findUserSubscription($id);

        if ($subscription->status === Subscription::STATUS_ACTIVE) {
            $subscription->status = Subscription::STATUS_PAUSED;
            $subscription->save();
            toastr()->success('Da tam dung goi dinh ky.');
        }

        return back();
    }

    public function resume(int $id)
    {
        $subscription = $this->findUserSubscription($id);

        if ($subscription->status === Subscription::STATUS_PAUSED) {
            $subscription->status = Subscription::STATUS_ACTIVE;
            $subscription->next_run_at = $this->nextFutureRunAt($subscription);
            $subscription->save();
            toastr()->success('Da tiep tuc goi dinh ky.');
        }

        return back();
    }

    public function cancel(int $id)
    {
        $subscription = $this->findUserSubscription($id);

        if (!in_array($subscription->status, [Subscription::STATUS_CANCELED, Subscription::STATUS_EXPIRED], true)) {
            $subscription->status = Subscription::STATUS_CANCELED;
            $subscription->next_run_at = null;
            $subscription->save();
            toastr()->success('Da huy goi dinh ky.');
        }

        return back();
    }

    private function findUserSubscription(int $id): Subscription
    {
        return Subscription::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function nextFutureRunAt(Subscription $subscription): Carbon
    {
        $nextRunAt = $subscription->calculateNextRunAt(now());

        while ($nextRunAt->lte(now())) {
            $nextRunAt = $subscription->frequency === Subscription::FREQUENCY_DAILY
                ? $nextRunAt->addDay()
                : $nextRunAt->addWeek();
        }

        return $nextRunAt;
    }
}
