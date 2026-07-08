@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý sản phẩm <small>Danh sách tất cả sản phẩm</small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách sản phẩm</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <p class="text-muted font-13 m-b-30">
                                            Trang quản lý sản phẩm cho phép admin tạo, chỉnh sửa và xóa các sản phẩm sản
                                            phẩm.
                                            Các sản phẩm giúp tổ chức sản phẩm theo từng nhóm, giúp khách hàng dễ dàng tìm
                                            kiếm và lựa chọn hơn.
                                            Dữ liệu được hiển thị dưới dạng bảng, hỗ trợ tìm kiếm, sắp xếp và thao tác nhanh
                                            chóng.
                                        </p>
                                        <div class="row mb-3">
                                            <div class="col-md-4 col-sm-6">
                                                <label for="product-category-filter">Tìm theo danh mục</label>
                                                <select id="product-category-filter" class="form-control">
                                                    <option value="">Tất cả danh mục</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Hình ảnh</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Danh mục</th>
                                                    <th>Slug</th>
                                                    <th>Mô tả</th>
                                                    <th>Số lượng</th>
                                                    <th>Giá</th>
                                                    <th>Đơn vị</th>
                                                    <th>Trạng thái</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                @foreach ($products as $product)
                                                    <tr id="product-row-{{ $product->id }}">
                                                        <td>
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                                class="image-product">
                                                        </td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->category->name }}</td>
                                                        <td>{{ $product->slug }}</td>
                                                        <td>{{ $product->description }}</td>
                                                        <td>{{ $product->stock }}</td>
                                                        <td>{{ number_format($product->price, 0, ',', '.') }}VNĐ</td>
                                                        <td>{{ $product->unit }}</td>
                                                        <td>{{ $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-app btn-update-product" data-toggle="modal"
                                                                data-target="#modalUpdate-{{ $product->id }}">
                                                                <i class="fa fa-edit"></i>Chỉnh sửa
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-app btn-delete-product"
                                                                data-id="{{ $product->id }}">
                                                                <i class="fa fa-trash"></i>Xóa
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    {{-- Modal chỉnh sửa được render lại bên ngoài table để form không bị browser/DataTables làm lệch DOM.
                                                    <div class="modal fade" id="modalUpdate-{{ $product->id }}"
                                                        tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="productModalLabel">Chỉnh
                                                                        sửa</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="update-product" method="POST"
                                                                        class="form-horizontal form-label-left"
                                                                        enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-name">Tên Sản Phẩm
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="product-name"
                                                                                    name="name" required
                                                                                    class="form-control "
                                                                                    value="{{ $product->name }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-name">Danh mục
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <select name="category_id" id="category_id"
                                                                                    class="form-control" required>
                                                                                    <option value="">Chọn danh mục
                                                                                    </option>
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
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-description">Mô tả
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text"
                                                                                    id="product-description"
                                                                                    name="description" required
                                                                                    class="form-control"
                                                                                    value="{{ $product->description }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-ingredients-{{ $product->id }}">Thành phần
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <textarea id="product-ingredients-{{ $product->id }}"
                                                                                    name="legacy_ingredients" rows="4"
                                                                                    class="form-control">{{ $product->ingredients }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-cooking-instructions-{{ $product->id }}">Cách chế biến
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <textarea id="product-cooking-instructions-{{ $product->id }}"
                                                                                    name="legacy_cooking_instructions" rows="5"
                                                                                    class="form-control">{{ $product->cooking_instructions }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        @include('admin.pages.partials.product-meal-kit-fields', ['product' => $product, 'formKey' => 'edit-' . $product->id])
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-description">Giá
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="number" id="product-price"
                                                                                    name="price" required
                                                                                    class="form-control"
                                                                                    value="{{ $product->price }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-description">Số lượng
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="number" id="product-stock"
                                                                                    name="stock" required
                                                                                    class="form-control"
                                                                                    value="{{ $product->stock }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-description">Đơn vị
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="product-unit"
                                                                                    name="unit" required
                                                                                    class="form-control"
                                                                                    value="{{ $product->unit }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-images">Hình
                                                                                ảnh</label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <label class="custom-file-upload"
                                                                                    for="product-images-{{ $product->id }}">
                                                                                    Chọn ảnh</label>
                                                                                <input type="file" name="images[]"
                                                                                    class="product-images"
                                                                                    id="product-images-{{ $product->id }}"
                                                                                    data-id="{{ $product->id }}"
                                                                                    accept="image/*" multiple>
                                                                                <div id="image-preview-container-{{ $product->id }}"
                                                                                    class="image-preview-container image-preview-listproduct"
                                                                                    data-id="{{ $product->id }}">
                                                                                    @foreach ($product->images as $image)
                                                                                        <img src="{{ asset('storage/' . $image->image) }}"
                                                                                            alt="Ảnh sản phẩm"
                                                                                            style="width: 100px; height: 100px; object-fit: cover; margin: 5px;">
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Quay lại</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary btn-update-submit-product"
                                                                        data-id="{{ $product->id }}">Chỉnh sửa</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    --}}
                                                @endforeach

                                            </tbody>
                                        </table>
                                        @foreach ($products as $product)
                                            @include('admin.pages.partials.product-edit-modal', ['product' => $product, 'categories' => $categories])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection
