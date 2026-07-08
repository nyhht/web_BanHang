<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'serving_size')) {
                $table->unsignedSmallInteger('serving_size')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('products', 'prep_time')) {
                $table->unsignedSmallInteger('prep_time')->nullable()->after('serving_size');
            }
            if (!Schema::hasColumn('products', 'cook_time')) {
                $table->unsignedSmallInteger('cook_time')->nullable()->after('prep_time');
            }
            if (!Schema::hasColumn('products', 'calories')) {
                $table->unsignedSmallInteger('calories')->nullable()->after('cook_time');
            }
            if (!Schema::hasColumn('products', 'storage_instruction')) {
                $table->text('storage_instruction')->nullable()->after('calories');
            }
            if (!Schema::hasColumn('products', 'expiry_days')) {
                $table->unsignedSmallInteger('expiry_days')->nullable()->after('storage_instruction');
            }
        });

        if (!Schema::hasTable('product_ingredients')) {
            Schema::create('product_ingredients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('name');
                $table->decimal('quantity', 8, 2)->nullable();
                $table->string('unit', 50)->nullable();
                $table->timestamps();

                $table->index('product_id');
            });
        }

        if (!Schema::hasTable('product_cooking_steps')) {
            Schema::create('product_cooking_steps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->unsignedSmallInteger('step_number');
                $table->text('instruction');
                $table->timestamps();

                $table->unique(['product_id', 'step_number']);
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE order_status_history MODIFY status ENUM('pending','processing','packed','ready_for_delivery','out_for_delivery','delivered','completed','canceled')");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE order_status_history MODIFY status ENUM('pending','processing','ready_for_delivery','out_for_delivery','delivered','completed','canceled')");
        }

        Schema::dropIfExists('product_cooking_steps');
        Schema::dropIfExists('product_ingredients');

        Schema::table('products', function (Blueprint $table) {
            $columns = collect([
                'serving_size',
                'prep_time',
                'cook_time',
                'calories',
                'storage_instruction',
                'expiry_days',
            ])->filter(fn ($column) => Schema::hasColumn('products', $column))->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
