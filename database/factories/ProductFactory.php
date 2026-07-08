<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            // ---  MEAL-KIT
            'Combo Meal-kit Thịt nấu đông (Chân giò, mộc nhĩ, nấm hương & tiêu Bắc)',
            'Combo Meal-kit Nem rán thập cẩm (Bánh ram Hà Tĩnh, thịt xay, tôm tươi & miến)',
            'Combo Meal-kit Thịt kho tàu cốt dừa (Thịt ba chỉ, trứng cút & nước dừa tươi)',
            'Combo Meal-kit Giò thủ xào (Tai heo, lưỡi heo, thịt chân giò & nấm mèo)',
            'Combo Meal-kit Gỏi cuốn tôm thịt (Tôm tươi, ba chỉ, bánh tráng & rau sống)',
            'Combo Meal-kit Nộm gà thập cẩm (Ức gà xé, hoa chuối, xoài xanh & lạc rang)',
            'Combo Meal-kit Miến trộn thập cẩm Hàn Quốc (Miến, thăn bò, cải bó xôi & nấm)',

            // --- CÁC NGUYÊN LIỆU LẺ
            'Cà chua bi hữu cơ', 'Rau muống chẻ', 'Bí đỏ hồ lô', 'Khoai tây Đà Lạt', 'Táo xanh nhập khẩu', 
            'Xà lách thủy canh', 'Dưa leo sạch', 'Ớt chuông đỏ', 'Cà rốt Đà Lạt', 'Bắp cải trái tim', 
            'Nho đen không hạt', 'Chuối chín tự nhiên', 'Bưởi da xanh', 'Cam sành Hàm Yên', 'Dưa hấu Long An', 
            'Hành lá & Ngò rí', 'Tỏi Lý Sơn', 'Gừng tươi', 'Khoai lang mật', 'Mướp đắng rừng', 
            'Thịt heo ba rọi sinh học', 'Thịt bò Úc phi lê', 'Cá hồi Na Uy phi lê', 'Tôm sú tươi', 'Gà ta nguyên con sạch'
        ]);

        return [
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1,1000),
            'category_id' => Category::inRandomOrder()->first()->id,
            'description'  => $this->faker->sentence(10),
            'serving_size' => $this->faker->randomElement([2, 3, 4]),
            'prep_time' => $this->faker->numberBetween(5, 20),
            'cook_time' => $this->faker->numberBetween(10, 35),
            'calories' => $this->faker->numberBetween(350, 850),
            'storage_instruction' => 'Bao quan lanh 2-6 do C, su dung ngay sau khi mo goi.',
            'expiry_days' => $this->faker->numberBetween(1, 3),
            'price'       => $this->faker->randomFloat(2, 10000, 200000),
            'stock'       => $this->faker->numberBetween(0,100),
            'status'      => $this->faker->randomElement(['in_stock', 'out_of_stock']),
            'unit'        => $this->faker->randomElement(['kg', 'bó', 'túi', 'hộp'])
        ];
    }
}
