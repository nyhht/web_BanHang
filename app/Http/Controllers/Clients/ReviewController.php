<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        return view('clients.components.includes.review-list', compact('product'))->render();
    }

    public function createReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $completedPurchaseCount = OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', Auth::id())->where('status', Order::STATUS_COMPLETED);
        })->where('product_id', $request->product_id)->count();

        $reviewCount = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->count();

        if ($completedPurchaseCount === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn cần mua và hoàn thành đơn hàng trước khi đánh giá sản phẩm này.'
            ], 403);
        }

        if ($reviewCount >= $completedPurchaseCount) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã đánh giá đủ số lần mua sản phẩm này.'
            ], 403);
        }

        $review = new Review();
        $review->user_id = Auth::id();
        $review->product_id = $request->product_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        return response()->json([
            'status' => true,
            'message' => 'Cảm ơn bạn đã gửi đánh giá!'
        ], 200);
    }
}
