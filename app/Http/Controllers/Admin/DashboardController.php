<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('admin')->user()?->role?->name === 'delivery_staff') {
            return redirect()->route('admin.deliveries.index');
        }

        $revenuePeriods = [
            'day' => 'Ngày',
            'week' => 'Tuần',
            'month' => 'Tháng',
            'year' => 'Năm',
        ];
        $revenuePeriod = $request->query('revenue_period', 'month');
        if (! array_key_exists($revenuePeriod, $revenuePeriods)) {
            $revenuePeriod = 'month';
        }

        $users = User::where('role_id', 3)->latest()->get();
        $categories = Category::with('products')->get();
        $products = Product::where('stock', '>', 0)->get();
        $orders = Order::with('shippingAddress')->latest()->get();

        //Get list top3 best seller products

        $topSellingProducts = Product::withCount(['orderItems as total_sold' => function ($query){
            $query->select(DB::raw("SUM(quantity)"));
        }])->orderByDesc('total_sold')->take(3)->get();

        $revenueExpression = $this->revenuePeriodExpression($revenuePeriod);
        $revenueData = Order::select(
            DB::raw("SUM(total_price) as revenue"),
            DB::raw($revenueExpression . ' as period')
        )
        ->groupBy('period')
        ->orderBy('period', 'ASC')
        ->get();

        return view('admin.pages.dashboard', compact('users', 'categories', 'products', 'orders', 'topSellingProducts', 'revenueData', 'revenuePeriods', 'revenuePeriod'));
    }

    private function revenuePeriodExpression(string $period): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($period) {
            'day' => $driver === 'sqlite'
                ? "strftime('%Y-%m-%d', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m-%d')",
            'week' => $driver === 'sqlite'
                ? "strftime('%Y-W%W', created_at)"
                : "DATE_FORMAT(created_at, '%x-W%v')",
            'year' => $driver === 'sqlite'
                ? "strftime('%Y', created_at)"
                : "DATE_FORMAT(created_at, '%Y')",
            default => $driver === 'sqlite'
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')",
        };
    }
}
