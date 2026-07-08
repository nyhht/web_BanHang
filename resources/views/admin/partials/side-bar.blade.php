<div class="col-md-3 left_col">
    @php
        $avatarPath = optional($userAdmin)->avatar ? asset('storage/' . $userAdmin->avatar) : asset('images/default-avatar.png');
        $adminHomeRoute = optional($userAdmin?->role)->name === 'delivery_staff'
            ? route('admin.deliveries.index')
            : route('admin.dashboard');
    @endphp

    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ $adminHomeRoute }}" class="site_title"
                style="display: flex; align-items: center; justify-content: center; height: 57px; padding: 0 16px; text-decoration: none;">
                <span
                    style="font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; font-size: 24px; font-weight: 800; line-height: 1; letter-spacing: 0.4px; color: #fff;">
                    Mealkit
                </span>
            </a>
        </div>

        <div class="clearfix"></div>

        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ $avatarPath }}" alt="Avatar" class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Xin chào,</span>
                <h2>{{ ucfirst(str_replace('_', ' ', optional($userAdmin?->role)->name ?? 'Admin')) }}</h2>
            </div>
        </div>

        <br />

        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Tổng quan</h3>
                <ul class="nav side-menu">
                    @if (optional($userAdmin?->role)->name !== 'delivery_staff')
                        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_users'))
                        <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-users"></i> Quản lý người dùng</a></li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_categories'))
                        <li><a><i class="fa fa-lock"></i> Quản lý danh mục <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('admin.categories.add') }}">Thêm danh mục</a></li>
                                <li><a href="{{ route('admin.categories.index') }}">Danh sách danh mục</a></li>
                            </ul>
                        </li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_products'))
                        <li><a><i class="fa fa-desktop"></i> Quản lý sản phẩm <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('admin.product.add') }}">Thêm sản phẩm</a></li>
                                <li><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
                            </ul>
                        </li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_coupons'))
                        <li><a href="{{ route('admin.coupons.index') }}"><i class="fa fa-ticket"></i> Quản lý mã giảm giá</a></li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_orders'))
                        <li><a href="{{ route('admin.orders.index') }}"><i class="fa fa-edit"></i> Quản lý đơn hàng</a></li>
                        <li><a href="{{ route('admin.subscriptions.index') }}"><i class="fa fa-calendar"></i> Quản lý gói định kỳ</a></li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_deliveries'))
                        <li><a href="{{ route('admin.deliveries.index') }}"><i class="fa fa-truck"></i> Quản lý giao hàng</a></li>
                    @endif

                    @if ($userAdmin && $userAdmin->role->permissions->contains('name', 'manage_contacts'))
                        <li><a href="{{ route('admin.contacts.index') }}"><i class="fa fa-envelope"></i> Quản lý liên hệ</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Đăng xuất" href="{{ route('admin.logout') }}" data-logout-confirm>
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>
