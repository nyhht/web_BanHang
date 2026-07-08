<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $products = Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->promotedFirst()
            ->paginate(9);
        $productsHighRate = Product::with(['firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit(2)
            ->get();

        return view('clients.pages.products', compact('categories', 'products', 'productsHighRate'));
    }

    public function filter(Request $request)
    {
        $query = Product::query();

        //Filter Category if exist
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $salePriceExpression = $this->salePriceExpression();

        //Filter Price if exist
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereRaw($salePriceExpression . ' BETWEEN ? AND ?', [$request->min_price, $request->max_price]);
        }

        //Filter SortBy if exist
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderByRaw($salePriceExpression . ' ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw($salePriceExpression . ' DESC');
                    break;
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->promotedFirst()->orderBy('id', 'desc');
                    break;
            }
        } else {
            $query->promotedFirst()->orderBy('id', 'desc');
        }

        $products = $query->with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->paginate(9);

        return response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => (string) $products->links('clients.components.pagination.pagination_custom')
        ]);
    }

    public function detail(string $slug)
    {
        $product = Product::with(['category', 'images', 'reviews.user', 'mealKitIngredients', 'cookingSteps'])->where('slug', $slug)->firstOrFail();

        //Get products in the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();

        // Call API on Python to get related Products
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/product-recommendation';
            $response = Http::get($apiUrl, [
                'product_id' => $product->id
            ]);

            if ($response->successful()) {
                $listId = $response->json('related_products');
                
                $relatedProducts = Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])->whereIn('id', $listId)->get();
            }
        } catch (\Exception $e) {
            Log::error('Error when call API: ' . $e->getMessage());
        }

        //Calculate average rating, ensure no null
        $averageRating = round($product->reviews()->avg('rating') ?? 0, 1);

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $user = Auth::user();

            $completedPurchaseCount = OrderItem::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', Order::STATUS_COMPLETED);
            })->where('product_id', $product->id)->count();

            $reviewCount = Review::where('user_id', $user->id)->where('product_id', $product->id)->count();
            $hasPurchased = $completedPurchaseCount > 0;
            $hasReviewed = $reviewCount >= $completedPurchaseCount;

        }

        return view('clients.pages.product-detail', compact('product', 'relatedProducts', 'hasPurchased', 'hasReviewed', 'averageRating'));
    }

    private function salePriceExpression(): string
    {
        return "CASE WHEN sale_price IS NOT NULL AND sale_price < price AND (sale_starts_at IS NULL OR sale_starts_at <= NOW()) AND (sale_ends_at IS NULL OR sale_ends_at > NOW()) THEN sale_price ELSE price END";
    }
}
