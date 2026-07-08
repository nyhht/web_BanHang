<div class="modal fade" id="modalUpdate-{{ $product->id }}" tabindex="-1" role="dialog"
    aria-labelledby="productModalLabel-{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel-{{ $product->id }}">Chỉnh sửa</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-product-{{ $product->id }}" method="POST" class="form-horizontal form-label-left"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-name-{{ $product->id }}">
                            Tên Sản Phẩm <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="product-name-{{ $product->id }}" name="name" required
                                class="form-control" value="{{ $product->name }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="category-id-{{ $product->id }}">
                            Danh mục <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select name="category_id" id="category-id-{{ $product->id }}" class="form-control" required>
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align"
                            for="product-description-{{ $product->id }}">
                            Mô tả <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="product-description-{{ $product->id }}" name="description" required
                                class="form-control" value="{{ $product->description }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align"
                            for="product-ingredients-{{ $product->id }}">Thành phần</label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="product-ingredients-{{ $product->id }}" name="legacy_ingredients" rows="4"
                                class="form-control">{{ $product->ingredients }}</textarea>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align"
                            for="product-cooking-instructions-{{ $product->id }}">Cách chế biến</label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="product-cooking-instructions-{{ $product->id }}" name="legacy_cooking_instructions" rows="5"
                                class="form-control">{{ $product->cooking_instructions }}</textarea>
                        </div>
                    </div>

                    @include('admin.pages.partials.product-meal-kit-fields', ['product' => $product, 'formKey' => 'edit-' . $product->id])

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-price-{{ $product->id }}">
                            Giá <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="product-price-{{ $product->id }}" name="price" required
                                class="form-control" value="{{ $product->price }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-stock-{{ $product->id }}">
                            Số lượng <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="product-stock-{{ $product->id }}" name="stock" required
                                class="form-control" value="{{ $product->stock }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-unit-{{ $product->id }}">
                            Đơn vị <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="product-unit-{{ $product->id }}" name="unit" required
                                class="form-control" value="{{ $product->unit }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align"
                            for="product-images-{{ $product->id }}">Hình ảnh</label>
                        <div class="col-md-6 col-sm-6">
                            <label class="custom-file-upload" for="product-images-{{ $product->id }}">Chọn ảnh</label>
                            <input type="file" name="images[]" class="product-images"
                                id="product-images-{{ $product->id }}" data-id="{{ $product->id }}" accept="image/*"
                                multiple>
                            <div id="image-preview-container-{{ $product->id }}"
                                class="image-preview-container image-preview-listproduct" data-id="{{ $product->id }}">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset('storage/' . $image->image) }}" alt="Ảnh sản phẩm"
                                        style="width: 100px; height: 100px; object-fit: cover; margin: 5px;">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Quay lại</button>
                <button type="button" class="btn btn-primary btn-update-submit-product" data-id="{{ $product->id }}">
                    Chỉnh sửa
                </button>
            </div>
        </div>
    </div>
</div>
