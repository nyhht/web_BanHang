<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained('shipping_addresses')->nullOnDelete();
            $table->string('frequency', 20);
            $table->unsignedTinyInteger('week_day')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('preferred_delivery_time')->default('08:00:00');
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_order_generated_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->string('payment_method', 20)->default('cash');
            $table->decimal('estimated_subtotal', 10, 2)->default(0);
            $table->decimal('estimated_shipping_fee', 10, 2)->default(0);
            $table->decimal('estimated_total', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price_snapshot', 10, 2);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('user_id')->constrained('subscriptions')->nullOnDelete();
            $table->string('order_type', 20)->default('normal')->after('subscription_id');
            $table->date('scheduled_delivery_date')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'subscription_id')) {
                $table->dropConstrainedForeignId('subscription_id');
            }

            if (Schema::hasColumn('orders', 'order_type')) {
                $table->dropColumn('order_type');
            }

            if (Schema::hasColumn('orders', 'scheduled_delivery_date')) {
                $table->dropColumn('scheduled_delivery_date');
            }
        });

        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
    }
};
