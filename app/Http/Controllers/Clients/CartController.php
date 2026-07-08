<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->merge(['quantity' => (int) $request->quantity]);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Số lượng vượt quá tồn kho'], 400);
        }

        //If logged in,  save to database
        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);
            }

            $cartCount = CartItem::where('user_id', Auth::id())->count();
        } else {
            // If not logged in, save to session
            $cart = session()->get('cart', []);

            if (isset($cart[$request->product_id])) {
                $cart[$request->product_id]['quantity'] += $request->quantity;
                $cart[$request->product_id]['price'] = $product->current_price;
                $cart[$request->product_id]['stock'] = $product->stock;
            } else {
                $cart[$request->product_id] = [
                    'product_id' => $request->product_id,
                    'name' => $product->name,
                    'price' => $product->current_price,
                    'quantity' => $request->quantity,
                    'stock' => $product->stock,
                    'image' => $product->images->first()->image ?? 'uploads/products/default-product.png'
                ];
            }

            session()->put('cart', $cart);
            $cartCount = count($cart);
        }

        return response()->json([
            'message' => true,
            'cart_count' => $cartCount
        ]);
    }

    public function loadMiniCart()
    {
        $cartItems = [];

        if (Auth::check()) {
            $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        } else {
            $cartItems = session('cart', []);
        }
        return response()->json([
            'status' => true,
            'html' => view('clients.components.includes.mini_cart', compact('cartItems'))->render()
        ]);
    }

    public function removeFormMiniCart(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $request->product_id)->delete();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
        } else {
            // If not logged in, save to session
            $cart = session()->get('cart', []);
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
            $cartCount = count($cart);
        }

        return response()->json([
            'status' => true,
            'cart_count' => $cartCount
        ]);
    }

    //View Cart
    public function viewCart()
    {
        if (Auth::check()) {
            //Get cart from database
            $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get()->map(function ($item) {
                return [
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->current_price,
                    'quantity' => $item->quantity,
                    'stock' => $item->product->stock,
                    'image' => $item->product->images->first()->image ?? 'uploads/products/default-product.png',
                ];
            })->toArray();
        } else {
            //Get cart from session
            $cartItems = $this->refreshSessionCartPrices(session()->get('cart', []));
            session()->put('cart', $cartItems);
        }

        return view('clients.pages.cart', compact('cartItems'));
    }

    //Hanlde update product in Page Cart
    public function updateCart(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if (Auth::check()) {
            //Update Cart in database
            $cartItems = CartItem::where('user_id', Auth::id())->where('product_id', $productId)->first();
            if (!$cartItems) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $product = Product::find($productId);

            if ($quantity > $product->stock) {
                return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
            }

            $cartItems->quantity = $quantity;
            $cartItems->save();
        } else {
            //Update Cart in session
            $cart = session()->get('cart', []);

            if (!isset($cart[$productId])) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $product = Product::find($productId);

            if ($quantity > $product->stock) {
                return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
            }

            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['price'] = $product->current_price;
            $cart[$productId]['stock'] = $product->stock;
            session()->put('cart', $cart);
        }
        //Calculate cartToltal again
        $subtotal = $quantity * $product->current_price;
        $total = $this->calculateCartTotal();

        return response()->json([
            'quantity' => $quantity,
            'subtotal' => number_format($subtotal, 0, ',', '.'),
            'total' => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($total, 0, ',', '.'),
        ]);
    }

    //Hanlde remove quantity product in Page Cart
    public function removeCartItem(Request $request)
    {
        $productId = $request->product_id;

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $productId)->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        
        //Calculate cartToltal again
        $total = $this->calculateCartTotal();

        return response()->json([
            'total' => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($total, 0, ',', '.'),
        ]);
    }

    function calculateCartTotal()
    {
        if (Auth::check()) {    
            return CartItem::where('user_id', Auth::id())->with('product')->get()->sum(fn($item) => $item->quantity * $item->product->current_price);
        } else {
            $cart = session()->get('cart', []);
            return collect($this->refreshSessionCartPrices($cart))->sum(fn($item) => $item['quantity'] * $item['price']);
        }
    }

    private function refreshSessionCartPrices(array $cart): array
    {
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product) {
                unset($cart[$productId]);
                continue;
            }

            $cart[$productId]['price'] = $product->current_price;
            $cart[$productId]['stock'] = $product->stock;
            $cart[$productId]['image'] = $product->images->first()->image ?? ($item['image'] ?? 'uploads/products/default-product.png');
        }

        return $cart;
    }
}
