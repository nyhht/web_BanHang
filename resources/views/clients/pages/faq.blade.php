@extends('layouts.client')

@section('title', 'Câu hỏi')

@section('breadcrumb', 'Những câu hỏi thường gặp')

@section('content')    
        <!-- FAQ AREA START (faq-2) (ID > accordion_2) -->
        <div class="ltn__faq-area mb-100">
            <div class="container">
                <div class="row">
                    <!-- Phần câu hỏi -->
                    <div class="col-lg-8">
                        <div class="ltn__faq-inner ltn__faq-inner-2">
                            <div id="accordion_2">
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-1" aria-expanded="false">
                                        Làm sao để mua sản phẩm?
                                    </h6>
                                    <div id="faq-item-2-1" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Để mua sản phẩm, bạn chỉ cần chọn sản phẩm mong muốn, thêm vào giỏ hàng và tiến hành thanh toán. Sau khi đơn hàng được xác nhận, chúng tôi sẽ giao hàng đến địa chỉ bạn cung cấp.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-3" aria-expanded="false">
                                        Tôi là người dùng mới. Nên bắt đầu như thế nào?
                                    </h6>
                                    <div id="faq-item-2-3" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Đăng ký tài khoản, cập nhật thông tin cá nhân, sau đó bạn có thể duyệt sản phẩm, thêm vào giỏ hàng và bắt đầu mua sắm.</p>
                                        </div>
                                    </div>
                                </div>
                        
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-5" aria-expanded="false">
                                        Thông tin của tôi có được bảo mật không?
                                    </h6>
                                    <div id="faq-item-2-5" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của bạn theo chính sách bảo mật của công ty. Dữ liệu được mã hóa và không chia sẻ với bên thứ ba.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-6" aria-expanded="false">
                                        Mã khuyến mãi không hoạt động?
                                    </h6>
                                    <div id="faq-item-2-6" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Hãy chắc chắn rằng mã khuyến mãi còn hiệu lực và áp dụng đúng điều kiện. Nếu vẫn không được, vui lòng liên hệ hỗ trợ để kiểm tra chi tiết hơn.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- card -->
                                <div class="card">
                                    <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse"
                                        data-bs-target="#faq-item-2-7" aria-expanded="false">
                                        Tôi thanh toán bằng thẻ tín dụng như thế nào?
                                    </h6>
                                    <div id="faq-item-2-7" class="collapse" data-parent="#accordion_2">
                                        <div class="card-body">
                                            <p>Trong quá trình thanh toán, hãy chọn phương thức “Thẻ tín dụng”, sau đó nhập thông tin thẻ và xác nhận. Hệ thống sẽ xử lý thanh toán bảo mật thông qua đối tác trung gian.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Hỗ trợ -->
                            <div class="need-support text-center mt-100">
                                <h2>Vẫn cần hỗ trợ? Hãy liên hệ chúng tôi 24/7:</h2>
                                <div class="btn-wrapper mb-30">
                                    <a href="contact.html" class="theme-btn-1 btn">Liên hệ ngay</a>
                                </div>
                                <h3><i class="fas fa-phone"></i> +0123-456-789</h3>
                            </div>
                        </div>
                    </div>
        
                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <aside class="sidebar-area ltn__right-sidebar">
                            <!-- Newsletter -->
                            <div class="widget ltn__search-widget ltn__newsletter-widget">
                                <h6 class="ltn__widget-sub-title">// nhận bản tin</h6>
                                <h4 class="ltn__widget-title">Đăng ký nhận thông tin</h4>
                                <form action="#">
                                    <input type="text" name="email" placeholder="Nhập email của bạn">
                                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                                </form>
                                <div class="ltn__newsletter-bg-icon">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                            </div>
                            <!-- Banner -->
                            <div class="widget ltn__banner-widget">
                                <a href="{{ route('products.index') }}"><img src="{{ asset('assets/clients/img/banner/banner-3.jpg')}}" alt="Hình ảnh quảng cáo"></a>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ AREA START -->

        <!-- COUNTER UP AREA START -->
        <div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70" data-bg="{{ asset('assets/clients/img/bg/5.jpg')}}">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-6 align-self-center">
                        <div class="ltn__counterup-item-3 text-color-white text-center">
                            <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/25.png')}}" alt="#"> </div>
                            <h1><span class="counter">3000</span><span class="counterUp-icon">+</span> </h1>
                            <h6>Bữa ăn được phục vụ</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 align-self-center">
                        <div class="ltn__counterup-item-3 text-color-white text-center">
                            <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/26.png')}}" alt="#"> </div>
                            <h1><span class="counter">100</span><span class="counterUp-letter">K</span><span
                                    class="counterUp-icon">+</span> </h1>
                            <h6>Công thức độc quyền</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 align-self-center">
                        <div class="ltn__counterup-item-3 text-color-white text-center">
                            <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/27.png')}}" alt="#"> </div>
                            <h1><span class="counter">100</span><span class="counterUp-icon">%</span> </h1>
                            <h6>Tươi sạch, an toàn</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 align-self-center">
                        <div class="ltn__counterup-item-3 text-color-white text-center">
                            <div class="counter-icon"> <img src="{{ asset('assets/clients/img/icons/icon-img/28.png')}}" alt="#"> </div>
                            <h1><span class="counter">15</span><span class="counterUp-icon">+</span> </h1>
                            <h6>Tiết kiệm thời gian</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- COUNTER UP AREA END -->
@endsection
