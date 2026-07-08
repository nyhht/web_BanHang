@extends('layouts.client')

@section('title', 'Liên hệ')

@section('breadcrumb', 'Liên hệ')

@section('content')
    <!-- CONTACT ADDRESS AREA START -->
    <div class="ltn__contact-address-area contact-page-address-area mb-90">
        <div class="container">
            <div class="row align-items-stretch">
                <div class="col-lg-4 col-md-6 mb-30">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset('assets/clients/img/icons/10.png') }}" alt="Icon Image">
                        </div>
                        <h3>Địa chỉ Email</h3>
                        <p>nguyenhieu27hsht@gmail.com</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-30">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset('assets/clients/img/icons/11.png') }}" alt="Icon Image">
                        </div>
                        <h3>Số điện thoại</h3>
                        <p>+0123-456789</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 mb-30">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset('assets/clients/img/icons/12.png') }}" alt="Icon Image">
                        </div>
                        <h3>Công ty</h3>
                        <p>Bình Minh, Hà Nội, Việt Nam</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTACT ADDRESS AREA END -->

    <!-- CONTACT MESSAGE AREA START -->
    <div class="ltn__contact-message-area contact-message-map-area mb-120">
        <div class="container">
            <div class="row align-items-stretch">
                <div class="col-lg-7 mb-30">
                    <div class="ltn__form-box contact-form-box contact-form-card box-shadow white-bg">
                        <h4 class="title-2">Liên hệ</h4>
                        <form id="contact-form" action="{{ route('contact') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-item input-item-name ltn__custom-icon">
                                        <input type="text" name="name" placeholder="Họ và tên" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-item input-item-phone ltn__custom-icon">
                                        <input type="text" name="phone" placeholder="Số điện thoại" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-item input-item-email ltn__custom-icon">
                                        <input type="email" name="email" placeholder="Địa chỉ email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="input-item input-item-textarea ltn__custom-icon">
                                <textarea name="message" placeholder="Nhập tin nhắn"></textarea>
                            </div>
                            <div class="btn-wrapper mt-0">
                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">Gửi</button>
                            </div>
                            <p class="form-messege mb-0 mt-20"></p>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 mb-30">
                    <div class="contact-map-card box-shadow white-bg">
                        <h4 class="title-2">Bản đồ cửa hàng</h4>
                        <div class="contact-map-frame">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29817.503892081324!2d105.74143586584937!3d20.904755215655968!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31344d30f4ee104b%3A0xb9687faf785c3687!2zQsOsbmggTWluaCwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1781024023057!5m2!1svi!2s"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTACT MESSAGE AREA END -->
@endsection
