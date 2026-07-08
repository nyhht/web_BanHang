<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categories = [
            ['old_slug' => 'rau-cu', 'slug' => 'mon-gia-dinh', 'name' => 'Món gia đình', 'description' => 'Các set meal-kit món cơm nhà quen thuộc, sơ chế sẵn và dễ nấu.', 'image' => 'image\danhmuc\MonNgonGiaDinh.jpg'],
            ['old_slug' => 'trai-cay', 'slug' => 'eat-clean', 'name' => 'Eat clean', 'description' => 'Các bộ nguyên liệu cân bằng dinh dưỡng, phù hợp lối sống lành mạnh.', 'image' => 'image\danhmuc\RauCu.jpg'],
            ['old_slug' => 'thit', 'slug' => 'mon-nhanh-15-phut', 'name' => 'Món nhanh 15 phút', 'description' => 'Meal-kit tối giản thao tác, phù hợp bữa ăn nhanh trong ngày bận rộn.', 'image' => 'image\danhmuc\MonNgon.jpg'],
            ['old_slug' => 'ca', 'slug' => 'lau-nuong', 'name' => 'Lẩu/Nướng', 'description' => 'Set lẩu, nướng và món tụ họp được định lượng theo khẩu phần.', 'image' => 'image\danhmuc\HaiSan.jpg'],
            ['old_slug' => 'mon-ngon-gia-dinh', 'slug' => 'combo-tiet-kiem', 'name' => 'Combo tiết kiệm', 'description' => 'Các combo meal-kit tối ưu chi phí cho gia đình và nhóm nhỏ.', 'image' => 'image\danhmuc\MonNgonGiaDinh.jpg'],
        ];

        foreach ($categories as $category) {
            $oldCategory = DB::table('categories')->where('slug', $category['old_slug'])->first();
            $targetExists = DB::table('categories')
                ->where('slug', $category['slug'])
                ->when($oldCategory, fn ($query) => $query->where('id', '!=', $oldCategory->id))
                ->exists();

            $data = [
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'image' => $category['image'],
                'updated_at' => now(),
            ];

            if ($oldCategory && !$targetExists) {
                DB::table('categories')->where('id', $oldCategory->id)->update($data);
                continue;
            }

            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $data + ['created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $categories = [
            ['slug' => 'mon-gia-dinh', 'old_slug' => 'rau-cu', 'name' => 'Rau củ', 'description' => 'Các loại rau củ tươi ngon', 'image' => 'image\danhmuc\RauCu.jpg'],
            ['slug' => 'eat-clean', 'old_slug' => 'trai-cay', 'name' => 'Trái cây', 'description' => 'Trái cây sạch, tươi ngon', 'image' => 'image\danhmuc\TraiCay.jpg'],
            ['slug' => 'mon-nhanh-15-phut', 'old_slug' => 'thit', 'name' => 'Thịt', 'description' => 'Thịt tươi ngon, đảm bảo chất lượng', 'image' => 'image\danhmuc\Thit.jpg'],
            ['slug' => 'lau-nuong', 'old_slug' => 'ca', 'name' => 'Cá', 'description' => 'Hải sản và cá tươi sống', 'image' => 'image\danhmuc\HaiSan.jpg'],
            ['slug' => 'combo-tiet-kiem', 'old_slug' => 'mon-ngon-gia-dinh', 'name' => 'Món ngon gia đình', 'description' => 'Các gói nguyên liệu nấu món cơm nhà chuẩn vị, nhanh chóng và ấm cúng.', 'image' => 'image\danhmuc\MonNgonGiaDinh.jpg'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->where('slug', $category['slug'])->update([
                'name' => $category['name'],
                'slug' => $category['old_slug'],
                'description' => $category['description'],
                'image' => $category['image'],
                'updated_at' => now(),
            ]);
        }
    }
};
