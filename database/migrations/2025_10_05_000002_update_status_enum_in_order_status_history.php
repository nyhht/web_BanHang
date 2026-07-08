<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE order_status_history MODIFY status ENUM('pending','processing','ready_for_delivery','out_for_delivery','delivered','completed','canceled')");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE order_status_history MODIFY status ENUM('pending','processing','shipped','completed','cancelled')");
    }
};
