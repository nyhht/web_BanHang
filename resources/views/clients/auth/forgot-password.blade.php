@extends('layouts.client')

@section('title', 'Quên mật khẩu')

@section('breadcrumb', 'Quên mật khẩu')

@section('content')
    <div class="container pb-70">
        <h2>Quên mật khẩu</h2>
        <div class="ltn__myaccount-tab-content-inner">
            <div class="ltn__form-box">
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="email" name="email" placeholder="Nhập email của bạn" required>
                                @error('email')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                    <div class="btn-wrapper">
                        <button type="submit" class="theme-btn-1 btn black-btn text-uppercase">Gửi liên kết đặt lại mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
