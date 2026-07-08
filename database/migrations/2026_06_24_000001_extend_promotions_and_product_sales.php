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
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'description')) {
                $table->string('description')->nullable()->after('code');
            }
            if (!Schema::hasColumn('coupons', 'starts_at')) {
                $table->dateTime('starts_at')->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('coupons', 'restricted_to_assigned_users')) {
                $table->boolean('restricted_to_assigned_users')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('coupons', 'auto_assign_on_register')) {
                $table->boolean('auto_assign_on_register')->default(false)->after('restricted_to_assigned_users');
            }
            if (!Schema::hasColumn('coupons', 'auto_assign_weekend')) {
                $table->boolean('auto_assign_weekend')->default(false)->after('auto_assign_on_register');
            }
            if (!Schema::hasColumn('coupons', 'auto_assign_dates')) {
                $table->json('auto_assign_dates')->nullable()->after('auto_assign_weekend');
            }
            if (!Schema::hasColumn('coupons', 'notify_customers')) {
                $table->boolean('notify_customers')->default(true)->after('auto_assign_dates');
            }
            if (!Schema::hasColumn('coupons', 'auto_apply_to_products')) {
                $table->boolean('auto_apply_to_products')->default(false)->after('notify_customers');
            }
            if (!Schema::hasColumn('coupons', 'product_ids')) {
                $table->json('product_ids')->nullable()->after('auto_apply_to_products');
            }
            if (!Schema::hasColumn('coupons', 'daily_product_limit')) {
                $table->unsignedSmallInteger('daily_product_limit')->nullable()->after('product_ids');
            }
            if (!Schema::hasColumn('coupons', 'last_product_promotion_at')) {
                $table->dateTime('last_product_promotion_at')->nullable()->after('daily_product_limit');
            }
            if (!Schema::hasColumn('coupons', 'product_promotion_message')) {
                $table->text('product_promotion_message')->nullable()->after('last_product_promotion_at');
            }
        });

        if (!Schema::hasTable('coupon_user')) {
            Schema::create('coupon_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('assigned_reason')->nullable();
                $table->dateTime('notified_at')->nullable();
                $table->dateTime('used_at')->nullable();
                $table->timestamps();

                $table->unique(['coupon_id', 'user_id']);
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'sale_discount_percentage')) {
                $table->unsignedTinyInteger('sale_discount_percentage')->nullable()->after('sale_price');
            }
            if (!Schema::hasColumn('products', 'sale_starts_at')) {
                $table->dateTime('sale_starts_at')->nullable()->after('sale_discount_percentage');
            }
            if (!Schema::hasColumn('products', 'sale_ends_at')) {
                $table->dateTime('sale_ends_at')->nullable()->after('sale_starts_at');
            }
            if (!Schema::hasColumn('products', 'sale_coupon_id')) {
                $table->foreignId('sale_coupon_id')->nullable()->after('sale_ends_at')->constrained('coupons')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'promoted_at')) {
                $table->dateTime('promoted_at')->nullable()->after('sale_coupon_id');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('notifications', 'image')) {
                $table->string('image')->nullable()->after('message');
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('link');
            }
            if (!Schema::hasColumn('notifications', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $columns = collect(['title', 'image', 'data', 'expires_at'])
                ->filter(fn ($column) => Schema::hasColumn('notifications', $column))
                ->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sale_coupon_id')) {
                $table->dropForeign(['sale_coupon_id']);
            }

            $columns = collect([
                'sale_price',
                'sale_discount_percentage',
                'sale_starts_at',
                'sale_ends_at',
                'sale_coupon_id',
                'promoted_at',
            ])->filter(fn ($column) => Schema::hasColumn('products', $column))->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('coupon_user');

        Schema::table('coupons', function (Blueprint $table) {
            $columns = collect([
                'description',
                'starts_at',
                'restricted_to_assigned_users',
                'auto_assign_on_register',
                'auto_assign_weekend',
                'auto_assign_dates',
                'notify_customers',
                'auto_apply_to_products',
                'product_ids',
                'daily_product_limit',
                'last_product_promotion_at',
                'product_promotion_message',
            ])->filter(fn ($column) => Schema::hasColumn('coupons', $column))->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
