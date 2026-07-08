@php
    $couponId = $coupon?->id ?? 'new';
    $selectedProductIds = collect(old('product_ids', $coupon->product_ids ?? []))->map(fn ($id) => (int) $id)->all();
    $assignedUserIds = $coupon ? $coupon->assignedUsers->pluck('id')->map(fn ($id) => (int) $id)->all() : [];
    $selectedUserIds = collect(old('user_ids', $assignedUserIds))->map(fn ($id) => (int) $id)->all();
    $productGroups = collect($products)
        ->groupBy(fn ($product) => $product->category?->name ?: 'Chưa phân loại')
        ->sortKeys();
@endphp

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-code-{{ $couponId }}">Mã giảm giá <span class="required">*</span></label>
    <div class="col-md-4 col-sm-6">
        <input type="text" id="coupon-code-{{ $couponId }}" name="code" value="{{ old('code', $coupon->code ?? '') }}" required class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-description-{{ $couponId }}">Mô tả</label>
    <div class="col-md-6 col-sm-6">
        <input type="text" id="coupon-description-{{ $couponId }}" name="description" value="{{ old('description', $coupon->description ?? '') }}" class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-percentage-{{ $couponId }}">Giảm (%) <span class="required">*</span></label>
    <div class="col-md-2 col-sm-3">
        <input type="number" id="coupon-percentage-{{ $couponId }}" name="discount_percentage" min="1" max="100" value="{{ old('discount_percentage', $coupon->discount_percentage ?? 5) }}" required class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-starts-{{ $couponId }}">Bắt đầu</label>
    <div class="col-md-4 col-sm-6">
        <input type="datetime-local" id="coupon-starts-{{ $couponId }}" name="starts_at" value="{{ old('starts_at', optional($coupon?->starts_at)->format('Y-m-d\TH:i')) }}" class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-expires-{{ $couponId }}">Thời hạn</label>
    <div class="col-md-4 col-sm-6">
        <input type="datetime-local" id="coupon-expires-{{ $couponId }}" name="expires_at" value="{{ old('expires_at', optional($coupon?->expires_at)->format('Y-m-d\TH:i')) }}" class="form-control">
        <small class="form-text text-muted">Để trống nếu không giới hạn thời gian.</small>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-usage-limit-{{ $couponId }}">Giới hạn lượt dùng</label>
    <div class="col-md-2 col-sm-3">
        <input type="number" id="coupon-usage-limit-{{ $couponId }}" name="usage_limit" min="1" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align">Trạng thái</label>
    <div class="col-md-8 col-sm-9">
        <label class="checkbox-inline mr-3">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}> Kích hoạt
        </label>
        <label class="checkbox-inline mr-3">
            <input type="checkbox" name="notify_customers" value="1" {{ old('notify_customers', $coupon->notify_customers ?? true) ? 'checked' : '' }}> Gửi thông báo cho khách
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" name="restricted_to_assigned_users" value="1" {{ old('restricted_to_assigned_users', $coupon->restricted_to_assigned_users ?? false) ? 'checked' : '' }}> Chỉ khách được tặng mới dùng được
        </label>
    </div>
</div>

<hr>
<h4>Tặng mã cho khách hàng</h4>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align">Tự động tặng</label>
    <div class="col-md-8 col-sm-9">
        <label class="checkbox-inline mr-3">
            <input type="checkbox" name="auto_assign_on_register" value="1" {{ old('auto_assign_on_register', $coupon->auto_assign_on_register ?? false) ? 'checked' : '' }}> Khách tạo tài khoản lần đầu
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" name="auto_assign_weekend" value="1" {{ old('auto_assign_weekend', $coupon->auto_assign_weekend ?? false) ? 'checked' : '' }}> Cuối tuần
        </label>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="auto-dates-{{ $couponId }}">Ngày lễ / ngày đặc biệt</label>
    <div class="col-md-6 col-sm-6">
        <input type="text" id="auto-dates-{{ $couponId }}" name="auto_assign_dates_text" value="{{ old('auto_assign_dates_text', $coupon ? $dateListText($coupon) : '') }}" class="form-control" placeholder="2026-09-02, 2026-12-25">
        <small class="form-text text-muted">Nhập nhiều ngày, cách nhau bằng dấu phẩy.</small>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-users-{{ $couponId }}">Tặng ngay cho khách</label>
    <div class="col-md-6 col-sm-6">
        <select id="coupon-users-{{ $couponId }}" name="user_ids[]" class="form-control" multiple size="6">
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" {{ in_array($customer->id, $selectedUserIds, true) ? 'selected' : '' }}>
                    {{ $customer->name }} - {{ $customer->email }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Giữ Ctrl để chọn nhiều khách hàng.</small>
    </div>
</div>

<hr>
<h4>Giảm giá sản phẩm hằng ngày</h4>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align">Tự áp deal</label>
    <div class="col-md-8 col-sm-9">
        <label class="checkbox-inline">
            <input type="checkbox" name="auto_apply_to_products" value="1" {{ old('auto_apply_to_products', $coupon->auto_apply_to_products ?? false) ? 'checked' : '' }}> Hằng ngày tự giảm giá thật trên sản phẩm
        </label>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="coupon-products-{{ $couponId }}">Sản phẩm áp dụng</label>
    <div class="col-md-6 col-sm-6">
        <div class="coupon-product-picker js-coupon-product-picker" data-target="coupon-products-{{ $couponId }}">
            <div class="coupon-product-picker-tools">
                <select class="form-control js-coupon-product-category" aria-label="Lọc theo danh mục">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($productGroups as $categoryName => $groupedProducts)
                        <option value="{{ \Illuminate\Support\Str::slug($categoryName) }}">{{ $categoryName }} ({{ $groupedProducts->count() }})</option>
                    @endforeach
                </select>
                <input type="text" class="form-control js-coupon-product-search" placeholder="Tìm tên món...">
            </div>
            <div class="coupon-product-picker-actions">
                <button type="button" class="btn btn-default btn-sm js-coupon-products-select-visible">Chọn tất cả đang lọc</button>
                <button type="button" class="btn btn-default btn-sm js-coupon-products-clear-visible">Bỏ chọn đang lọc</button>
                <button type="button" class="btn btn-default btn-sm js-coupon-products-clear-all">Bỏ chọn tất cả</button>
                <span class="text-muted js-coupon-products-count"></span>
            </div>
        </div>
        <select id="coupon-products-{{ $couponId }}" name="product_ids[]" class="form-control js-coupon-products-select" multiple size="10">
            @foreach ($productGroups as $categoryName => $groupedProducts)
                @php
                    $categoryKey = \Illuminate\Support\Str::slug($categoryName);
                @endphp
                <optgroup label="{{ $categoryName }}" data-category="{{ $categoryKey }}">
                    @foreach ($groupedProducts as $product)
                        <option value="{{ $product->id }}"
                            data-category="{{ $categoryKey }}"
                            data-search="{{ \Illuminate\Support\Str::lower($product->name . ' ' . $categoryName) }}"
                            {{ in_array($product->id, $selectedProductIds, true) ? 'selected' : '' }}>
                    {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }} VNĐ
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <small class="form-text text-muted">Không chọn sản phẩm nào thì hệ thống chọn ngẫu nhiên trong kho.</small>
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="daily-limit-{{ $couponId }}">Số món/ngày</label>
    <div class="col-md-2 col-sm-3">
        <input type="number" id="daily-limit-{{ $couponId }}" name="daily_product_limit" min="1" max="100" value="{{ old('daily_product_limit', $coupon->daily_product_limit ?? 6) }}" class="form-control">
    </div>
</div>

<div class="item form-group">
    <label class="col-form-label col-md-2 col-sm-3 label-align" for="product-message-{{ $couponId }}">Nội dung thông báo món giảm</label>
    <div class="col-md-6 col-sm-6">
        <textarea id="product-message-{{ $couponId }}" name="product_promotion_message" rows="3" class="form-control">{{ old('product_promotion_message', $coupon->product_promotion_message ?? '') }}</textarea>
    </div>
</div>
