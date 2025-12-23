<!DOCTYPE html>
<html lang="en">
<head>
    {{-- ================= META ================= --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', 'Dashboard') | Admin</title>

    <meta name="description" content="Admin Dashboard">
    <meta name="author" content="Admin">

    {{-- ================= FAVICON ================= --}}
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">

    {{-- ================= GOOGLE FONT ================= --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
        id="main-font-link"
    >

    {{-- ================= ICON FONTS ================= --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- ================= TEMPLATE CSS ================= --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">

    {{-- ================= EXTRA CSS ================= --}}
    @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">

    {{-- ================= PRELOADER ================= --}}
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    {{-- ================= SIDEBAR ================= --}}
    @include('layouts.partials.sidebar')

    {{-- ================= NAVBAR ================= --}}
    @include('layouts.partials.navbar')

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="pc-container">
        <div class="pc-content">

            {{-- OPTIONAL PAGE HEADER --}}
            @hasSection('page-title')
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">@yield('page-title')</h5>
                                </div>
                                @hasSection('breadcrumb')
                                    <ul class="breadcrumb">
                                        @yield('breadcrumb')
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- PAGE CONTENT --}}
            @yield('content')

        </div>
    </div>

    {{-- ================= FOOTER ================= --}}
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p class="m-0">
                        Admin Dashboard © {{ date('Y') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    {{-- ================= JS PLUGINS ================= --}}
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    {{-- ================= TEMPLATE JS ================= --}}
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>

    {{-- ================= PAGE SPECIFIC JS ================= --}}
    @stack('scripts')

    {{-- ================= DEFAULT THEME CONFIG ================= --}}
    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change('preset-1');
        font_change('Public-Sans');
    </script>

</body>
</html>
