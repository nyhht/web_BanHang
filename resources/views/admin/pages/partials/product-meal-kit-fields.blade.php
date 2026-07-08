@php
    $product = $product ?? null;
    $formKey = $formKey ?? ($product ? 'product-' . $product->id : 'new-product');

    $ingredientRows = old('product_ingredients');
    if ($ingredientRows === null && $product) {
        $ingredientRows = $product->mealKitIngredients
            ->map(fn ($ingredient) => [
                'name' => $ingredient->name,
                'quantity' => $ingredient->quantity,
                'unit' => $ingredient->unit,
            ])
            ->toArray();
    }
    $ingredientRows = $ingredientRows ?: [['name' => '', 'quantity' => '', 'unit' => '']];

    $stepRows = old('cooking_steps');
    if ($stepRows === null && $product) {
        $stepRows = $product->cookingSteps
            ->map(fn ($step) => ['instruction' => $step->instruction])
            ->toArray();
    }
    $stepRows = $stepRows ?: [['instruction' => '']];
@endphp

<input type="hidden" name="ingredients" value="{{ $product?->ingredients }}">
<input type="hidden" name="cooking_instructions" value="{{ $product?->cooking_instructions }}">
<input type="hidden" name="product_ingredients_json" value="">
<input type="hidden" name="cooking_steps_json" value="">

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" for="serving-size-{{ $formKey }}">Khẩu phần</label>
    <div class="col-md-6 col-sm-6">
        <input type="number" min="1" id="serving-size-{{ $formKey }}" name="serving_size" class="form-control"
            value="{{ old('serving_size', $product?->serving_size) }}" placeholder="Ví dụ: 2">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align">Thời gian</label>
    <div class="col-md-3 col-sm-3">
        <input type="number" min="0" name="prep_time" class="form-control"
            value="{{ old('prep_time', $product?->prep_time) }}" placeholder="Sơ chế (phút)">
    </div>
    <div class="col-md-3 col-sm-3">
        <input type="number" min="0" name="cook_time" class="form-control"
            value="{{ old('cook_time', $product?->cook_time) }}" placeholder="Nấu (phút)">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align">Dinh dưỡng & hạn dùng</label>
    <div class="col-md-3 col-sm-3">
        <input type="number" min="0" name="calories" class="form-control"
            value="{{ old('calories', $product?->calories) }}" placeholder="Kcal / khẩu phần">
    </div>
    <div class="col-md-3 col-sm-3">
        <input type="number" min="0" name="expiry_days" class="form-control"
            value="{{ old('expiry_days', $product?->expiry_days) }}" placeholder="Hạn dùng (ngày)">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" for="storage-instruction-{{ $formKey }}">Bảo quản</label>
    <div class="col-md-6 col-sm-6">
        <textarea id="storage-instruction-{{ $formKey }}" name="storage_instruction" rows="3" class="form-control"
            placeholder="Ví dụ: Bảo quản lạnh 2-6 độ C, dùng trong ngày sau khi mở gói.">{{ old('storage_instruction', $product?->storage_instruction) }}</textarea>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align">Nguyên liệu định lượng</label>
    <div class="col-md-8 col-sm-8">
        <div class="meal-kit-repeatable" id="meal-kit-ingredients-{{ $formKey }}" data-field="product_ingredients">
            @foreach ($ingredientRows as $index => $ingredient)
                <div class="meal-kit-repeatable-row meal-kit-ingredient-row">
                    <input type="text" class="form-control" data-name="name"
                        name="product_ingredients[{{ $index }}][name]" value="{{ $ingredient['name'] ?? '' }}"
                        placeholder="Tên nguyên liệu">
                    <input type="number" step="0.01" min="0" class="form-control" data-name="quantity"
                        name="product_ingredients[{{ $index }}][quantity]" value="{{ $ingredient['quantity'] ?? '' }}"
                        placeholder="Định lượng">
                    <input type="text" class="form-control" data-name="unit"
                        name="product_ingredients[{{ $index }}][unit]" value="{{ $ingredient['unit'] ?? '' }}"
                        placeholder="Đơn vị">
                    <button type="button" class="btn btn-danger meal-kit-remove-row">Xóa</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-default meal-kit-add-row" data-target="#meal-kit-ingredients-{{ $formKey }}">
            Thêm nguyên liệu
        </button>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align">Các bước nấu</label>
    <div class="col-md-8 col-sm-8">
        <div class="meal-kit-repeatable" id="meal-kit-steps-{{ $formKey }}" data-field="cooking_steps">
            @foreach ($stepRows as $index => $step)
                <div class="meal-kit-repeatable-row meal-kit-step-row">
                    <span class="meal-kit-step-number">{{ $index + 1 }}</span>
                    <textarea class="form-control" rows="2" data-name="instruction"
                        name="cooking_steps[{{ $index }}][instruction]" placeholder="Nhập hướng dẫn bước {{ $index + 1 }}">{{ $step['instruction'] ?? '' }}</textarea>
                    <button type="button" class="btn btn-danger meal-kit-remove-row">Xóa</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-default meal-kit-add-row" data-target="#meal-kit-steps-{{ $formKey }}">
            Thêm bước nấu
        </button>
    </div>
</div>
