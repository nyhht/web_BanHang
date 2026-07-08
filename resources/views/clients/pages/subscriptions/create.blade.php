@extends('layouts.client')

@section('title', 'Tạo gói định kỳ')

@section('breadcrumb', 'Tạo gói định kỳ')

@section('content')
    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Tạo gói đặt lịch định kỳ</h3>
                    <p class="mb-30">Hệ thống sẽ tự tạo đơn theo lịch. Mỗi đơn thanh toán riêng bằng COD hoặc VietQR.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if ($addresses->isEmpty())
                        <div class="alert alert-warning">
                            Bạn cần thêm địa chỉ giao hàng có tọa độ trước khi tạo gói định kỳ.
                            <a href="{{ route('account') }}">Thêm địa chỉ</a>
                        </div>
                    @elseif ($products->isEmpty())
                        <div class="alert alert-warning">Hiện chưa có sản phẩm còn hàng để tạo gói.</div>
                    @else
                        <form action="{{ route('subscriptions.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-lg-6">
                                    <h5>Thông tin lịch giao</h5>
                                    <div class="mb-3">
                                        <label for="shipping_address_id">Địa chỉ giao hàng</label>
                                        <select name="shipping_address_id" id="shipping_address_id" class="form-control" required>
                                            @foreach ($addresses as $address)
                                                <option value="{{ $address->id }}" @selected(old('shipping_address_id', $addresses->firstWhere('default', true)?->id) == $address->id)>
                                                    {{ $address->full_name }} - {{ $address->address }}, {{ $address->city }}
                                                    {{ $address->default ? '(Mặc định)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="frequency">Chu kỳ</label>
                                            <select name="frequency" id="frequency" class="form-control" required>
                                                <option value="daily" @selected(old('frequency') === 'daily')>Hằng ngày</option>
                                                <option value="weekly" @selected(old('frequency') === 'weekly')>Hằng tuần</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="week_day">Ngày trong tuần</label>
                                            <select name="week_day" id="week_day" class="form-control">
                                                @foreach ($weekDayLabels as $value => $label)
                                                    <option value="{{ $value }}" @selected((int) old('week_day', 1) === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <small>Chỉ áp dụng khi chọn hằng tuần.</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="start_date">Ngày bắt đầu</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                value="{{ old('start_date', now()->toDateString()) }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="end_date">Ngày kết thúc</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                value="{{ old('end_date') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="preferred_delivery_time">Giờ giao</label>
                                            <input type="time" class="form-control" id="preferred_delivery_time"
                                                name="preferred_delivery_time" value="{{ old('preferred_delivery_time', '08:00') }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label>Thanh toán từng đơn</label>
                                        <div>
                                            <label class="me-3">
                                                <input type="radio" name="payment_method" value="cash" @checked(old('payment_method', 'cash') === 'cash')>
                                                COD
                                            </label>
                                            <label>
                                                <input type="radio" name="payment_method" value="vietqr" @checked(old('payment_method') === 'vietqr')>
                                                VietQR
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="note">Ghi chú</label>
                                        <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <h5>Sản phẩm trong gói</h5>
                                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Sản phẩm</th>
                                                    <th>Giá chốt</th>
                                                    <th>Số lượng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($products as $product)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                                    style="width: 54px; height: 54px; object-fit: cover; margin-right: 10px;">
                                                                <div>
                                                                    <strong>{{ $product->name }}</strong><br>
                                                                    <small>Còn {{ $product->stock }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ number_format($product->current_price, 0, ',', '.') }} đ</td>
                                                        <td style="width: 110px;">
                                                            <input type="number" name="quantities[{{ $product->id }}]"
                                                                class="form-control" min="0" max="{{ min($product->stock, 99) }}"
                                                                value="{{ old('quantities.' . $product->id, 0) }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-wrapper text-end mt-30">
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="theme-btn-1 btn btn-effect-1">Tạo gói định kỳ</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
