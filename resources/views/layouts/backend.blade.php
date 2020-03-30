<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>@yield('page-title')</title>

        <meta name="description" content="OneUI - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
        <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">

        <!-- Fonts and Styles -->
        @yield('css_before')
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" id="css-main" href="{{ asset('css/oneui.css') }}">

        <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="{{ asset('css/themes/amethyst.css') }}"> -->
        <!-- Select2 -->
        <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>
        @yield('css_after')

        <!-- Scripts -->
{{--        <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>--}}
    </head>
    <body>

        <div id="page-container" class="sidebar-o enable-page-overlay sidebar-dark side-scroll page-header-fixed">
            <!-- Side Overlay-->
{{--            @include('layouts.layouts-files.right-sidebar')--}}
            <!-- END Side Overlay -->

            @include('layouts.layouts-files.left-sidebar')
            <!-- END Sidebar -->

            <!-- Header -->
            @include('layouts.layouts-files.header')
            <!-- END Header -->

            <!-- Main Container -->
            <main id="main-container">
                @yield('content-header')
                <div class="section">
                    @yield('content')
                </div>
            </main>
            <!-- END Main Container -->

            <!-- Footer -->
            @include('layouts.layouts-files.footer')
            <!-- END Footer -->
        </div>
        <!-- END Page Container -->

        <script !src="">
            APP_URL = '{{ URL::to('/') }}';
            ajaxRequests = 0;
            detailClick = false;
        </script>
        <!-- OneUI Core JS -->
        <script src="{{ asset('js/oneui.app.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>
        <script src={{ asset("/add_ons/select2/js/i18n/fr.js") }}></script>
        <!-- Laravel Scaffolding JS -->
        <script src="{{ asset('js/laravel.app.js') }}"></script>

        @yield('js_after')
    </body>
</html>
