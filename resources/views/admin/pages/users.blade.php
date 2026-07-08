@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Chờ kích hoạt',
            'active' => 'Đang hoạt động',
            'banned' => 'Bị chặn',
            'deleted' => 'Đã xóa',
        ];

        $statusClasses = [
            'pending' => 'label-warning',
            'active' => 'label-success',
            'banned' => 'label-danger',
            'deleted' => 'label-default',
        ];

        $roleLabels = [
            'admin' => 'Admin',
            'staff' => 'Nhân viên',
            'delivery_staff' => 'Nhân viên giao hàng',
            'customer' => 'Khách hàng',
        ];

        $defaultCreateRoleId = optional($roles->firstWhere('name', 'customer'))->id ?? optional($roles->first())->id;
    @endphp

    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý người dùng</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Vui lòng kiểm tra lại thông tin:</strong>
                    <ul style="margin-bottom: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="x_panel">
                <div class="x_title">
                    <h2>Khach VIP</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    @if ($vipCustomers->isEmpty())
                        <div class="alert alert-info" style="margin-bottom: 0;">
                            Chua co du du lieu mua hang de xac dinh khach VIP.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Khach hang</th>
                                        <th>Lead Score</th>
                                        <th>Phan nhom</th>
                                        <th>Tong chi tieu</th>
                                        <th>Don thanh cong</th>
                                        <th>Don gan nhat</th>
                                        <th>Ghi chu uu tien</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vipCustomers as $vipCustomer)
                                        @php
                                            $vipUser = $vipCustomer['user'];
                                            $vipProfile = $vipCustomer['profile'];
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $vipUser->name }}</strong><br>
                                                <small>{{ $vipUser->email }}</small>
                                            </td>
                                            <td>
                                                <span class="label label-primary"
                                                    style="font-size: 13px; padding: 6px 10px;">
                                                    {{ $vipProfile['score'] }}/100
                                                </span>
                                            </td>
                                            <td>
                                                <span class="label {{ $vipProfile['segment_class'] }}">
                                                    {{ $vipProfile['segment_label'] }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($vipProfile['total_spent'], 0, ',', '.') }} VND</td>
                                            <td>{{ $vipProfile['successful_orders'] }}</td>
                                            <td>
                                                {{ $vipProfile['last_order_at'] ? $vipProfile['last_order_at']->format('d-m-Y') : 'Chua co' }}
                                            </td>
                                            <td>{{ $vipProfile['priority_note'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="x_panel">
                <div class="x_title">
                    <h2>Danh sách người dùng</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#modalCreateUser">
                                <i class="fa fa-plus"></i> Thêm người dùng
                            </button>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline"
                        style="margin-bottom: 20px;">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm theo tên, email hoặc số điện thoại" value="{{ $search ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">
                            <select name="role_id" class="form-control">
                                <option value="">Tất cả vai trò</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ (int) ($roleId ?? 0) === $role->id ? 'selected' : '' }}>
                                        {{ $roleLabels[$role->name] ?? $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                        @if ($search || $roleId)
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                                <i class="fa fa-times"></i> Xóa lọc
                            </a>
                        @endif
                    </form>

                    <div class="row">
                        @forelse ($users as $user)
                            @php
                                $avatar = $user->avatar ?: 'uploads/users/default-avatar.png';
                                $isCurrentAdmin = auth('admin')->id() === $user->id;
                                $leadProfile = $leadProfiles->get($user->id, [
                                    'applicable' => false,
                                    'score' => 0,
                                    'segment_label' => 'Khong ap dung',
                                    'segment_class' => 'label-default',
                                    'priority_note' => 'Tai khoan noi bo',
                                    'successful_orders' => 0,
                                    'total_spent' => 0,
                                    'last_order_at' => null,
                                    'payment_completion_rate' => 0,
                                    'cancellation_rate' => 0,
                                ]);
                            @endphp

                            <div class="col-md-4 col-sm-6 profile_details">
                                <div class="well profile_view">
                                    <div class="col-sm-12">
                                        <h4 class="brief text-uppercase">
                                            <i>{{ $roleLabels[optional($user->role)->name] ?? optional($user->role)->name }}</i>
                                            <span class="label {{ $statusClasses[$user->status] ?? 'label-default' }}"
                                                style="float: right;">
                                                {{ $statusLabels[$user->status] ?? $user->status }}
                                            </span>
                                        </h4>

                                        <div class="left col-md-7 col-sm-7">
                                            <h2>{{ $user->name }}</h2>
                                            <p><strong>Email: </strong>{{ $user->email }}</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-building"></i> Địa chỉ:
                                                    {{ $user->address ?: 'Chưa cập nhật' }}</li>
                                                <li><i class="fa fa-phone"></i> SĐT:
                                                    {{ $user->phone_number ?: 'Chưa cập nhật' }}</li>
                                            </ul>
                                            <div style="margin-top: 10px;">
                                                <span class="label {{ $leadProfile['segment_class'] }}">
                                                    Lead Score: {{ $leadProfile['score'] }}/100
                                                </span>
                                                <span class="label label-default">
                                                    {{ $leadProfile['segment_label'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="right col-md-5 col-sm-5 text-center">
                                            <img src="{{ asset('storage/' . $avatar) }}" alt="{{ $user->name }}"
                                                class="img-circle img-fluid">
                                        </div>
                                    </div>

                                    <div class="profile-bottom text-center">
                                        <div class="col-sm-12" style="margin-bottom: 12px; text-align: left;">
                                            <p style="margin-bottom: 5px;">
                                                <strong>Uu tien:</strong> {{ $leadProfile['priority_note'] }}
                                            </p>
                                            <p style="margin-bottom: 5px;">
                                                <strong>Don thanh cong:</strong> {{ $leadProfile['successful_orders'] }}
                                                | <strong>Chi tieu:</strong>
                                                {{ number_format($leadProfile['total_spent'], 0, ',', '.') }} VND
                                            </p>
                                            <p style="margin-bottom: 5px;">
                                                <strong>Ty le thanh toan xong:</strong>
                                                {{ $leadProfile['payment_completion_rate'] }}%
                                                | <strong>Ty le huy:</strong>
                                                {{ $leadProfile['cancellation_rate'] }}%
                                            </p>
                                            <p style="margin-bottom: 0;">
                                                <strong>Lan mua gan nhat:</strong>
                                                {{ $leadProfile['last_order_at'] ? $leadProfile['last_order_at']->format('d-m-Y H:i') : 'Chua co giao dich' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-12 emphasis">
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modalUpdateUser-{{ $user->id }}">
                                                <i class="fa fa-edit"></i> Sửa
                                            </button>

                                            @if ($isCurrentAdmin || $user->status === 'deleted')
                                                <button type="button" class="btn btn-danger btn-sm" disabled>
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.delete') }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modalUpdateUser-{{ $user->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="modalUpdateUserLabel-{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.users.update') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                                            @if ($isCurrentAdmin)
                                                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                                <input type="hidden" name="status" value="{{ $user->status }}">
                                            @endif

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalUpdateUserLabel-{{ $user->id }}">
                                                    Chỉnh sửa người dùng
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Họ tên <span class="required">*</span></label>
                                                            <input type="text" name="name" class="form-control"
                                                                value="{{ old('name', $user->name) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Email <span class="required">*</span></label>
                                                            <input type="email" name="email" class="form-control"
                                                                value="{{ old('email', $user->email) }}" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Mật khẩu mới</label>
                                                            <input type="password" name="password" class="form-control"
                                                                autocomplete="new-password"
                                                                placeholder="Để trống nếu không đổi">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Xác nhận mật khẩu</label>
                                                            <input type="password" name="password_confirmation"
                                                                class="form-control" autocomplete="new-password">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Số điện thoại</label>
                                                            <input type="text" name="phone_number" class="form-control"
                                                                value="{{ old('phone_number', $user->phone_number) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Ảnh đại diện</label>
                                                            <input type="file" name="avatar" class="form-control"
                                                                accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Vai trò <span class="required">*</span></label>
                                                            <select name="role_id" class="form-control"
                                                                {{ $isCurrentAdmin ? 'disabled' : '' }} required>
                                                                @foreach ($roles as $role)
                                                                    <option value="{{ $role->id }}"
                                                                        {{ (int) old('role_id', $user->role_id) === $role->id ? 'selected' : '' }}>
                                                                        {{ $roleLabels[$role->name] ?? $role->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Trạng thái <span class="required">*</span></label>
                                                            <select name="status" class="form-control"
                                                                {{ $isCurrentAdmin ? 'disabled' : '' }} required>
                                                                @foreach ($statuses as $status)
                                                                    <option value="{{ $status }}"
                                                                        {{ old('status', $user->status) === $status ? 'selected' : '' }}>
                                                                        {{ $statusLabels[$status] ?? $status }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Địa chỉ</label>
                                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-12">
                                <div class="alert alert-info text-center">
                                    Không tìm thấy người dùng phù hợp.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreateUser" tabindex="-1" role="dialog" aria-labelledby="modalCreateUserLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateUserLabel">Thêm người dùng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Họ tên <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mật khẩu <span class="required">*</span></label>
                                    <input type="password" name="password" class="form-control"
                                        autocomplete="new-password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu <span class="required">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        autocomplete="new-password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="text" name="phone_number" class="form-control"
                                        value="{{ old('phone_number') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ảnh đại diện</label>
                                    <input type="file" name="avatar" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vai trò <span class="required">*</span></label>
                                    <select name="role_id" class="form-control" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ (int) old('role_id', $defaultCreateRoleId) === $role->id ? 'selected' : '' }}>
                                                {{ $roleLabels[$role->name] ?? $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Trạng thái <span class="required">*</span></label>
                                    <select name="status" class="form-control" required>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status', 'active') === $status ? 'selected' : '' }}>
                                                {{ $statusLabels[$status] ?? $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success">Thêm người dùng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
