@extends('layouts.admin')

@section('title', 'Quản lý gói định kỳ')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý gói định kỳ <small>Theo dõi gói khách đã đặt</small></h3>
                </div>
            </div>

            <div class="clearfix"></div>

            @php
                $totalSubscriptions = $statusCounts->sum();
                $cards = [
                    'all' => ['label' => 'Tất cả', 'count' => $totalSubscriptions, 'class' => 'bg-blue'],
                    'active' => ['label' => 'Đang đặt', 'count' => $statusCounts['active'] ?? 0, 'class' => 'bg-green'],
                    'paused' => ['label' => 'Tạm dừng', 'count' => $statusCounts['paused'] ?? 0, 'class' => 'bg-orange'],
                    'canceled' => ['label' => 'Đã hủy', 'count' => $statusCounts['canceled'] ?? 0, 'class' => 'bg-red'],
                    'expired' => ['label' => 'Hết hạn', 'count' => $statusCounts['expired'] ?? 0, 'class' => 'bg-purple'],
                ];
            @endphp

            <div class="row tile_count">
                @foreach ($cards as $status => $card)
                    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                        <span class="count_top">{{ $card['label'] }}</span>
                        <div class="count">{{ $card['count'] }}</div>
                        @if ($status !== 'all')
                            <span class="count_bottom">
                                <a href="{{ route('admin.subscriptions.index', ['status' => $status]) }}">Xem danh sách</a>
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách gói định kỳ</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="row mb-3">
                                <div class="col-md-3">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="">Tất cả</option>
                                        @foreach ($statusLabels as $value => $label)
                                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Chu kỳ</label>
                                    <select name="frequency" class="form-control">
                                        <option value="">Tất cả</option>
                                        @foreach ($frequencyLabels as $value => $label)
                                            <option value="{{ $value }}" @selected(($filters['frequency'] ?? '') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        value="{{ $filters['keyword'] ?? '' }}"
                                        placeholder="Mã gói, tên khách, email, SĐT, địa chỉ">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Lọc</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="text-align:center;">
                                    <thead>
                                        <tr>
                                            <th>Mã gói</th>
                                            <th>Khách hàng</th>
                                            <th>Sản phẩm</th>
                                            <th>Lịch giao</th>
                                            <th>Địa chỉ</th>
                                            <th>Dự kiến</th>
                                            <th>Đơn đã sinh</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subscriptions as $subscription)
                                            @php
                                                $badgeMap = [
                                                    'active' => 'badge badge-success',
                                                    'paused' => 'badge badge-warning',
                                                    'canceled' => 'badge badge-danger',
                                                    'expired' => 'badge badge-secondary',
                                                ];
                                                $badgeClass = $badgeMap[$subscription->status] ?? 'badge badge-secondary';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>#{{ $subscription->id }}</strong><br>
                                                    <small>{{ strtoupper($subscription->payment_method) }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $subscription->user?->name ?? 'Không rõ' }}</strong><br>
                                                    <small>{{ $subscription->user?->email }}</small>
                                                </td>
                                                <td style="text-align:left;">
                                                    @foreach ($subscription->items as $item)
                                                        <div>
                                                            {{ $item->product?->name ?? 'Sản phẩm đã xóa' }}
                                                            x {{ $item->quantity }}
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ $frequencyLabels[$subscription->frequency] ?? $subscription->frequency }}
                                                    @if ($subscription->frequency === 'weekly')
                                                        <br>{{ $weekDayLabels[$subscription->week_day] ?? '' }}
                                                    @endif
                                                    <br>
                                                    <small>
                                                        Lần tới:
                                                        {{ $subscription->next_run_at ? $subscription->next_run_at->format('d/m/Y H:i') : 'Không còn lịch' }}
                                                    </small>
                                                    @if ($subscription->end_date)
                                                        <br><small>Đến {{ $subscription->end_date->format('d/m/Y') }}</small>
                                                    @endif
                                                </td>
                                                <td style="text-align:left;">
                                                    @if ($subscription->shippingAddress)
                                                        <strong>{{ $subscription->shippingAddress->full_name }}</strong><br>
                                                        {{ $subscription->shippingAddress->phone }}<br>
                                                        {{ $subscription->shippingAddress->address }},
                                                        {{ $subscription->shippingAddress->city }}
                                                    @else
                                                        <span class="text-danger">Địa chỉ đã bị xóa</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ number_format($subscription->estimated_total, 0, ',', '.') }} VNĐ<br>
                                                    <small>Ship: {{ number_format($subscription->estimated_shipping_fee, 0, ',', '.') }} VNĐ</small>
                                                </td>
                                                <td>
                                                    @forelse ($subscription->orders as $order)
                                                        <a href="{{ route('admin.order-detail', $order->id) }}">#{{ $order->id }}</a>
                                                        <small>({{ $order->created_at->format('d/m') }})</small><br>
                                                    @empty
                                                        <span class="text-muted">Chưa có</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    <span class="{{ $badgeClass }}">
                                                        {{ $statusLabels[$subscription->status] ?? $subscription->status }}
                                                    </span>
                                                    @if ($subscription->last_order_generated_at)
                                                        <br><small>Tạo đơn gần nhất: {{ $subscription->last_order_generated_at->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($subscription->status === 'active')
                                                        <button class="btn btn-warning btn-xs subscription-action"
                                                            data-url="{{ route('admin.subscriptions.pause') }}"
                                                            data-id="{{ $subscription->id }}"
                                                            data-confirm="Tạm dừng gói #{{ $subscription->id }}?">
                                                            Tạm dừng
                                                        </button>
                                                    @elseif ($subscription->status === 'paused')
                                                        <button class="btn btn-success btn-xs subscription-action"
                                                            data-url="{{ route('admin.subscriptions.resume') }}"
                                                            data-id="{{ $subscription->id }}"
                                                            data-confirm="Tiếp tục gói #{{ $subscription->id }}?">
                                                            Tiếp tục
                                                        </button>
                                                    @endif

                                                    @if (!in_array($subscription->status, ['canceled', 'expired'], true))
                                                        <button class="btn btn-danger btn-xs subscription-action"
                                                            data-url="{{ route('admin.subscriptions.cancel') }}"
                                                            data-id="{{ $subscription->id }}"
                                                            data-confirm="Hủy gói #{{ $subscription->id }}?">
                                                            Hủy
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9">Không có gói định kỳ phù hợp.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{ $subscriptions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.subscription-action', function () {
            const button = $(this);

            if (!confirm(button.data('confirm'))) {
                return;
            }

            $.ajax({
                url: button.data('url'),
                method: 'POST',
                data: {
                    id: button.data('id'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message || 'Cập nhật thành công.');
                    location.reload();
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Không thể cập nhật gói định kỳ.');
                }
            });
        });
    </script>
@endpush
