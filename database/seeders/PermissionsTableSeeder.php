<?php

namespace Database\Seeders\it\database\seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage_users',
            'manage_products',
            'manage_orders',
            'manage_categories',
            'manage_contacts',
            'manage_deliveries',
            'manage_coupons',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}