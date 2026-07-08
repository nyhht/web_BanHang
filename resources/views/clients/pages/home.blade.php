@extends('layouts.client_home')

@section('title', 'Trang chủ')

@section('content')

    @php
        // Build a single list of products used on the page to render each modal only once
        $modalProducts = collect($categories)
            ->flatMap->products
            ->merge(collect($promotedProducts ?? []))
            ->merge(collect($bestSellingProducts))
            ->unique('id');
    @endphp

    <!-- SLIDER AREA START (slider-3) -->
    <div class="ltn__slider-area ltn__slider-3  section-bg-1">
        <div class="ltn__slide-one-active slick-slide-arrow-1 slick-slide-dots-1">
            <!-- ltn__slide-item -->
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3 ltn__slide-item-3-normal bg-image"
                data-bg="{{ asset('assets/clients/img/slider/13.jpg') }}">
                <div class="ltn__slide-item-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        <div class="slide-video mb-50 d-none">
                                            <a class="ltn__video-icon-2 ltn__video-icon-2-border"
                                                href="#"
                                                data-rel="lightcase:myCollection">
                                                <i class="fa fa-play"></i>
                                            </a>
                                        </div>
                                        <h6 class="slide-sub-title animated"><img
                                                src="{{ asset('assets/clients/img/icons/icon-img/1.png') }}" alt="#">
                                            Giải pháp vào bếp tiện lợi mỗi ngày</h6>
                                        <h1 class="slide-title animated ">Trải nghiệm nấu nướng <br> thông minh tại nhà
                                        </h1>
                                        <div class="slide-brief animated">
                                            <p>Chúng tôi mang đến những hộp nguyên liệu sơ chế sẵn, định lượng hoàn hảo
                                                kèm công thức độc quyền, giúp bữa cơm gia đình hoàn thành trong 15 phút.</p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{ route('products.index') }}" class="theme-btn-1 btn btn-effect-1 text-uppercase">Khám phá
                                                ngay</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ltn__slide-item -->
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3 ltn__slide-item-3-normal bg-image"
                data-bg="{{ asset('assets/clients/img/slider/14.jpg') }}">
                <div class="ltn__slide-item-inner  text-right text-end">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        <h6 class="slide-sub-title ltn__secondary-color animated">// RAU CỦ TƯƠI SẠCH & AN TOÀN</h6>
                                        <h1 class="slide-title animated ">Ăn Ngon & Bổ Dưỡng <br> Với Thực Đơn Meal-Kit</h1>
                                        <div class="slide-brief animated">
                                            <p>Cung cấp các set nguyên liệu tươi ngon được tuyển chọn kỹ lưỡng, tính sẵn hàm lượng dinh dưỡng,
                                                phù hợp cho cả chế độ Eat Clean và giữ dáng.</p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{ route('products.index') }}" class="theme-btn-1 btn btn-effect-1 text-uppercase">Khám phá
                                                ngay</a>
                                            <a href="{{ route('about') }}" class="btn btn-transparent btn-effect-3">Tìm hiểu
                                                thêm</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  -->
        </div>
    </div>
    <!-- SLIDER AREA END -->

    <!-- BANNER AREA START -->
    <div class="ltn__banner-area freshbox-banner-showcase mt-80 mb-70">
        <div class="container freshbox-banner-container">
            <div class="freshbox-banner-layout">
                @foreach ([17, 18, 19, 20, 21, 22, 23, 24, 25] as $bannerNumber)
                    <a href="{{ route('products.index') }}" class="freshbox-banner-card">
                        <img src="{{ asset('assets/clients/img/banner/' . $bannerNumber . '.png') }}" alt="FreshBox meal kit banner {{ $bannerNumber }}">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <!-- BANNER AREA END -->

    @if (($promotedProducts ?? collect())->isNotEmpty())
        <div class="ltn__product-area ltn__product-gutter pt-80 pb-40 freshbox-promotion-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title">Món ăn đang giảm giá</h1>
                        </div>
                    </div>
                </div>
                <div class="row ltn__tab-product-slider-one-active--- slick-arrow-1">
                    @foreach ($promotedProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                            <div class="ltn__product-item ltn__product-item-3 text-center freshbox-promoted-product">
                                <div class="product-img">
                                    <a href="{{ route('product.detail', $product->slug) }}"><img src="{{ $product->image_url }}" alt="{{ $product->name }}"></a>
                                    <div class="product-badge">
                                        <ul><li class="sale-badge">-{{ $product->sale_percent }}%</li></ul>
                                    </div>
                                    <div class="product-hover-action">
                                        <ul>
                                            <li>
                                                <a href="#" title="Xem nhanh" data-bs-toggle="modal" data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" title="Thêm vào giỏ hàng" class="add-to-cart-btn" data-id="{{ $product->id }}">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Yêu thích" class="add-to-wishlist" data-id="{{ $product->id }}">
                                                    <i class="far fa-heart"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <div class="product-ratting">
                                        @include('clients.components.includes.rating', ['product' => $product])
                                    </div>
                                    <h2 class="product-title">
                                        <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                    </h2>
                                    @include('clients.components.includes.meal-kit-meta', ['product' => $product])
                                    <div class="product-price">
                                        @include('clients.components.includes.product-price', ['product' => $product])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    <!-- CATEGORY AREA START -->
    <div class="ltn__category-area section-bg-1-- ltn__primary-bg before-bg-1 bg-image bg-overlay-theme-black-5--0 pt-115 pb-90"
        data-bg="{{ asset('assets/clients/img/bg/5.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color">Danh mục</h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__category-slider-active slick-arrow-1">
                @foreach ($categories as $category)
                    <div class="col-12">
                        <div class="ltn__category-item ltn__category-item-3 text-center">
                            <div class="ltn__category-item-img">
                                <a href="{{ route('products.index') }}">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width:55px; height: 55px">
                                </a>
                            </div>
                            <div class="ltn__category-item-name">
                                <h5><a href="{{ route('products.index') }}">{{ $category->name }}</a></h5>
                                <h6>({{ $category->products->count() }} sản phẩm)</h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- CATEGORY AREA END -->

    <!-- PRODUCT TAB AREA START (product-item-3) -->
    <div class="ltn__product-tab-area ltn__product-gutter pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Sản phẩm</h1>
                    </div>
                    <div class="ltn__tab-menu ltn__tab-menu-2 ltn__tab-menu-top-right-- text-uppercase text-center">
                        <div class="nav">
                            @foreach ($categories as $index => $category)
                                <a class="{{ $index == 0 ? 'active show' : '' }}" data-bs-toggle="tab"
                                    href="#tab_{{ $category->id }}">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-content">
                        @foreach ($categories as $index => $category)
                            <div class="tab-pane fade {{ $index == 0 ? 'active show' : '' }}"
                                id="tab_{{ $category->id }}">
                                <div class="ltn__product-tab-content-inner">
                                    <div class="row ltn__tab-product-slider-one-active slick-arrow-1">
                                        @foreach ($category->products as $product)
                                            <div class="col-lg-12">
                                                <div class="ltn__product-item ltn__product-item-3 text-center">
                                                    <div class="product-img">
                                                        <a href="#"><img src="{{ $product->image_url }}"
                                                                alt="{{ $product->name }}"></a>
                                                        <div class="product-hover-action">
                                                            <ul>
                                                                <li>
                                                                    <a href="#" title="Xem nhanh"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                                        <i class="far fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" title="Thêm vào giỏ hàng"
                                                                        class="add-to-cart-btn"
                                                                        data-id="{{ $product->id }}">
                                                                        <i class="fas fa-shopping-cart"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" title="Yêu thích"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#liton_wishlist_modal-{{ $product->id }}">
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
                                                        <h2 class="product-title"><a
                                                                href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                                        </h2>
                                                        @include('clients.components.includes.meal-kit-meta', ['product' => $product])
                                                        <div class="product-price">
                                                            @include('clients.components.includes.product-price', ['product' => $product])
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT TAB AREA END -->

    <!-- COUNTER UP AREA START -->
    <div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70"
        data-bg="{{ asset('assets/clients/img/bg/5.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/25.png') }}"
                                alt="#"> </div>
                        <h1><span class="counter">3000</span><span class="counterUp-icon">+</span> </h1>
                        <h6>Bữa ăn được phục vụ</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/26.png') }}"
                                alt="#"> </div>
                        <h1><span class="counter">100</span><span class="counterUp-letter">+</span> </h1>
                        <h6>Công thức độc quyền</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/27.png') }}"
                                alt="#"> </div>
                        <h1><span class="counter">100</span><span class="counterUp-icon">%</span> </h1>
                        <h6>Tươi sạch, an toàn</h6>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/28.png') }}"
                                alt="#"> </div>
                        <h1><span class="counter">20</span><span class="counterUp-icon">+</span> </h1>
                        <h6>Tiết kiệm thời gian</h6>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- COUNTER UP AREA END -->

    <!-- PRODUCT AREA START (product-item-3) -->
    <div class="ltn__product-area ltn__product-gutter pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Sản phẩm bán chạy</h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__tab-product-slider-one-active--- slick-arrow-1">
                @foreach ($bestSellingProducts as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                        <div class="ltn__product-item ltn__product-item-3 text-left">
                            <div class="product-img">
                                <a href="#"><img src="{{ $product->image_url }}" alt="{{ $product->name }}"></a>
                                <div class="product-hover-action">
                                    <ul>
                                        <li>
                                            <a href="#" title="Xem nhanh" data-bs-toggle="modal"
                                                data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                                data-id="{{ $product->id }}">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" title="Yêu thích" class="add-to-wishlist"
                                                data-id="{{ $product->id }}">
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
                                <h2 class="product-title"><a
                                        href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                </h2>
                                @include('clients.components.includes.meal-kit-meta', ['product' => $product])
                                <div class="product-price">
                                    @include('clients.components.includes.product-price', ['product' => $product])
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- PRODUCT AREA END -->

    @foreach ($modalProducts as $product)
        @include('clients.components.includes.include-modals')
    @endforeach

    <!-- CALL TO ACTION START (call-to-action-4) -->
    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image pt-115 pb-120"
        data-bg="{{ asset('assets/clients/img/bg/6.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="call-to-action-inner call-to-action-inner-4 text-center">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">// bất kỳ câu hỏi nào bạn có //</h6>
                            <h1 class="section-title white-color">0123-456-789</h1>
                        </div>
                        <div class="btn-wrapper">
                            <a href="tel:+123456789" class="theme-btn-1 btn btn-effect-1">GỌI ĐIỆN</a>
                            <a href="contact.html" class="btn btn-transparent btn-effect-4 white-color">LIÊN HỆ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ltn__call-to-4-img-1">
            <img src="{{ asset('assets/clients/img/bg/12.png') }}" alt="#">
        </div>
        <div class="ltn__call-to-4-img-2">
            <img src="{{ asset('assets/clients/img/bg/18.png') }}" alt="#">
        </div>
    </div>
    <!-- CALL TO ACTION END -->

@endsection
