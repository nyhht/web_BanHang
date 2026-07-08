@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Hóa đơn</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Hóa đơn</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <section class="content invoice">
                                <!-- title row -->
                                <div class="row">
                                    <div class="  invoice-header">
                                        <h1>
                                            <i class="fa fa-globe"></i> Hóa đơn.
                                            <small class="pull-right">Ngày tạo : {{ $order->created_at }}</small>
                                        </h1>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        Từ
                                        <address>
                                            <strong>{{ $order->shippingAddress->full_name }}</strong>
                                            <br>{{ $order->shippingAddress->address }}
                                            <br>{{ $order->shippingAddress->city }}
                                            <br>Sô điện thoại: {{ $order->shippingAddress->phone }}
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        Đến
                                        <address>
                                            <strong>Mealkit</strong>
                                            <br>Bình Minh
                                            <br>Hà Nội, Việt Nam
                                            <br>Số điện thoại: 1 (804) 123-9876
                                            <br>Email: nguyenhieu27hsht@gmail.com
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        <b>Order ID: {{ $order->id }}</b>
                                        <br>
                                        @if ($order->order_type === \App\Models\Order::TYPE_SUBSCRIPTION)
                                            <b>Loại đơn:</b> Đơn định kỳ
                                            @if ($order->scheduled_delivery_date)
                                                <br><b>Ngày giao dự kiến:</b> {{ $order->scheduled_delivery_date->format('d/m/Y') }}
                                            @endif
                                            <br>
                                        @endif
                                        <b>Email: {{ $order->user->email }}</b>
                                        <br>
                                        <b>Tài khoản:</b> {{ $order->user->name }}
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <div class="  table">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Ảnh</th>
                                                    <th>Sản phẩm</th>
                                                    <th>Giá</th>
                                                    <th>Số lượng</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $item)
                                                    <tr>
                                                        <td>
                                                            <img src="{{ $item->product->image_url }}" width="50px"
                                                                alt="{{ $item->product->name }}">
                                                        </td>
                                                        <td>{{ $item->product->name }}</td>
                                                        <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                            VNĐ</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">
                                    <!-- accepted payments column -->
                                    <div class="col-md-6">
                                        <p class="lead">Phương thức thanh toán:</p>

                                        @if (optional($order->payment)->payment_method == 'paypal')
                                            <img src="{{ asset('assets/admin/images/paypal.png') }}" alt="Paypal">
                                        @elseif (optional($order->payment)->payment_method == 'vietqr')
                                            <p class="well well-sm no-shadow">
                                                <strong>Chuyển khoản VietQR</strong><br>
                                                Nội dung: {{ optional($order->payment)->transaction_id ?: 'MEALKIT DH' . $order->id }}
                                            </p>
                                        @else
                                            <img src="{{ asset('assets/admin/images/cash.jpg') }}" width="80px"
                                                height="50px" alt="Thanh toán khi nhận hàng">
                                        @endif

                                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                            Đây là phương thức thanh toán mà khách hàng đã chọn cho đơn hàng này. Với
                                            VietQR, vui lòng kiểm tra giao dịch ngân hàng theo đúng nội dung chuyển khoản
                                            trước khi xác nhận thanh toán.
                                        </p>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Tiền hàng:</th>
                                                        <td>{{ number_format($order->subtotal ?: max($order->total_price - ($order->shipping_fee ?: 25000) + $order->discount_amount, 0), 0, ',', '.') }} VNĐ</td>
                                                    </tr>
                                                    @if ($order->discount_amount > 0)
                                                        <tr>
                                                            <th>Giảm giá{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}:</th>
                                                            <td>-{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Phí giao hàng:</th>
                                                        <td>{{ number_format($order->shipping_fee ?: 25000, 0, ',', '.') }} VNĐ</td>
                                                    </tr>
                                                    @if ($order->shipping_distance_km)
                                                        <tr>
                                                            <th>Khoảng cách giao hàng:</th>
                                                            <td>{{ number_format($order->shipping_distance_km, 2, ',', '.') }} km</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Tổng tiền:</th>
                                                        <td>{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div>
                                        @if ($order->status != 'canceled')
                                            <button class="btn btn-default" onclick="window.print();"><i
                                                    class="fa fa-print"></i> In hóa đơn</button>
                                            <button class="btn btn-success pull-right send-invoice-mail"
                                                data-id="{{ $order->id }}"
                                                data-url="{{ route('admin.orders.send-invoice') }}">
                                                <i class="fa fa-send"></i> Gửi
                                                hóa
                                                đơn</button>

                                            @if ($order->status == 'pending')
                                                <button class="btn btn-danger pull-right cancel-order" style="margin-right: 5px;"
                                                    data-id="{{ $order->id }}"
                                                    data-url="{{ route('admin.orders.cancel') }}">
                                                    <i class="fa fa-remove"></i> Hủy đơn hàng
                                                </button>
                                            @endif
                                            @if (
                                                optional($order->payment)->status !== 'completed' &&
                                                    !in_array($order->status, ['canceled', 'completed'], true) &&
                                                    ($order->status == 'delivered' || optional($order->payment)->payment_method == 'vietqr'))
                                                <button class="btn btn-primary pull-right confirm-payment" style="margin-right: 5px;"
                                                    data-id="{{ $order->id }}"
                                                    data-url="{{ route('admin.orders.confirm-payment') }}">
                                                    <i class="fa fa-check"></i> Xác nhận thanh toán
                                                </button>
                                            @endif
                                        @else
                                            <button class="btn btn-danger" style="cursor: not-allowed"><i
                                                    class="fa fa-info"></i> Đơn hàng đã hủy</button>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection
