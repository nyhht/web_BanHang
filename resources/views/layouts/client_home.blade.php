<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Place favicon.png in the root directory -->
    <link rel="shortcut icon" href="{{ asset('assets/clients/img/favicon.png') }}" type="image/x-icon" />
    <!-- Font Icons css -->
    <link rel="stylesheet" href="{{ asset('assets/clients/css/font-icons.css') }}">
    <!-- plugins css -->
    <link rel="stylesheet" href="{{ asset('assets/clients/css/plugins.css') }}">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/clients/css/style.css') }}">
    <!-- Responsive css -->
    <link rel="stylesheet" href="{{ asset('assets/clients/css/responsive.css') }}">

    <!-- Import CSS for Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    {{-- Import custom css  --}}
    <link rel="stylesheet" href="{{ asset('assets/clients/css/custom.css') }}?v={{ filemtime(public_path('assets/clients/css/custom.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/clients/css/chat.css') }}">

</head>

<body>
    <div class="body-wrapper">
        @include('clients.partials.header_home')
        <main>
            @yield('content')
        </main>

        @include('clients.partials.footer_home')
    </div>

    <!-- preloader area start -->
    <div class="preloader d-none" id="preloader">
        <div class="preloader-inner">
            <div class="spinner">
                <div class="dot1"></div>
                <div class="dot2"></div>
            </div>
        </div>
    </div>
    <!-- preloader area end -->

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- All JS Plugins -->
    <script src="{{ asset('assets/clients/js/plugins.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assets/clients/js/main.js') }}"></script>
    {{-- jquery-toast  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- JavaScript --}}
    <script src="{{ asset('assets/clients/js/custom.js') }}?v={{ filemtime(public_path('assets/clients/js/custom.js')) }}"></script>
    <script src="{{ asset('assets/clients/js/chat.js') }}?v={{ filemtime(public_path('assets/clients/js/chat.js')) }}"></script>
    @if (Request::is('checkout'))
        <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=USD"></script>
    @endif

    @include('partials.logout-confirm')
    @include('clients.partials.chat_ai')
    @include('clients.partials.tawk_to')

</body>

</html>
