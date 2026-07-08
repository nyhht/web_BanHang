@extends('layouts.client')

@section('title', 'Gói định kỳ')

@section('breadcrumb', 'Gói định kỳ')

@section('content')
    <div class="ltn__myaccount-area pb-90">
        <div class="container">
            <div class="row mb-30">
                <div class="col-lg-8">
                    <h3>Gói đặt lịch định kỳ</h3>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('subscriptions.create') }}" class="theme-btn-1 btn btn-effect-1">Tạo gói mới</a>
                </div>
            </div>

            @if ($subscriptions->isEmpty())
                <div class="alert alert-info">
                    Bạn chưa có gói định kỳ nào. Hãy tạo gói để hệ thống tự lên đơn theo lịch giao.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Gói</th>
                                <th>Sản phẩm</th>
                                <th>Lịch giao</th>
                                <th>Địa chỉ</th>
                                <th>Tổng dự kiến</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <strong>#{{ $subscription->id }}</strong><br>
                                        <small>{{ strtoupper($subscription->payment_method) }}</small>
                                    </td>
                                    <td>
                                        @foreach ($subscription->items as $item)
                                            <div class="mb-1">
                                                {{ $item->product?->name ?? 'Sản phẩm đã xóa' }}
                                                x {{ $item->quantity }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $frequencyLabels[$subscription->frequency] ?? $subscription->frequency }}
                                        @if ($subscription->frequency === 'weekly')
                                            - {{ $weekDayLabels[$subscription->week_day] ?? '' }}
                                        @endif
                                        <br>
                                        <small>
                                            Lần tới:
                                            {{ $subscription->next_run_at ? $subscription->next_run_at->format('d/m/Y H:i') : 'Không còn lịch' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($subscription->shippingAddress)
                                            {{ $subscription->shippingAddress->address }}<br>
                                            <small>{{ $subscription->shippingAddress->city }}</small>
                                        @else
                                            <span class="text-danger">Địa chỉ đã bị xóa</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($subscription->estimated_total, 0, ',', '.') }} đ</td>
                                    <td>
                                        @php
                                            $badgeClass = [
                                                'active' => 'badge bg-success',
                                                'paused' => 'badge bg-warning',
                                                'canceled' => 'badge bg-danger',
                                                'expired' => 'badge bg-secondary',
                                            ][$subscription->status] ?? 'badge bg-secondary';
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $statusLabels[$subscription->status] ?? $subscription->status }}</span>
                                    </td>
                                    <td>
                                        @if ($subscription->status === 'active')
                                            <form action="{{ route('subscriptions.pause', $subscription->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-warning">Tạm dừng</button>
                                            </form>
                                        @elseif ($subscription->status === 'paused')
                                            <form action="{{ route('subscriptions.resume', $subscription->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success">Tiếp tục</button>
                                            </form>
                                        @endif

                                        @if (!in_array($subscription->status, ['canceled', 'expired'], true))
                                            <form action="{{ route('subscriptions.cancel', $subscription->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn hủy gói này?')">Hủy</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
