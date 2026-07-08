<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $categories = $this->getHomeCategories();
        $bestSellingProducts = $this->getBestSellingProducts();
        $promotedProducts = Product::with(['firstImage', 'reviews'])
            ->with(['category', 'mealKitIngredients', 'cookingSteps'])
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->onSale()
            ->promotedFirst()
            ->take(8)
            ->get();

        return view('clients.pages.home', compact('categories', 'bestSellingProducts', 'promotedProducts'));
    }

    private function getHomeCategories()
    {
        return Category::with([
            'products' => function ($query) {
                $query->with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
                    ->where('status', 'in_stock')
                    ->promotedFirst()
                    ->latest();
            },
        ])->get();
    }

    private function getBestSellingProducts()
    {
        $bestSellingIds = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->pluck('product_id');

        if ($bestSellingIds->isEmpty()) {
            return Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
                ->where('status', 'in_stock')
                ->promotedFirst()
                ->latest()
                ->take(8)
                ->get();
        }

        return Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->whereIn('id', $bestSellingIds)
            ->where('status', 'in_stock')
            ->get()
            ->sortBy(fn ($product) => $bestSellingIds->search($product->id))
            ->values();
    }

    private function hasStaleCategoryCache(Collection $categories): bool
    {
        return $categories->sum(fn ($category) => $category->products->count()) === 0
            && Product::where('status', 'in_stock')->exists();
    }
}
