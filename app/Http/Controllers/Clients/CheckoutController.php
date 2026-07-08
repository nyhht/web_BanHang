<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Services\ShippingFeeService;
use App\Services\VietQrService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private ShippingFeeService $shippingFeeService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('default', 1)->first();
        if (is_null($addresses) || is_null($defaultAddress)) {
            toastr()->error('Vui lòng thêm địa chỉ giao hàng!');
            return redirect()->route('account');
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $activeCoupon = $this->resolveCouponFromSession($cartItems);
        try {
            $amounts = $this->calculateOrderAmounts($cartItems, $activeCoupon, $defaultAddress);
        } catch (\RuntimeException $e) {
            toastr()->error($e->getMessage());
            return redirect()->route('account');
        }

        $appliedCoupon = null;
        if ($activeCoupon) {
            $appliedCoupon = [
                'code' => $activeCoupon->code,
                'discount_percentage' => $activeCoupon->discount_percentage,
                'discount_amount' => $amounts['discount_amount'],
            ];
        }

        return view('clients.pages.checkout', [
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'cartItems' => $cartItems,
            'subtotal' => $amounts['subtotal'],
            'shippingFee' => $amounts['shipping_fee'],
            'shippingDistanceKm' => $amounts['shipping_distance_km'],
            'shippingDurationSeconds' => $amounts['shipping_duration_seconds'],
            'discountAmount' => $amounts['discount_amount'],
            'totalPrice' => $amounts['total'],
            'appliedCoupon' => $appliedCoupon,
        ]);
    }

    public function getAddress(Request $request)
    {
        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ!']);
        }

        try {
            $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
            $coupon = $this->resolveCouponFromSession($cartItems);
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon, $address);

            return response()->json([
                'success' => true,
                'data' => $address,
                'amounts' => $this->formatAmountResponse($amounts, $coupon),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
            'address_id' => [
                'required',
                'integer',
                Rule::exists('shipping_addresses', 'id')->where('user_id', Auth::id()),
            ],
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng của bạn đang trống.',
            ], 422);
        }

        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$this->isCouponUsable($coupon, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        try {
            $address = $this->resolveCheckoutAddress($request);
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon, $address);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        if ($amounts['discount_amount'] <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng không đủ điều kiện để áp dụng mã này.',
            ], 422);
        }

        session(['checkout_coupon' => ['code' => $coupon->code]]);

        return response()->json([
            'status' => true,
            'message' => 'Áp dụng mã giảm giá thành công.',
            'data' => $this->formatAmountResponse($amounts, $coupon),
        ]);
    }

    public function removeCoupon(Request $request)
    {
        $request->validate([
            'address_id' => [
                'required',
                'integer',
                Rule::exists('shipping_addresses', 'id')->where('user_id', Auth::id()),
            ],
        ]);

        session()->forget('checkout_coupon');

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        try {
            $address = $this->resolveCheckoutAddress($request);
            $amounts = $this->calculateOrderAmounts($cartItems, null, $address);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đã gỡ mã giảm giá.',
            'data' => $this->formatAmountResponse($amounts, null),
        ]);
    }

    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'address_id' => [
                'required',
                'integer',
                Rule::exists('shipping_addresses', 'id')->where('user_id', Auth::id()),
            ],
            'payment_method' => ['required', Rule::in(['cash', 'vietqr'])],
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();

        try {
            $coupon = $this->resolveCouponFromSession($cartItems);
            $address = ShippingAddress::where('id', $data['address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon, $address);

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $data['address_id'];
            $order->subtotal = $amounts['subtotal'];
            $order->discount_amount = $amounts['discount_amount'];
            $order->shipping_fee = $amounts['shipping_fee'];
            $order->shipping_distance_km = $amounts['shipping_distance_km'];
            $order->shipping_duration_seconds = $amounts['shipping_duration_seconds'];
            $order->total_price = $amounts['total'];
            $order->status = 'pending';

            if ($coupon) {
                $order->coupon_id = $coupon->id;
                $order->coupon_code = $coupon->code;
            }

            $order->save();

            foreach ($cartItems as $item) {
                $product = $this->lockProductForCheckoutItem($item);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                ]);

                // Product row was locked before creating the order item.
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['payment_method'] === 'vietqr' ? app(VietQrService::class)->paymentContent($order) : null,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            CartItem::where('user_id', $user->id)->delete();

            if ($coupon) {
                $coupon->increment('times_used');
                $user->coupons()->updateExistingPivot($coupon->id, ['used_at' => now()]);
                session()->forget('checkout_coupon');
            }

            DB::commit();

            Notification::create([
                'user_id' => $user->id,
                'type' => 'order',
                'message' => "Có đơn đặt hàng mới từ " . $user->email,
                'link' => '/orders',
                'is_read' => 0,
            ]);

            toastr()->success('Đặt hàng thành công!');
            if ($data['payment_method'] === 'vietqr') {
                toastr()->info('Vui lòng quét mã VietQR trong chi tiết đơn hàng để thanh toán.');
                return redirect()->route('order.show', $order->id);
            }

            return redirect()->route('account');
        } catch (\RuntimeException $e) {
            DB::rollBack();
            toastr()->error($e->getMessage());
            return redirect()->route('checkout');
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng: ' . $e->getMessage());
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra, vui lòng thử lại! ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    public function placeOrderPayPal(Request $request)
    {
        $data = $request->validate([
            'address_id' => [
                'required',
                'integer',
                Rule::exists('shipping_addresses', 'id')->where('user_id', Auth::id()),
            ],
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng trống.'], 422);
            }

            $coupon = $this->resolveCouponFromSession($cartItems);
            $address = ShippingAddress::where('id', $data['address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon, $address);

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $data['address_id'];
            $order->subtotal = $amounts['subtotal'];
            $order->discount_amount = $amounts['discount_amount'];
            $order->shipping_fee = $amounts['shipping_fee'];
            $order->shipping_distance_km = $amounts['shipping_distance_km'];
            $order->shipping_duration_seconds = $amounts['shipping_duration_seconds'];
            $order->total_price = $amounts['total'];
            $order->status = 'pending';

            if ($coupon) {
                $order->coupon_id = $coupon->id;
                $order->coupon_code = $coupon->code;
            }

            $order->save();

            foreach ($cartItems as $item) {
                $product = $this->lockProductForCheckoutItem($item);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                ]);

                // Product row was locked before creating the order item.
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'transaction_id' => $request->transactionID,
                'amount' => $order->total_price,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            CartItem::where('user_id', $user->id)->delete();

            if ($coupon) {
                $coupon->increment('times_used');
                $user->coupons()->updateExistingPivot($coupon->id, ['used_at' => now()]);
                session()->forget('checkout_coupon');
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\RuntimeException $e) {
            Log::warning('Khong the tinh phi giao hang PayPal: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng PayPal: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    private function lockProductForCheckoutItem(CartItem $item): Product
    {
        $product = Product::whereKey($item->product_id)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new \Exception('Sản phẩm không tồn tại.');
        }

        if ($product->stock < $item->quantity) {
            throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
        }

        return $product;
    }

    private function resolveCouponFromSession(Collection $cartItems): ?Coupon
    {
        $sessionCoupon = session('checkout_coupon');

        if (!$sessionCoupon || empty($sessionCoupon['code'])) {
            return null;
        }

        $coupon = Coupon::where('code', Str::upper($sessionCoupon['code']))->first();

        if ($coupon && $this->isCouponUsable($coupon, Auth::user()) && $cartItems->isNotEmpty()) {
            return $coupon;
        }

        session()->forget('checkout_coupon');

        return null;
    }

    private function isCouponUsable(Coupon $coupon, ?\App\Models\User $user = null): bool
    {
        if (!$coupon->is_active) {
            return false;
        }

        if (!$coupon->hasStarted()) {
            return false;
        }

        if ($coupon->isExpired()) {
            return false;
        }

        if ($coupon->hasReachedUsageLimit()) {
            return false;
        }

        if ($coupon->restricted_to_assigned_users) {
            if (!$user) {
                return false;
            }

            return $user->coupons()->where('coupons.id', $coupon->id)->exists();
        }

        return true;
    }

    private function resolveCheckoutAddress(Request $request): ShippingAddress
    {
        return ShippingAddress::where('id', $request->input('address_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function formatAmountResponse(array $amounts, ?Coupon $coupon): array
    {
        return [
            'coupon_code' => $coupon?->code,
            'discount_percentage' => $coupon?->discount_percentage,
            'discount_amount' => $amounts['discount_amount'],
            'subtotal' => $amounts['subtotal'],
            'shipping_fee' => $amounts['shipping_fee'],
            'shipping_distance_km' => $amounts['shipping_distance_km'],
            'shipping_duration_seconds' => $amounts['shipping_duration_seconds'],
            'total' => $amounts['total'],
        ];
    }

    private function calculateOrderAmounts(
        Collection $cartItems,
        ?Coupon $coupon = null,
        ?ShippingAddress $shippingAddress = null
    ): array
    {
        $subtotal = (float) $cartItems->sum(fn($item) => $item->quantity * $item->product->current_price);
        $shippingQuote = [
            'shipping_fee' => 0.0,
            'distance_km' => null,
            'duration_seconds' => null,
        ];

        if ($subtotal > 0) {
            if (!$shippingAddress) {
                throw new \RuntimeException('Vui lòng chọn địa chỉ giao hàng.');
            }

            $shippingQuote = $this->shippingFeeService->quoteForAddress($shippingAddress);
        }

        $shippingFee = (float) $shippingQuote['shipping_fee'];
        $discountAmount = 0.0;

        if ($coupon && $subtotal > 0) {
            $discountAmount = round($subtotal * $coupon->discount_percentage / 100, 2);
            $discountAmount = min($discountAmount, $subtotal);
        }

        $total = max($subtotal + $shippingFee - $discountAmount, 0);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'shipping_distance_km' => $shippingQuote['distance_km'],
            'shipping_duration_seconds' => $shippingQuote['duration_seconds'],
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }
}
