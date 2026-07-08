<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm.');
        }

        $products = Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])
            ->where('name', 'LIKE', "%$keyword%")
            // ->orWhere('description', 'LIKE', "%$keyword%")
            ->paginate(12);

         // Call API on Python to get related Products
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/search-products';
            $response = Http::get($apiUrl, [
                'keyword' => $keyword
            ]);

            if ($response->successful()) {
                $listId = $response->json('related_products');
                // dd($listId);
                $products = Product::with(['category', 'firstImage', 'reviews', 'mealKitIngredients', 'cookingSteps'])->whereIn('id', $listId)->get();
            }
            
        } catch (\Exception $e) {
            Log::error('Error when call API: ' . $e->getMessage());
        }

        // dd($products);
        return view('clients.pages.products-search', compact('products'));
    }
}
