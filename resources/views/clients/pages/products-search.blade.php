@extends('layouts.client')

@section('title', 'Tìm kiếm')

@section('breadcrumb', 'Tìm kiếm')


@section('content')
    <!-- PRODUCT DETAILS AREA START -->
    <div class="ltn__product-area ltn__product-gutter mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="liton_product_grid">
                            <div class="ltn__product-tab-content-inner ltn__product-grid-view">
                                <div class="row">
                                    @forelse ($products as $product)
                                        <div class="col-xl-3 col-lg-4 col-sm-6 col-6">
                                            <div class="ltn__product-item ltn__product-item-3 text-center">
                                                <div class="product-img">
                                                    <a href="{{ route('product.detail', $product->slug) }}">
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"></a>
                                                    <div class="product-hover-action">
                                                        <ul>
                                                            <li>
                                                                <a href="javascript:void(0)" title="Xem nhanh"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                                    <i class="far fa-eye"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)" title="Thêm vào giỏ hàng"
                                                                    class="add-to-cart-btn" data-id="{{ $product->id }}">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)" title="Yêu thích"
                                                                    class="add-to-wishlist" data-id="{{ $product->id }}">
                                                                    <i class="far fa-heart"></i></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="product-info">
                                                    <div class="product-ratting">
                                                        @include('clients.components.includes.rating', [
                                                            'product' => $product,
                                                        ])
                                                    </div>
                                                    <h2 class="product-title">
                                                        <a
                                                            href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                                    </h2>
                                                    @include('clients.components.includes.meal-kit-meta', ['product' => $product])
                                                    <div class="product-price">
                                                        @include('clients.components.includes.product-price', ['product' => $product])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="product-empty-state">
                                                <i class="fas fa-search"></i>
                                                <h4>Không tìm thấy sản phẩm</h4>
                                                <p>Không có sản phẩm phù hợp với từ khóa bạn đã nhập.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT DETAILS AREA END -->
@endsection
