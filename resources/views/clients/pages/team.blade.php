@extends('layouts.client')

@section('title', 'Đội ngũ')
@section('breadcrumb', 'Đội ngũ của chúng tôi')

@section('content')    
<div class="ltn__team-area pt-110--- pb-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h6 class="section-subtitle ltn__secondary-color">Đội ngũ nhân sự</h6>
                    <h1 class="section-title">Những Người Đồng Sáng Lập<span>.</span></h1>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/1.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color"> Người sáng lập </h6>
                        <h4><a href="javascript:void(0)">Nguyễn Yên Hiếu</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/2.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color"> Giám đốc điều hành </h6>
                        <h4><a href="javascript:void(0)">Nguyễn Thị Thu Bích</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/3.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Đầu bếp trưởng - Nghiên cứu thực đơn</h6>
                        <h4><a href="javascript:void(0)">Phạm Nam</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/4.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Trưởng bộ phận Giám sát chất lượng</h6>
                        <h4><a href="javascript:void(0)">Lê Thị Mai</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/5.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Trưởng bộ phận Vận hành luồng kho</h6>
                        <h4><a href="javascript:void(0)">Nguyễn Văn Hoàng</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/6.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Chuyên gia Dinh dưỡng</h6>
                        <h4><a href="javascript:void(0)">Nguyễn Quốc Đại</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/7.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Quản lý chuỗi cung ứng sạch</h6>
                        <h4><a href="javascript:void(0)">Nguyễn Quốc Anh</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="ltn__team-item">
                    <div class="team-img">
                        <img src="{{ asset('assets/clients/img/team/8.jpg') }}" alt="Image">
                    </div>
                    <div class="team-info">
                        <h6 class="ltn__secondary-color">Giám đốc Marketing nền tảng</h6>
                        <h4><a href="javascript:void(0)">Nguyễn Thị Hạnh</a></h4>
                        <div class="ltn__social-media">
                            <ul>
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ltn__progress-bar-area pt-115 pb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="ltn__progress-bar-wrap">
                    <div class="section-title-area ltn__section-title-2">
                        <h6 class="section-subtitle ltn__secondary-color">// Năng lực cốt lõi</h6>
                        <h1 class="section-title">Quy Chuẩn Vận Hành Nghiêm Ngặt<span>.</span></h1>
                        <p>Hệ thống Meal-kit ứng dụng mô hình quản lý chuỗi cung ứng khép kín hiện đại. Đội ngũ chuyên gia của chúng tôi luôn đảm bảo từng hộp nguyên liệu đến tay khách hàng đạt điểm tối đa về độ tươi ngon, giá trị dinh dưỡng và tốc độ giao vận.</p>
                    </div>
                    <div class="ltn__progress-bar-inner">
                        <div class="ltn__progress-bar-item">
                            <p>Kiểm định và Tuyển chọn Nguyên liệu</p>
                            <div class="progress">
                                <div class="progress-bar wow fadeInLeft" data-wow-duration="0.5s"
                                    data-wow-delay=".5s" role="progressbar" style="width: 95%">
                                    <span>95%</span>
                                </div>
                            </div>
                        </div>
                        <div class="ltn__progress-bar-item">
                            <p>Cân bằng Dinh dưỡng & Công thức độc quyền</p>
                            <div class="progress">
                                <div class="progress-bar wow fadeInLeft" data-wow-duration="0.5s"
                                    data-wow-delay=".5s" role="progressbar" style="width: 90%">
                                    <span>90%</span>
                                </div>
                            </div>
                        </div>
                        <div class="ltn__progress-bar-item">
                            <p>Tốc độ Đóng gói & Giao vận tối ưu</p>
                            <div class="progress">
                                <div class="progress-bar wow fadeInLeft" data-wow-duration="0.5s"
                                    data-wow-delay=".5s" role="progressbar" style="width: 92%">
                                    <span>92%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 align-self-center">
                <div class="about-img-right">
                    <img src="{{ asset('assets/clients/img/team/9.jpg') }}" alt="Đội ngũ chuyên gia">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection