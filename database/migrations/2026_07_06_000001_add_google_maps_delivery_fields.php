<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('city');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('google_place_id')->nullable()->after('longitude');
            $table->string('formatted_address')->nullable()->after('google_place_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_distance_km', 8, 2)->nullable()->after('shipping_fee');
            $table->unsignedInteger('shipping_duration_seconds')->nullable()->after('shipping_distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_distance_km', 'shipping_duration_seconds']);
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'google_place_id', 'formatted_address']);
        });
    }
};
