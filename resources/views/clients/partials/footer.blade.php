<footer class="ltn__footer-area  ">
    <div class="footer-top-area  section-bg-1 plr--5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-about-widget">
                        <div class="footer-logo">
                            <div class="site-logo">
                                <a href="/"><img src="{{ asset('assets/clients/img/logo.png') }}" alt="Logo"></a>
                            </div>
                        </div>
                        <p>Giải pháp vào bếp thông minh cho người bận rộn. Cung cấp các hộp nguyên liệu sơ chế, định lượng sẵn kèm nước sốt độc quyền chuẩn vị.</p>

                        <div class="footer-address">
                            <ul>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-placeholder"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p>Bình Minh, Hà Nội, Việt Nam</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-call"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p><a href="tel:0123456789">0123 456 789</a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-mail"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p><a href="mailto:nguyenhieu27hsht@gmail.com">nguyenhieu27hsht@gmail.com</a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="ltn__social-media mt-20">
                            <ul>
                                <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                <li><a href="#" title="Youtube"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Công ty</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{ route('about') }}">Giới thiệu</a></li>
                                <li><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                                <li><a href="{{ route('faq') }}">Câu hỏi</a></li>
                                <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Dịch vụ</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{ route('account') }}">Theo dõi đơn hàng</a></li>
                                <li><a href="{{ route('wishlist') }}">Danh sách yêu thích</a></li>
                                <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                                <li><a href="{{ route('account') }}">Tài khoản</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Hỗ trợ khách hàng</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{ route('login') }}">Đăng ký thành viên</a></li>
                                <li><a href="{{ route('account') }}">Chính sách bảo mật</a></li>
                                <li><a href="{{ route('wishlist') }}">Điều khoản dịch vụ</a></li>
                                <li><a href="{{ route('faq') }}">Hướng dẫn nấu nướng</a></li>
                                <li><a href="{{ route('contact') }}">Gửi phản hồi</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12 col-12">
                    <div class="footer-widget footer-newsletter-widget">
                        <h4 class="footer-title">Bảng tin</h4>
                        <p>Đăng ký nhận thông báo về các món ăn mới trong thực đơn tuần và ưu đãi giảm giá độc quyền.</p>
                        <div class="footer-newsletter">
                            <form action="javascript:void(0);" method="POST">
                                <input type="email" name="email" placeholder="Email nhận ưu đãi*" required>
                                <div class="btn-wrapper">
                                    <button class="theme-btn-1 btn" type="submit"><i class="fas fa-location-arrow"></i></button>
                                </div>
                            </form>
                        </div>
                        <h5 class="mt-30">Phương thức thanh toán</h5>
                        <img src="{{ asset('assets/clients/img/icons/payment-4.png') }}" alt="Payment Image">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ltn__copyright-area ltn__copyright-2 section-bg-2  ltn__border-top-2 plr--5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="ltn__copyright-design clearfix">
                        <p>Bản quyền thuộc về Hệ thống Đặt Hàng Meal-kit &copy; <span class="current-year">{{ date('Y') }}</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-12 align-self-center">
                    <div class="ltn__copyright-menu text-right text-end">
                        <ul>
                            <li><a href="#">Điều khoản sử dụng</a></li>
                            <li><a href="#">Chính sách hoàn tiền</a></li>
                            <li><a href="#">Chính sách bảo mật</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>