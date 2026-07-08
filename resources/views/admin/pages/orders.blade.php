@extends('layouts.admin')

@php
    use App\Models\Order;
@endphp

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý đơn hàng <small>Danh sách tất cả đơn hàng</small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách đơn hàng</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <p class="text-muted font-13 m-b-30">
                                            Trang quản lý đơn hàng giúp admin theo dõi, xác nhận và phân công giao hàng.
                                            Bảng dữ liệu hỗ trợ tìm kiếm, sắp xếp và thao tác nhanh chóng.
                                            Ô Search có thể gõ mã đơn, tên tài khoản, địa chỉ giao, trạng thái đơn,
                                            COD/VietQR/PayPal, trạng thái thanh toán hoặc tên nhân viên giao hàng.
                                        </p>
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%; text-align:center;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tài khoản</th>
                                                    <th>Địa chỉ giao</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái đơn</th>
                                                    <th>Nhân viên giao hàng</th>
                                                    <th>Thanh toán</th>
                                                    <th>Chi tiết</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orders as $order)
                                                    @php
                                                        $statusLabels = Order::statusLabels();
                                                        $badgeMap = [
                                                            Order::STATUS_PENDING => 'badge badge-warning',
                                                            Order::STATUS_PROCESSING => 'badge badge-primary',
                                                            Order::STATUS_PACKED => 'badge badge-secondary',
                                                            Order::STATUS_READY_FOR_DELIVERY => 'badge badge-info',
                                                            Order::STATUS_OUT_FOR_DELIVERY => 'badge badge-dark',
                                                            Order::STATUS_DELIVERED => 'badge badge-success',
                                                            Order::STATUS_COMPLETED => 'badge badge-success',
                                                            Order::STATUS_CANCELED => 'badge badge-danger',
                                                        ];
                                                        $badgeClass =
                                                            $badgeMap[$order->status] ?? 'badge badge-secondary';
                                                    @endphp
                                                    <tr>
                                                        <td data-order="{{ $order->id }}">
                                                            {{ $order->id }}
                                                            @if ($order->order_type === Order::TYPE_SUBSCRIPTION)
                                                                <br><span class="badge badge-info">Định kỳ</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $order->user->name }}</td>
                                                        <td>
                                                            <a href="javascript:void(0)" data-toggle="modal"
                                                                data-target="#addressShippingModal-{{ $order->id }}">
                                                                {{ $order->shippingAddress->address }}
                                                            </a>
                                                        </td>
                                                        <td>{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>
                                                        <td class="order-status">
                                                            <span class="custom-badge {{ $badgeClass }}">
                                                                {{ $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
                                                            </span>
                                                            @if ($order->status === Order::STATUS_OUT_FOR_DELIVERY && $order->dispatched_at)
                                                                <small class="text-muted d-block mt-1">Bắt đầu:
                                                                    {{ $order->dispatched_at->format('d/m/Y H:i') }}</small>
                                                            @endif
                                                            @if ($order->status === Order::STATUS_DELIVERED && $order->delivered_at)
                                                                <small class="text-muted d-block mt-1">Hoàn tất:
                                                                    {{ $order->delivered_at->format('d/m/Y H:i') }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($order->deliveryStaff)
                                                                {{ $order->deliveryStaff->name }}
                                                                <small
                                                                    class="text-muted d-block">{{ $order->deliveryStaff->phone_number }}</small>
                                                            @else
                                                                <span class="text-muted">Chưa phân công</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $paymentStatus = optional($order->payment)->status;
                                                                $paymentMethodLabels = [
                                                                    'cash' => 'COD',
                                                                    'paypal' => 'PayPal',
                                                                    'vietqr' => 'VietQR',
                                                                ];
                                                                $paymentMethod = optional($order->payment)->payment_method;
                                                            @endphp
                                                            @if ($paymentMethod)
                                                                <div class="text-muted small">{{ $paymentMethodLabels[$paymentMethod] ?? $paymentMethod }}</div>
                                                            @endif
                                                            @switch($paymentStatus)
                                                                @case('completed')
                                                                    <span class="custom-badge badge badge-success">Đã thanh
                                                                        toán</span>
                                                                @break

                                                                @case('failed')
                                                                    <span class="custom-badge badge badge-danger">Thanh toán thất
                                                                        bại</span>
                                                                @break

                                                                @default
                                                                    <span class="custom-badge badge badge-warning">Chờ thanh
                                                                        toán</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#orderItemsModal-{{ $order->id }}">
                                                                Xem chi tiết
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm dropdown-toggle"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    Thao tác
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    @if ($order->status === Order::STATUS_PENDING)
                                                                        <a class="dropdown-item confirm-order"
                                                                            href="javascript:void(0)"
                                                                            data-id="{{ $order->id }}"
                                                                            data-url="{{ route('admin.orders.confirm') }}">Xác nhận</a>
                                                                    @endif

                                                                    @if ($order->status === Order::STATUS_PROCESSING)
                                                                        <a class="dropdown-item pack-order"
                                                                            href="javascript:void(0)"
                                                                            data-id="{{ $order->id }}"
                                                                            data-url="{{ route('admin.orders.packed') }}">Đã đóng gói</a>
                                                                    @endif

                                                                    @if (in_array($order->status, [Order::STATUS_PACKED, Order::STATUS_READY_FOR_DELIVERY], true))
                                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                                            data-toggle="modal"
                                                                            data-target="#assignDeliveryModal-{{ $order->id }}">Phân
                                                                            công giao</a>
                                                                    @endif

                                                                    @if (!in_array($order->status, [Order::STATUS_CANCELED, Order::STATUS_COMPLETED, Order::STATUS_DELIVERED], true))
                                                                        <a class="dropdown-item cancel-order"
                                                                            href="javascript:void(0)"
                                                                            data-id="{{ $order->id }}"
                                                                            data-url="{{ route('admin.orders.cancel') }}">Hủy đơn</a>
                                                                    @endif

                                                                    @if (
                                                                        optional($order->payment)->status !== 'completed' &&
                                                                            !in_array($order->status, [Order::STATUS_CANCELED, Order::STATUS_COMPLETED], true) &&
                                                                            ($order->status === Order::STATUS_DELIVERED || optional($order->payment)->payment_method === 'vietqr'))
                                                                        <a class="dropdown-item confirm-payment"
                                                                            href="javascript:void(0)"
                                                                            data-id="{{ $order->id }}"
                                                                            data-url="{{ route('admin.orders.confirm-payment') }}">Xác
                                                                            nhận thanh toán</a>
                                                                    @endif

                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('admin.order-detail', $order->id) }}">Trang
                                                                        chi tiết</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @foreach ($orders as $order)
                                            {{-- Modal địa chỉ giao hàng --}}
                                            <div class="modal fade" id="addressShippingModal-{{ $order->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="addressShippingModalLabel-{{ $order->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="addressShippingModalLabel-{{ $order->id }}">Thông
                                                                tin giao hàng</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-left">
                                                            <p>Người nhận: {{ $order->shippingAddress->full_name }}</p>
                                                            <p>Địa chỉ: {{ $order->shippingAddress->address }}</p>
                                                            <p>Thành phố: {{ $order->shippingAddress->city }}</p>
                                                            <p>Số điện thoại: {{ $order->shippingAddress->phone }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal chi tiết đơn hàng --}}
                                            <div class="modal fade" id="orderItemsModal-{{ $order->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="orderItemsModalLabel-{{ $order->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="orderItemsModalLabel-{{ $order->id }}">Chi tiết hóa
                                                                đơn</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Tên sản phẩm</th>
                                                                        <th>Số lượng</th>
                                                                        <th>Đơn giá</th>
                                                                        <th>Thành tiền</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $index = 1; @endphp
                                                                    @foreach ($order->orderItems as $item)
                                                                        <tr>
                                                                            <td>{{ $index++ }}</td>
                                                                            <td>{{ $item->product->name }}</td>
                                                                            <td>{{ $item->quantity }}</td>
                                                                            <td>{{ number_format($item->price, 0, ',', '.') }}
                                                                                VNĐ</td>
                                                                            <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                                VNĐ</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal phân công giao hàng --}}
                                            <div class="modal fade" id="assignDeliveryModal-{{ $order->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="assignDeliveryModalLabel-{{ $order->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="assignDeliveryModalLabel-{{ $order->id }}">Phân
                                                                công giao hàng #{{ $order->id }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-left">
                                                            <form class="assign-delivery-form"
                                                                data-order-id="{{ $order->id }}">
                                                                <input type="hidden" name="order_id"
                                                                    value="{{ $order->id }}">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="delivery_staff_id_{{ $order->id }}">Nhân
                                                                        viên giao hàng</label>
                                                                    <select name="delivery_staff_id" class="form-control"
                                                                        id="delivery_staff_id_{{ $order->id }}"
                                                                        required>
                                                                        <option value="">-- Chọn nhân viên --
                                                                        </option>
                                                                        @foreach ($deliveryStaffs as $staff)
                                                                            <option value="{{ $staff->id }}"
                                                                                @if ($order->delivery_staff_id === $staff->id) selected @endif>
                                                                                {{ $staff->name }}
                                                                                ({{ $staff->phone_number }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="delivery_note_{{ $order->id }}">Ghi
                                                                        chú</label>
                                                                    <textarea name="note" id="delivery_note_{{ $order->id }}" class="form-control" rows="3"
                                                                        placeholder="Ví dụ: giao trước 18h, gọi khách trước..."></textarea>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Đóng</button>
                                                            <button type="button"
                                                                class="btn btn-primary submit-assign-delivery"
                                                                data-order-id="{{ $order->id }}"
                                                                data-url="{{ route('admin.orders.ready') }}">Lưu phân công</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection

@push('scripts')
    <script>
        $(function () {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable-buttons')) {
                $('#datatable-buttons').DataTable().order([0, 'desc']).draw();
            }
        });
    </script>
@endpush
