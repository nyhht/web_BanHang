@extends('layouts.client')

@section('title', 'Yêu thích')

@section('breadcrumb', 'Yêu thích')

@section('content')
    <!-- SHOPING CART AREA START -->
    <div class="liton__shoping-cart-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping-cart-inner">
                        <div class="shoping-cart-table table-responsive">
                            <table class="table">
                                <tbody>
                                    @forelse ($wishlist as $item)
                                        <tr>
                                            <td class="wistlist-product-remove" data-id="{{ $item->product->id }}"> x </td>
                                            <td class="cart-product-image">
                                                <a href="{{ route('product.detail', $item->product->slug) }}">
                                                    <img src="{{ $item->product->image_url }}" alt="Sản phẩm"></a>
                                            </td>
                                            <td class="wishlist-product-info">
                                                <h4><a
                                                        href="{{ route('product.detail', $item->product->slug) }}">{{ $item->product->name }}</a>
                                                </h4>
                                            </td>
                                            <td class="wishlist-product-price">
                                                @include('clients.components.includes.product-price', ['product' => $item->product])
                                            </td>
                                            <td class="wishlist-product-stock">
                                                {{ $item->product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('product.detail', $item->product->slug) }}"
                                                    class="submit-button-1 " title="Thêm vào giỏ hàng">
                                                    <span>Thêm vào giỏ hàng</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Danh sách yêu thích của bạn đang trống!
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SHOPING CART AREA END -->
@endsection
