@extends('layouts.client')

@section('title', 'Đặt hàng')

@section('breadcrumb', 'Đặt hàng')

@section('content')
    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Chi tiết thanh toán</h4>
                            <div class="select-address">
                                <div>
                                    <h6>Chọn địa chỉ khác:</h6>
                                </div>
                                <div>
                                    <select name="address_id" id="list_address" class="input-item">
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}" {{ $address->default ? 'selected' : '' }}>
                                                {{ $address->full_name }} - {{ $address->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <a href="{{ route('account') }}"
                                        class="btn theme-btn-1 btn-effect-1 text-uppercase">Thêm địa chỉ mới</a>
                                </div>
                            </div>
                            <div class="ltn__checkout-single-content-info">
                                <h6>Thông tin cá nhân</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" name="ltn__name" placeholder="Họ và tên"
                                                value="{{ $defaultAddress->full_name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-phone ltn__custom-icon">
                                            <input type="text" name="ltn__phone" placeholder="Số điện thoại"
                                                value="{{ $defaultAddress->phone }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Địa chỉ</h6>
                                        <div class="input-item">
                                            <input type="text" name="ltn__address" placeholder="Số nhà và tên đường"
                                                value="{{ $defaultAddress->address }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Thành phố</h6>
                                        <div class="input-item">
                                            <input type="text" name="ltn__city" placeholder="Thành phố"
                                                value="{{ $defaultAddress->city }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ltn__checkout-payment-method mt-50">
                        <h4 class="title-2">Phương thức thanh toán</h4>
                        <form action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            <input type="hidden" name="address_id" value="{{ $defaultAddress->id }}">

                            <div id="checkout_payment">
                                <div class="card">
                                    <h5 class="ltn__card-title">
                                        <input type="radio" name="payment_method" value="cash" id="payment_cod" checked>
                                        <label for="payment_cod">
                                            Thanh toán khi nhận hàng
                                            <img src="{{ asset('assets/clients/img/icons/cash.png') }}">
                                        </label>
                                    </h5>
                                </div>

                                <div class="card">
                                    <h5 class="collapsed ltn__card-title">
                                        <input type="radio" name="payment_method" value="paypal" id="payment_paypal">
                                        <label for="payment_paypal">
                                            PayPal <img src="{{ asset('assets/clients/img/icons/payment-3.png') }}">
                                        </label>
                                    </h5>
                                </div>

                                <div class="card">
                                    <h5 class="collapsed ltn__card-title">
                                        <input type="radio" name="payment_method" value="vietqr" id="payment_vietqr">
                                        <label for="payment_vietqr">
                                            VietQR - chuyển khoản ngân hàng
                                        </label>
                                    </h5>
                                    <div id="vietqr-checkout-note" class="vietqr-checkout-note" style="display: none;">
                                        Sau khi đặt hàng, hệ thống sẽ tạo mã VietQR theo đúng tổng tiền và mã đơn để bạn quét thanh toán.
                                    </div>
                                </div>
                            </div>
                            <div class="ltn__payment-note mt-30 mb-30">
                                <p>Dữ liệu cá nhân của bạn sẽ được sử dụng để xử lý đơn hàng của bạn, hỗ trợ trải nghiệm của
                                    bạn trên toàn bộ trang web này và cho các mục đích khác được mô tả trong chính sách bảo mật
                                    của chúng tôi.</p>
                            </div>
                            <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit"
                                id="order_button_cash">Đặt hàng</button>
                            <div id="paypal-button-container"></div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping-cart-total mt-50" id="checkout-summary"
                        data-subtotal="{{ $subtotal }}"
                        data-shipping-fee="{{ $shippingFee }}"
                        data-shipping-distance-km="{{ $shippingDistanceKm }}"
                        data-shipping-duration-seconds="{{ $shippingDurationSeconds }}"
                        data-discount="{{ $discountAmount }}"
                        data-total="{{ $totalPrice }}"
                        data-apply-url="{{ route('checkout.applyCoupon') }}"
                        data-remove-url="{{ route('checkout.removeCoupon') }}">
                        <h4 class="title-2">Tổng sản phẩm</h4>
                        <div class="cart-coupon" style="display: flex; margin: 15px 0;justify-content: space-between;">
                            <div class="input-item">
                                <input type="text" id="coupon_code" placeholder="Nhập mã giảm giá"
                                    value="{{ $appliedCoupon['code'] ?? '' }}">
                            </div>
                            <button type="button" class="btn theme-btn-2 btn-effect-2" id="applyCouponButton">Áp dụng</button>
                            <button type="button" class="btn btn-link" id="removeCouponButton"
                                style="{{ $appliedCoupon ? '' : 'display:none;' }}">Gỡ mã</button>
                        </div>
                        <table class="table">
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }} <strong>× {{ $item->quantity }}</strong></td>
                                        <td>{{ number_format($item->product->current_price * $item->quantity, 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>Tạm tính</td>
                                    <td><span class="checkout-subtotal">{{ number_format($subtotal, 0, ',', '.') }}</span> ₫</td>
                                </tr>
                                <tr id="coupon-row" style="{{ $appliedCoupon ? '' : 'display:none;' }}">
                                    <td>Mã giảm giá (<span id="coupon-code-label">{{ $appliedCoupon['code'] ?? '' }}</span>)</td>
                                    <td>-<span id="coupon-discount">{{ $appliedCoupon ? number_format($appliedCoupon['discount_amount'], 0, ',', '.') : '' }}</span> ₫</td>
                                </tr>
                                <tr>
                                    <td>Vận chuyển và xử lý</td>
                                    <td><span id="checkout-shipping">{{ number_format($shippingFee, 0, ',', '.') }}</span> ₫</td>
                                </tr>
                                <tr>
                                    <td>Khoảng cách giao hàng</td>
                                    <td><span id="checkout-distance">{{ $shippingDistanceKm ? number_format($shippingDistanceKm, 2, ',', '.') . ' km' : 'Chưa xác định' }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Tổng cộng</strong></td>
                                    <td><strong class="totalPrice_Checkout">{{ number_format($totalPrice, 0, ',', '.') }} ₫</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
