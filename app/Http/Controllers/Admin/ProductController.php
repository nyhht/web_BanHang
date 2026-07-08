<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function showFormAddProduct()
    {
        $categories = Category::all();
        return view('admin.pages.product-add', compact('categories'));
    }

    public function addProduct(Request $request)
    {
        $this->validateProduct($request);

        $stock = (int) ($request->stock ?? 0);
        $slug = Str::slug($request->name) . '-' . time();
        $mealKitDetails = $this->normalizedMealKitDetails($request);
        $legacyIngredients = $request->input('legacy_ingredients', $request->input('ingredients'));
        $legacyCookingInstructions = $request->input('legacy_cooking_instructions', $request->input('cooking_instructions'));

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'ingredients' => $mealKitDetails['ingredients_text'] ?: $legacyIngredients,
            'cooking_instructions' => $mealKitDetails['cooking_text'] ?: $legacyCookingInstructions,
            'serving_size' => $request->serving_size,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'calories' => $request->calories,
            'storage_instruction' => $request->storage_instruction,
            'expiry_days' => $request->expiry_days,
            'price' => $request->price,
            'stock' => $stock,
            'unit' => $request->unit ?? 'hộp',
            'status' => $stock > 0 ? 'in_stock' : 'out_of_stock',
        ]);

        $this->syncMealKitDetails($product, $mealKitDetails);
        $this->storeProductImages($request, $product);

        return redirect()->route('admin.product.add')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function index()
    {
        $products = Product::with('category', 'images', 'mealKitIngredients', 'cookingSteps')->get();
        $categories = Category::all();
        return view('admin.pages.products', compact('products', 'categories'));
    }

    public function updateProduct(Request $request)
    {
        $this->validateProduct($request, true);

        $stock = (int) ($request->stock ?? 0);
        $product = Product::findOrFail($request->id);
        $mealKitDetails = $this->normalizedMealKitDetails($request);
        $legacyIngredients = $request->input('legacy_ingredients', $request->input('ingredients'));
        $legacyCookingInstructions = $request->input('legacy_cooking_instructions', $request->input('cooking_instructions'));

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'ingredients' => $mealKitDetails['ingredients_text'] ?: $legacyIngredients,
            'cooking_instructions' => $mealKitDetails['cooking_text'] ?: $legacyCookingInstructions,
            'serving_size' => $request->serving_size,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'calories' => $request->calories,
            'storage_instruction' => $request->storage_instruction,
            'expiry_days' => $request->expiry_days,
            'price' => $request->price,
            'stock' => $stock,
            'unit' => $request->unit ?? 'hộp',
            'status' => $stock > 0 ? 'in_stock' : 'out_of_stock',
        ]);

        $this->syncMealKitDetails($product, $mealKitDetails);

        if ($request->hasFile('images')) {
            foreach (ProductImage::where('product_id', $product->id)->get() as $image) {
                Storage::disk('public')->delete($image->image);
            }

            ProductImage::where('product_id', $product->id)->delete();
            $this->storeProductImages($request, $product);
        }

        $product->load('category', 'images', 'mealKitIngredients', 'cookingSteps');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_name' => $product->category->name,
                'description' => $product->description,
                'ingredients' => $product->ingredients,
                'cooking_instructions' => $product->cooking_instructions,
                'serving_size' => $product->serving_size,
                'prep_time' => $product->prep_time,
                'cook_time' => $product->cook_time,
                'calories' => $product->calories,
                'storage_instruction' => $product->storage_instruction,
                'expiry_days' => $product->expiry_days,
                'price' => $product->price,
                'stock' => $product->stock,
                'unit' => $product->unit,
                'status' => $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng',
                'images' => $product->images->map(fn ($img) => asset('storage/' . $img->image)),
                'meal_kit_ingredients' => $product->mealKitIngredients->map(fn ($ingredient) => [
                    'name' => $ingredient->name,
                    'quantity' => $ingredient->quantity,
                    'unit' => $ingredient->unit,
                ]),
                'cooking_steps' => $product->cookingSteps->map(fn ($step) => [
                    'instruction' => $step->instruction,
                ]),
            ],
        ]);
    }

    public function deleteProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->id);

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công!',
        ]);
    }

    private function validateProduct(Request $request, bool $isUpdate = false): void
    {
        $request->validate([
            'id' => $isUpdate ? 'required|exists:products,id' : 'nullable',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'cooking_instructions' => 'nullable|string',
            'legacy_ingredients' => 'nullable|string',
            'legacy_cooking_instructions' => 'nullable|string',
            'product_ingredients_json' => 'nullable|string',
            'cooking_steps_json' => 'nullable|string',
            'serving_size' => 'nullable|integer|min:1|max:50',
            'prep_time' => 'nullable|integer|min:0|max:1440',
            'cook_time' => 'nullable|integer|min:0|max:1440',
            'calories' => 'nullable|integer|min:0|max:10000',
            'storage_instruction' => 'nullable|string',
            'expiry_days' => 'nullable|integer|min:0|max:365',
            'product_ingredients' => 'nullable|array',
            'product_ingredients.*.name' => 'nullable|string|max:255',
            'product_ingredients.*.quantity' => 'nullable|numeric|min:0',
            'product_ingredients.*.unit' => 'nullable|string|max:50',
            'cooking_steps' => 'nullable|array',
            'cooking_steps.*.instruction' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image',
        ]);
    }

    private function normalizedMealKitDetails(Request $request): array
    {
        $ingredientRows = $request->filled('product_ingredients_json')
            ? $this->decodedMealKitRows($request->input('product_ingredients_json'))
            : $request->input('product_ingredients', []);

        $stepRows = $request->filled('cooking_steps_json')
            ? $this->decodedMealKitRows($request->input('cooking_steps_json'))
            : $request->input('cooking_steps', []);

        $ingredients = collect($ingredientRows)
            ->filter(fn ($ingredient) => is_array($ingredient))
            ->map(function (array $ingredient) {
                $quantity = $ingredient['quantity'] ?? null;

                return [
                    'name' => trim((string) ($ingredient['name'] ?? '')),
                    'quantity' => $quantity === '' || $quantity === null ? null : $quantity,
                    'unit' => trim((string) ($ingredient['unit'] ?? '')) ?: null,
                ];
            })
            ->filter(fn (array $ingredient) => $ingredient['name'] !== '')
            ->values();

        $steps = collect($stepRows)
            ->filter(fn ($step) => is_array($step))
            ->map(fn (array $step) => trim((string) ($step['instruction'] ?? '')))
            ->filter()
            ->values()
            ->map(fn (string $instruction, int $index) => [
                'step_number' => $index + 1,
                'instruction' => $instruction,
            ]);

        return [
            'ingredients' => $ingredients,
            'cooking_steps' => $steps,
            'ingredients_text' => $ingredients->map(function (array $ingredient) {
                $amount = trim(collect([$ingredient['quantity'], $ingredient['unit']])->filter()->implode(' '));
                return trim($ingredient['name'] . ($amount ? ': ' . $amount : ''));
            })->implode("\n") ?: null,
            'cooking_text' => $steps->map(fn (array $step) => $step['step_number'] . '. ' . $step['instruction'])->implode("\n") ?: null,
        ];
    }

    private function decodedMealKitRows(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $rows = json_decode($json, true);

        return is_array($rows) ? $rows : [];
    }

    private function syncMealKitDetails(Product $product, array $mealKitDetails): void
    {
        $product->mealKitIngredients()->delete();
        if ($mealKitDetails['ingredients']->isNotEmpty()) {
            $product->mealKitIngredients()->createMany($mealKitDetails['ingredients']->all());
        }

        $product->cookingSteps()->delete();
        if ($mealKitDetails['cooking_steps']->isNotEmpty()) {
            $product->cookingSteps()->createMany($mealKitDetails['cooking_steps']->all());
        }
    }

    private function storeProductImages(Request $request, Product $product): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'uploads/products/' . $imageName;

            $resizedImage = Image::make($image)
                ->resize(600, 600)
                ->encode();

            Storage::disk('public')->put($path, $resizedImage);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
            ]);
        }
    }
}
