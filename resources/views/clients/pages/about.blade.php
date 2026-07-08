@extends('layouts.client')

@section('title', 'Giới thiệu')
@section('breadcrumb', 'Giới thiệu')

@section('content')    
 <!-- ABOUT US AREA START -->
 <div class="ltn__about-us-area pt-120--- pb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 align-self-center">
                <div class="about-us-img-wrap about-img-left">
                    <img src="{{ asset('assets/clients/img/others/7.png') }}" alt="About Us Image">
                </div>
            </div>
            <div class="col-lg-6 align-self-center">
                <div class="about-us-info-wrap">
                    <div class="section-title-area ltn__section-title-2">
                        <h6 class="section-subtitle ltn__secondary-color">Giải Pháp Nấu Nướng Hiện Đại</h6>
                        <h1 class="section-title"> <br class="d-none d-md-block"> Meal-Kit Tiện Lợi</h1>
                            <p>Chúng tôi mang đến giải pháp nấu ăn thông minh với các hộp nguyên liệu tươi ngon được định lượng và sơ chế sẵn, kèm công thức chuẩn vị</p>
                    </div>
                    <p>Chào mừng bạn đến với nền tảng cung cấp Meal-kit, nơi biến việc vào bếp mỗi ngày trở nên thư giãn và dễ dàng hơn bao giờ hết.
                         Không còn nỗi lo "hôm nay ăn gì" hay mất hàng giờ đi chợ, nhặt rau và dọn dẹp, 
                         chúng tôi giúp bạn chuẩn bị một bữa ăn gia đình trọn vẹn chỉ trong 15-30 phút nấu nướng.</p>
                    <div class="about-author-info d-flex">
                        <div class="author-name-designation  align-self-center">
                            <h4 class="mb-0">HieuNguyen</h4>
                                <small>Người Sáng Lập Hệ Thống</small>
                        </div>
                        <div class="author-sign">
                            <img src="{{ asset('assets/clients/img/icons/icon-img/author-sign.png') }}" alt="#">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ABOUT US AREA END -->

<!-- FEATURE AREA START ( Feature - 6) -->
<div class="ltn__feature-area section-bg-1 pt-115 pb-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h6 class="section-subtitle ltn__secondary-color">Ưu điểm vượt trội</h6>
                    <h1 class="section-title">Tại Sao Nên Chọn Meal-Kit Của Chúng Tôi<span></span></h1>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="ltn__feature-item ltn__feature-item-7">
                    <div class="ltn__feature-icon-title">
                        <div class="ltn__feature-icon">
                            <span><img src="{{asset('assets/clients/img/icons/icon-img/21.png')}}" alt="#"></span>
                        </div>
                        <h3><a href="service-details.html">Sơ Chế & Định Lượng Sẵn</a></h3>
                    </div>
                    <div class="ltn__feature-info">
                        <p>Nguyên liệu được cắt thái, làm sạch và chia chính xác theo khẩu phần, giúp bạn tiết kiệm tối đa thời gian chuẩn bị và hạn chế thực phẩm thừa.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="ltn__feature-item ltn__feature-item-7">
                    <div class="ltn__feature-icon-title">
                        <div class="ltn__feature-icon">
                            <span><img src="{{asset('assets/clients/img/icons/icon-img/22.png')}}" alt="#"></span>
                        </div>
                        <h3><a href="service-details.html">Thực Đơn Đa Dạng & Khoa Học</a></h3>
                    </div>
                    <div class="ltn__feature-info">
                        <p>Cập nhật liên tục các món ăn truyền thống lẫn hiện đại, từ cơm nhà, lẩu nướng đến chế độ Eat Clean được tính toán kỹ lưỡng hàm lượng dinh dưỡng.</p>

                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="ltn__feature-item ltn__feature-item-7">
                    <div class="ltn__feature-icon-title">
                        <div class="ltn__feature-icon">
                            <span><img src="{{asset('assets/clients/img/icons/icon-img/23.png')}}" alt="#"></span>
                        </div>
                        <h3><a href="service-details.html">Nước Sốt Đóng Gói Chuẩn Vị</a></h3>
                    </div>
                    <div class="ltn__feature-info">
                        <p>Chúng tôi cung cấp các loại nước sốt đóng gói với hương vị chuẩn, giúp bạn dễ dàng chế biến món ăn ngon miệng chỉ trong vài phút.</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FEATURE AREA END -->

@endsection