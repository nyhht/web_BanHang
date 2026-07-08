<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('user_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('discount_amount');
            $table->foreignId('coupon_id')->nullable()->after('shipping_fee')->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['subtotal', 'discount_amount', 'shipping_fee', 'coupon_id', 'coupon_code']);
        });
    }
};