<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::with('assignedUsers')->orderByDesc('created_at')->get();
        $products = Product::with('category')
            ->where('status', 'in_stock')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();
        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->orderBy('name')->get();

        return view('admin.pages.coupons', compact('coupons', 'products', 'customers'));
    }

    public function store(Request $request, PromotionService $promotionService): RedirectResponse
    {
        $validated = $this->validateCoupon($request);

        if (!empty($validated['expires_at']) && Carbon::parse($validated['expires_at'])->isPast()) {
            return back()->withErrors(['expires_at' => 'Thời hạn phải lớn hơn thời gian hiện tại.'])->withInput();
        }

        $coupon = Coupon::create($this->prepareCouponData($request, $validated));
        $this->assignSelectedCustomers($coupon, $request->input('user_ids', []), $promotionService);
        $this->syncProductPromotions($coupon, $promotionService);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã tạo mã giảm giá mới thành công.');
    }

    public function update(Request $request, Coupon $coupon, PromotionService $promotionService): RedirectResponse
    {
        $validated = $this->validateCoupon($request, $coupon->id);

        if (!empty($validated['expires_at']) && Carbon::parse($validated['expires_at'])->isPast()) {
            return back()->withErrors(['expires_at' => 'Thời hạn phải lớn hơn thời gian hiện tại.'])->withInput();
        }

        $coupon->update($this->prepareCouponData($request, $validated));
        $this->assignSelectedCustomers($coupon, $request->input('user_ids', []), $promotionService);
        $this->syncProductPromotions($coupon, $promotionService);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã cập nhật mã giảm giá.');
    }

    public function destroy(Coupon $coupon, PromotionService $promotionService): RedirectResponse
    {
        $promotionService->clearProductPromotionsForCoupon($coupon);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa mã giảm giá.');
    }

    private function validateCoupon(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:coupons,code';

        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:50', $uniqueRule],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'restricted_to_assigned_users' => ['sometimes', 'boolean'],
            'auto_assign_on_register' => ['sometimes', 'boolean'],
            'auto_assign_weekend' => ['sometimes', 'boolean'],
            'auto_assign_dates_text' => ['nullable', 'string'],
            'notify_customers' => ['sometimes', 'boolean'],
            'auto_apply_to_products' => ['sometimes', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'daily_product_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'product_promotion_message' => ['nullable', 'string', 'max:1000'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);
    }

    private function prepareCouponData(Request $request, array $validated): array
    {
        return [
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'discount_percentage' => $validated['discount_percentage'],
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'restricted_to_assigned_users' => $request->boolean('restricted_to_assigned_users'),
            'auto_assign_on_register' => $request->boolean('auto_assign_on_register'),
            'auto_assign_weekend' => $request->boolean('auto_assign_weekend'),
            'auto_assign_dates' => $this->parseDates($request->input('auto_assign_dates_text')),
            'notify_customers' => $request->boolean('notify_customers', true),
            'auto_apply_to_products' => $request->boolean('auto_apply_to_products'),
            'product_ids' => $this->normalizeIds($request->input('product_ids', [])),
            'daily_product_limit' => $validated['daily_product_limit'] ?? null,
            'product_promotion_message' => $validated['product_promotion_message'] ?? null,
        ];
    }

    private function parseDates(?string $value): ?array
    {
        $dates = collect(preg_split('/[\s,;]+/', (string) $value))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->values()
            ->all();

        return $dates ?: null;
    }

    private function normalizeIds(array $ids): ?array
    {
        $ids = collect($ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return $ids ?: null;
    }

    private function assignSelectedCustomers(Coupon $coupon, array $userIds, PromotionService $promotionService): void
    {
        $normalizedIds = $this->normalizeIds($userIds);

        if (!$normalizedIds) {
            return;
        }

        $users = User::whereIn('id', $normalizedIds)->get();

        foreach ($users as $user) {
            $promotionService->assignCouponsToUser(collect([$coupon]), $user, 'admin');
        }
    }

    private function syncProductPromotions(Coupon $coupon, PromotionService $promotionService): void
    {
        $coupon->refresh();
        $promotionService->clearProductPromotionsForCoupon($coupon);

        if ($coupon->auto_apply_to_products) {
            $promotionService->applyProductPromotionForCoupon($coupon, now(), true);
        }
    }
}
