<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Món gia đình', 'slug' => 'mon-gia-dinh', 'description' => 'Các set meal-kit món cơm nhà quen thuộc, sơ chế sẵn và dễ nấu.', 'image' => 'image\danhmuc\MonNgonGiaDinh.jpg'],
            ['name' => 'Eat clean', 'slug' => 'eat-clean', 'description' => 'Các bộ nguyên liệu cân bằng dinh dưỡng, phù hợp lối sống lành mạnh.', 'image' => 'image\danhmuc\RauCu.jpg'],
            ['name' => 'Món nhanh 15 phút', 'slug' => 'mon-nhanh-15-phut', 'description' => 'Meal-kit tối giản thao tác, phù hợp bữa ăn nhanh trong ngày bận rộn.', 'image' => 'image\danhmuc\MonNgon.jpg'],
            ['name' => 'Lẩu/Nướng', 'slug' => 'lau-nuong', 'description' => 'Set lẩu, nướng và món tụ họp được định lượng theo khẩu phần.', 'image' => 'image\danhmuc\HaiSan.jpg'],
            ['name' => 'Combo tiết kiệm', 'slug' => 'combo-tiet-kiem', 'description' => 'Các combo meal-kit tối ưu chi phí cho gia đình và nhóm nhỏ.', 'image' => 'image\danhmuc\MonNgonGiaDinh.jpg'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
