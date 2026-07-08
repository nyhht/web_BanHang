<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminStaffTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::whereIn('name', ['admin', 'staff', 'delivery_staff'])
            ->pluck('id', 'name');

        if ($roles->isEmpty()) {
            $this->command?->warn('Roles not found. Please seed RolesTableSeeder first.');
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin9@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'),
                'phone_number' => '099999999',
                'status' => 'active',
                'avatar' => '',
                'address' => 'Ha Noi, Vietnam',
                'role_id' => $roles['admin'] ?? $roles->first(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('123456'),
                'phone_number' => '088888888',
                'status' => 'active',
                'avatar' => '',
                'address' => 'Ha Noi, Vietnam',
                'role_id' => $roles['staff'] ?? $roles->first(),
            ]
        );

        if ($roles->has('delivery_staff')) {
            User::updateOrCreate(
                ['email' => 'delivery@example.com'],
                [
                    'name' => 'Delivery User',
                    'password' => Hash::make('123456'),
                    'phone_number' => '077777777',
                    'status' => 'active',
                    'avatar' => '',
                    'address' => 'Ha Noi, Vietnam',
                    'role_id' => $roles['delivery_staff'],
                ]
            );
        }
    }
}