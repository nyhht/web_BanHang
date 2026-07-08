@extends('layouts.admin')

@section('title', 'Quản lý thông báo')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Thông báo</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Tại đây, bạn có thể xem và quản lý các thông báo liên lạc hoặc <br> đơn hàng từ khách hàng, và theo dõi các trao đổi để cải thiện dịch vụ.</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-md-9 col-sm-9 ">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">

                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active " aria-labelledby="home-tab">
                                                <ul class="messages">
                                                    @foreach ($notifications as $notification)
                                                        <li style="display: flex; ">
                                                            <div>
                                                                <img src="{{ asset('assets/admin/images/bell_notifications.png') }}"
                                                                style="width: 34px; height: 34px;" alt="">
                                                            </div>
                                                                <div class="message_wrapper"  style="min-width: 400px">
                                                                    <a href="{{ '../admin' . $notification->link }}" class="notification-item" data-id="{{ $notification->id }}">
                                                                        <h4 class="heading">{{ $notification->title }}</h4>
                                                                    </a>
                                                                    <blockquote class="message">{{ Str::limit($notification->message, 100) }}
                                                                    </blockquote>
                                                                    <br />
    
                                                                </div>
                                                            <div class="message_date">
                                                                <p class="month">{{ $notification->created_at->format('h:i A d-m-Y') }}</p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->
@endsection
