@extends('layouts.admin')

@section('title', 'Quản lý khuyến mãi')

@php
    $dateListText = fn ($coupon) => implode(', ', $coupon->auto_assign_dates ?? []);
@endphp

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý khuyến mãi</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thêm mã mới</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form action="{{ route('admin.coupons.store', [], false) }}" method="POST" class="form-horizontal form-label-left">
                                @csrf
                                @include('admin.pages.partials.coupon-form', [
                                    'coupon' => null,
                                    'products' => $products,
                                    'customers' => $customers,
                                    'dateListText' => $dateListText,
                                ])
                                <div class="ln_solid"></div>
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 offset-md-2">
                                        <button type="submit" class="btn btn-success">Thêm mã</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách mã giảm giá</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Mã</th>
                                            <th>Giảm</th>
                                            <th>Thời gian</th>
                                            <th>Tự tặng</th>
                                            <th>Deal sản phẩm</th>
                                            <th>Đã dùng</th>
                                            <th>Trạng thái</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($coupons as $coupon)
                                            <tr>
                                                <td>
                                                    <strong>{{ $coupon->code }}</strong>
                                                    @if ($coupon->restricted_to_assigned_users)
                                                        <br><span class="badge badge-info">Chỉ khách được tặng</span>
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->discount_percentage }}%</td>
                                                <td>
                                                    @if ($coupon->starts_at)
                                                        Bắt đầu: {{ $coupon->starts_at->format('d/m/Y H:i') }}<br>
                                                    @endif
                                                    @if ($coupon->expires_at)
                                                        Hết hạn: {{ $coupon->expires_at->format('d/m/Y H:i') }}
                                                        @if ($coupon->isExpired())
                                                            <span class="badge badge-danger">Hết hạn</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">Không giới hạn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($coupon->auto_assign_on_register)
                                                        <span class="badge badge-success">Đăng ký mới</span><br>
                                                    @endif
                                                    @if ($coupon->auto_assign_weekend)
                                                        <span class="badge badge-success">Cuối tuần</span><br>
                                                    @endif
                                                    @if (!empty($coupon->auto_assign_dates))
                                                        <span class="badge badge-success">Ngày lễ</span>
                                                    @endif
                                                    @if (!$coupon->auto_assign_on_register && !$coupon->auto_assign_weekend && empty($coupon->auto_assign_dates))
                                                        <span class="text-muted">Không</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($coupon->auto_apply_to_products)
                                                        <span class="badge badge-warning">Tự áp hằng ngày</span><br>
                                                        <small>{{ $coupon->daily_product_limit ?? 6 }} món/ngày</small>
                                                    @else
                                                        <span class="text-muted">Không</span>
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->times_used }} / {{ $coupon->usage_limit ?? 'Không giới hạn' }}</td>
                                                <td>
                                                    @if ($coupon->is_active)
                                                        <span class="badge badge-success">Đang kích hoạt</span>
                                                    @else
                                                        <span class="badge badge-secondary">Đã tắt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editCouponModal-{{ $coupon->id }}">
                                                        <i class="fa fa-edit"></i> Sửa
                                                    </button>
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.coupons.destroy', $coupon, false) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa mã này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editCouponModal-{{ $coupon->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật mã giảm giá</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.coupons.update', $coupon, false) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                @include('admin.pages.partials.coupon-form', [
                                                                    'coupon' => $coupon,
                                                                    'products' => $products,
                                                                    'customers' => $customers,
                                                                    'dateListText' => $dateListText,
                                                                ])
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Chưa có mã giảm giá nào.</td>
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
    </div>

    @if (session('success'))
        <script>
            toastr.success({{ json_encode(session('success')) }});
        </script>
    @endif

    @if (isset($errors) && $errors->any())
        <script>
            toastr.error({{ json_encode($errors->first()) }});
        </script>
    @endif

    <script>
        (function () {
            function normalize(value) {
                return (value || '').toString().toLowerCase().trim();
            }

            function updatePicker(picker) {
                const select = document.getElementById(picker.dataset.target);
                if (!select) {
                    return;
                }

                const category = picker.querySelector('.js-coupon-product-category')?.value || '';
                const keyword = normalize(picker.querySelector('.js-coupon-product-search')?.value);
                let visibleCount = 0;

                Array.from(select.options).forEach(function (option) {
                    const categoryMatch = !category || option.dataset.category === category;
                    const keywordMatch = !keyword || normalize(option.dataset.search || option.textContent).includes(keyword);
                    const isVisible = categoryMatch && keywordMatch;

                    option.hidden = !isVisible;
                    option.style.display = isVisible ? '' : 'none';

                    if (isVisible) {
                        visibleCount++;
                    }
                });

                Array.from(select.querySelectorAll('optgroup')).forEach(function (group) {
                    const hasVisibleOption = Array.from(group.children).some(function (option) {
                        return !option.hidden;
                    });

                    group.hidden = !hasVisibleOption;
                    group.style.display = hasVisibleOption ? '' : 'none';
                });

                updateCount(picker, select, visibleCount);
            }

            function updateCount(picker, select, visibleCount) {
                const count = picker.querySelector('.js-coupon-products-count');
                if (!count) {
                    return;
                }

                const selectedCount = Array.from(select.selectedOptions).length;
                count.textContent = selectedCount + ' đã chọn / ' + visibleCount + ' đang hiển thị';
            }

            function forVisibleOptions(picker, callback) {
                const select = document.getElementById(picker.dataset.target);
                if (!select) {
                    return;
                }

                Array.from(select.options).forEach(function (option) {
                    if (!option.hidden) {
                        callback(option);
                    }
                });

                select.dispatchEvent(new Event('change', { bubbles: true }));
                updatePicker(picker);
            }

            document.addEventListener('input', function (event) {
                const picker = event.target.closest('.js-coupon-product-picker');
                if (picker && event.target.matches('.js-coupon-product-search')) {
                    updatePicker(picker);
                }
            });

            document.addEventListener('change', function (event) {
                const picker = event.target.closest('.js-coupon-product-picker');
                if (picker && event.target.matches('.js-coupon-product-category')) {
                    updatePicker(picker);
                    return;
                }

                if (event.target.matches('.js-coupon-products-select')) {
                    const relatedPicker = document.querySelector('.js-coupon-product-picker[data-target="' + event.target.id + '"]');
                    if (relatedPicker) {
                        updatePicker(relatedPicker);
                    }
                }
            });

            document.addEventListener('click', function (event) {
                const picker = event.target.closest('.js-coupon-product-picker');
                if (!picker) {
                    return;
                }

                if (event.target.matches('.js-coupon-products-select-visible')) {
                    forVisibleOptions(picker, function (option) {
                        option.selected = true;
                    });
                }

                if (event.target.matches('.js-coupon-products-clear-visible')) {
                    forVisibleOptions(picker, function (option) {
                        option.selected = false;
                    });
                }

                if (event.target.matches('.js-coupon-products-clear-all')) {
                    const select = document.getElementById(picker.dataset.target);
                    if (!select) {
                        return;
                    }

                    Array.from(select.options).forEach(function (option) {
                        option.selected = false;
                    });
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    updatePicker(picker);
                }
            });

            document.querySelectorAll('.js-coupon-product-picker').forEach(updatePicker);
        })();
    </script>
@endsection
