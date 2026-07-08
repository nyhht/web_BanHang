@extends('layouts.client')

@section('title', 'Dịch vụ')
@section('breadcrumb', 'Dịch vụ')

@section('content')

    <div class="ltn__about-us-area pb-115">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 align-self-center">
                    <div class="about-us-img-wrap ltn__img-shape-left  about-img-left">
                        <img src="{{ asset('assets/clients/img/service/4.jpg') }}" alt="Image">
                    </div>
                </div>
                <div class="col-lg-7 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">// GIẢI PHÁP TIỆN LỢI</h6>
                            <h1 class="section-title">Dịch Vụ Cung Cấp Hộp Nguyên Liệu Nấu Ăn<span>.</span></h1>
                            <p>Chúng tôi cam kết đơn giản hóa công việc vào bếp của bạn bằng các hộp nguyên liệu sơ chế sẵn, giúp bạn nấu ngon như đầu bếp một cách nhanh chóng.</p>
                        </div>
                        <div class="about-us-info-wrap-inner about-us-info-devide">
                            <p>Hệ thống Meal-kit đồng hành cùng bữa ăn gia đình hiện đại. Bạn chỉ cần chọn thực đơn mong muốn trên website, mọi công đoạn đi chợ, nhặt rau, cân đo đong đếm gia vị và sơ chế thịt cá đều được đội ngũ chuyên nghiệp của chúng tôi hoàn thiện và đóng gói an toàn giao tận cửa.</p>
                            <div class="list-item-with-icon">
                                <ul>
                                    <li><a href="javascript:void(0)">Nguyên liệu định lượng chính xác theo khẩu phần</a></li>
                                    <li><a href="javascript:void(0)">Nước sốt pha chế sẵn kèm công thức chi tiết</a></li>
                                    <li><a href="javascript:void(0)">Giao hàng nhanh chóng, bảo quản thùng giữ nhiệt</a></li>
                                    <li><a href="javascript:void(0)">Thực đơn phong phú thay đổi liên tục theo tuần</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ltn__service-area section-bg-1 pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color---">Tiện Ích Hệ Thống Mang Lại</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/1.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Sơ chế hoàn chỉnh</a></h3>
                            <p>Thịt, cá, rau củ được làm sạch, cắt thái sẵn sàng. Bạn chỉ cần mở gói và nấu ngay lập tức.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/3.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Giao hàng siêu tốc</a></h3>
                            <p>Hệ thống vận chuyển tối ưu, đảm bảo hộp nguyên liệu luôn giữ được độ tươi ngon tuyệt đối.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Tính toán dinh dưỡng</a></h3>
                            <p>Mỗi set ăn đều được hiển thị đầy đủ lượng calo và thành phần, hỗ trợ kiểm soát chế độ ăn khoa học.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Tích hợp AI Gợi Ý</a></h3>
                            <p>Ứng dụng công nghệ thông minh, tự động gợi ý món ăn liên quan dựa trên từ khóa tìm kiếm của bạn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/1.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Đóng gói chuẩn sạch</a></h3>
                            <p>Quy trình đóng gói khép kín, sử dụng khay sinh học thân thiện với môi trường và giữ an toàn vệ sinh.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <a href="javascript:void(0)"><img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="#"></a>
                        </div>
                        <div class="service-item-brief">
                            <h3><a href="javascript:void(0)">Không lãng phí thực phẩm</a></h3>
                            <p>Định lượng chính xác theo số người ăn giúp loại bỏ hoàn toàn tình trạng thừa nguyên liệu dư thừa bỏ đi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ltn__our-journey-area bg-image bg-overlay-theme-90 pt-280 pb-350 mb-35 plr--9" data-bg="{{ asset('assets/clients/img/bg/8.jpg') }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__our-journey-wrap">
                        <ul>
                            <li><span class="ltn__journey-icon">Bước 1</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/5.jpg') }}" alt="Đặt hàng">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Lựa chọn thực đơn</h3>
                                                <p>Khách hàng truy cập website, duyệt qua danh mục món ngon gia đình hoặc healthy và tiến hành đặt hàng trực tuyến.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="active"><span class="ltn__journey-icon">Bước 2</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/4.jpg') }}" alt="Sơ chế">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Sơ chế & Định lượng</h3>
                                                <p>Hệ thống tiếp nhận đơn hàng, nhân viên tiến hành phân loại, cân đo định lượng và sơ chế nguyên liệu sạch sẽ.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">Bước 3</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/2.jpg') }}" alt="Đóng gói">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Đóng gói & Bảo quản</h3>
                                                <p>Nguyên liệu được đưa vào các khay hộp chuyên dụng, đi kèm gói nước sốt pha sẵn và hút chân không bảo quản.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li><span class="ltn__journey-icon">Bước 4</span>
                                <ul>
                                    <li>
                                        <div class="ltn__journey-history-item-info clearfix">
                                            <div class="ltn__journey-history-img">
                                                <img src="{{ asset('assets/clients/img/service/3.jpg') }}" alt="Giao hàng">
                                            </div>
                                            <div class="ltn__journey-history-info">
                                                <h3>Giao hàng tận cửa</h3>
                                                <p>Hộp Meal-kit hoàn chỉnh được giao đến tận nhà khách hàng một cách nhanh chóng, sẵn sàng kích hoạt trải nghiệm nấu nướng.</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection