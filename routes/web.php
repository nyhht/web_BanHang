<?php

use App\Http\Controllers\Clients\CartController;
use App\Http\Controllers\Clients\CheckoutController;
use App\Http\Controllers\Clients\AccountController;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\ChatController;
use App\Http\Controllers\Clients\ContactController;
use App\Http\Controllers\Clients\ForgotPasswordController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\NotificationController;
use App\Http\Controllers\Clients\OrderController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\ResetPasswordController;
use App\Http\Controllers\Clients\ReviewController;
use App\Http\Controllers\Clients\SearchController;
use App\Http\Controllers\Clients\SubscriptionController;
use App\Http\Controllers\Clients\WishListController;
use App\Http\Middleware\DefaultClientData;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Scalar\MagicConst\Dir;



Route::prefix('/')->middleware([DefaultClientData::class])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/about', function () {
        return view('clients.pages.about');
    })->name('about');

    Route::get('/service', function () {
        return view('clients.pages.service');
    })->name('service');

    Route::get('/team', function () {
        return view('clients.pages.team');
    })->name('team');

    Route::get('/faq', function () {
        return view('clients.pages.faq');
    })->name('faq');

    //guest

    Route::middleware('guest')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('post-register');

        Route::get('/login', [AuthController::class, 'showloginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('post-login');

        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
    });

    Route::get('/activate/{token}', [AuthController::class, 'activate'])->name('activate');


    Route::middleware(['auth.custom'])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('client.notifications.read');
        Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('client.notifications.markRead');

        Route::prefix('account')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('account');
            Route::put('/update', [AccountController::class, 'update'])->name('account.update');
            Route::post('/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');

            Route::post('/addresses', [AccountController::class, 'addAddress'])->name('account.addresses.add');
            Route::put('/addresses/{id}', [AccountController::class, 'updatePrimaryAddress'])->name('account.addresses.update');
            Route::delete('/addresses/{id}', [AccountController::class, 'deleteAddress'])->name('account.addresses.delete');

        });

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::get('/checkout/get-address', [CheckoutController::class, 'getAddress']);
        Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
        Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');
        Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
        Route::post('/checkout/paypal', [CheckoutController::class, 'placeOrderPayPal'])->name('checkout.placeOrderPayPal');

        Route::get('/order/{id}', [OrderController::class, 'showOrder'])->name('order.show');
        Route::post('/order/{id}/received', [OrderController::class, 'received'])->name('order.received');
        Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::post('/subscriptions/{id}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
        Route::post('/subscriptions/{id}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
        Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        Route::post('/review', [ReviewController::class, 'createReview']);
        Route::get('/review/{product}', [ReviewController::class, 'index']);

        Route::get('/wishlist', [WishListController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/add', [WishListController::class, 'addToWishList']);
        Route::post('/wishlist/remove', [WishListController::class, 'removeWishListItem']);

    });

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');

    //Detai product
    Route::get('/product/{slug}', [ProductController::class, 'detail'])->name('product.detail');


    //Handle Cart
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'removeFormMiniCart'])->name('cart.remove-mini');
    Route::get('/mini-cart', [CartController::class, 'loadMiniCart'])->name('cart.mini');

    //Handle Page Cart
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove-cart', [CartController::class, 'removeCartItem'])->name('cart.remove');

    //Handle Contact
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'sendContact'])->name('contact');


    //Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    //Chat AI
    Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});


require __DIR__ . '/admin.php';
