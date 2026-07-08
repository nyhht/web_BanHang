@extends('layouts.admin')

@php
    use App\Models\Order;

    $statusLabels = Order::statusLabels();

    $deliveryHistoryStatuses = [
        Order::STATUS_READY_FOR_DELIVERY,
        Order::STATUS_OUT_FOR_DELIVERY,
        Order::STATUS_DELIVERED,
        Order::STATUS_COMPLETED,
    ];

    $deliveryStatusLabels = [
        Order::STATUS_READY_FOR_DELIVERY => 'Sẵn sàng giao',
        Order::STATUS_OUT_FOR_DELIVERY => 'Đang giao',
        Order::STATUS_DELIVERED => 'Đã giao',
        Order::STATUS_COMPLETED => 'Đã giao / Hoàn thành',
    ];
@endphp

@section('title', 'Quản lý giao hàng')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý giao hàng <small>Theo dõi tiến trình giao nhận</small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách giao hàng</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form method="GET" class="form-inline" style="margin-bottom: 20px;">
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="filter-status" class="sr-only">Trạng thái</label>
                                    <select name="status" id="filter-status" class="form-control">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="{{ Order::STATUS_READY_FOR_DELIVERY }}" @selected(($filters['status'] ?? '') === Order::STATUS_READY_FOR_DELIVERY)>
                                            Sẵn sàng giao</option>
                                        <option value="{{ Order::STATUS_OUT_FOR_DELIVERY }}" @selected(($filters['status'] ?? '') === Order::STATUS_OUT_FOR_DELIVERY)>
                                            Đang giao</option>
                                        <option value="{{ Order::STATUS_DELIVERED }}" @selected(($filters['status'] ?? '') === Order::STATUS_DELIVERED)>Đã giao
                                        </option>
                                    </select>
                                </div>
                                @if ($adminUser->role->name !== 'delivery_staff')
                                    <div class="form-group" style="margin-right: 10px;">
                                        <label for="filter-staff" class="sr-only">Nhân viên giao</label>
                                        <select name="delivery_staff_id" id="filter-staff" class="form-control">
                                            <option value="">Tất cả nhân viên</option>
                                            @foreach ($deliveryStaffs as $staff)
                                                <option value="{{ $staff->id }}" @selected(($filters['delivery_staff_id'] ?? '') == $staff->id)>
                                                    {{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary">Lọc</button>
                                @if (!empty($filters))
                                    <a href="{{ route('admin.deliveries.index') }}" class="btn btn-default"
                                        style="margin-left: 10px;">Đặt lại</a>
                                @endif
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="width:100%; text-align:center;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Khách hàng</th>
                                            <th>Địa chỉ giao</th>
                                            <th>Nhân viên giao</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian</th>
                                            <th>Liên hệ</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->user->name }}</td>
                                                <td class="text-left">
                                                    <strong>{{ $order->shippingAddress->full_name }}</strong><br>
                                                    {{ $order->shippingAddress->address }},
                                                    {{ $order->shippingAddress->city }}
                                                </td>
                                                <td>
                                                    @if ($order->deliveryStaff)
                                                        {{ $order->deliveryStaff->name }}<br>
                                                        <small>{{ $order->deliveryStaff->phone_number }}</small>
                                                    @else
                                                        <span class="text-muted">Chưa phân công</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeMap = [
                                                            Order::STATUS_READY_FOR_DELIVERY => 'badge badge-info',
                                                            Order::STATUS_OUT_FOR_DELIVERY => 'badge badge-primary',
                                                            Order::STATUS_DELIVERED => 'badge badge-success',
                                                            Order::STATUS_COMPLETED => 'badge badge-success',
                                                        ];
                                                        $badgeClass =
                                                            $badgeMap[$order->status] ?? 'badge badge-secondary';
                                                    @endphp
                                                    <span class="custom-badge {{ $badgeClass }}">
                                                        {{ $deliveryStatusLabels[$order->status] ?? ($statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status))) }}
                                                    </span>
                                                </td>
                                                <td class="text-left">
                                                    <div><strong>Tạo:</strong>
                                                        {{ $order->created_at->format('d/m/Y H:i') }}</div>
                                                    @if ($order->dispatched_at)
                                                        <div><strong>Bắt đầu:</strong>
                                                            {{ $order->dispatched_at->format('d/m/Y H:i') }}</div>
                                                    @endif
                                                    @if ($order->delivered_at)
                                                        <div><strong>Hoàn tất:</strong>
                                                            {{ $order->delivered_at->format('d/m/Y H:i') }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-left">
                                                    <div><i class="fa fa-phone"></i> {{ $order->shippingAddress->phone }}
                                                    </div>
                                                    <div><i class="fa fa-envelope"></i> {{ $order->user->email }}</div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#deliveryOrderItemsModal-{{ $order->id }}">Chi
                                                        tiết</button>
                                                    @if ($order->status === Order::STATUS_READY_FOR_DELIVERY)
                                                        <button type="button" class="btn btn-primary btn-sm start-delivery"
                                                            data-id="{{ $order->id }}"
                                                            data-url="{{ route('admin.deliveries.start') }}">Bắt đầu giao</button>
                                                    @endif
                                                    @if ($order->status === Order::STATUS_OUT_FOR_DELIVERY)
                                                        <button type="button"
                                                            class="btn btn-success btn-sm complete-delivery"
                                                            data-id="{{ $order->id }}"
                                                            data-url="{{ route('admin.deliveries.complete') }}">Hoàn tất giao</button>
                                                    @endif
                                                </td>
                                            </tr>


                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Chưa có đơn giao hàng nào
                                                    phù hợp.</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                            @foreach ($orders as $modalOrder)
                                <div class="modal fade" id="deliveryOrderItemsModal-{{ $modalOrder->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="deliveryOrderItemsLabel-{{ $modalOrder->id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deliveryOrderItemsLabel-{{ $modalOrder->id }}">
                                                    Chi tiết đơn hàng #{{ $modalOrder->id }}
                                                </h5>
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
                                                            <th>Sản phẩm</th>
                                                            <th>Số lượng</th>
                                                            <th>Đơn giá</th>
                                                            <th>Thành tiền</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $index = 1; @endphp
                                                        @foreach ($modalOrder->orderItems as $item)
                                                            <tr>
                                                                <td>{{ $index++ }}</td>
                                                                <td>{{ $item->product->name }}</td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                                                <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                    VNĐ</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                                @php
                                                    $deliveryHistories = $modalOrder->orderStatusHistory
                                                        ->whereIn('status', $deliveryHistoryStatuses);
                                                @endphp

                                                <h5>Lịch sử giao hàng</h5>
                                                <ul class="delivery-history-list">
                                                    @forelse ($deliveryHistories as $history)
                                                        <li>
                                                            <span class="custom-badge badge badge-info">
                                                                {{ $deliveryStatusLabels[$history->status] ?? ($statusLabels[$history->status] ?? ucfirst(str_replace('_', ' ', $history->status))) }}
                                                            </span>
                                                            <span class="delivery-history-time">
                                                                {{ $history->changed_at->format('d/m/Y H:i') }}
                                                            </span>
                                                            @if ($history->note)
                                                                <div class="delivery-history-note">{{ $history->note }}</div>
                                                            @endif
                                                        </li>
                                                    @empty
                                                        <li class="text-muted">Chưa có lịch sử giao hàng.</li>
                                                    @endforelse
                                                </ul>
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
@endsection
