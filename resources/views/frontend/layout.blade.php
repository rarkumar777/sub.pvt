<!DOCTYPE html>
<html dir="{{ strtolower($lang) == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ strtolower($lang) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <link rel="icon" href="{{ asset((isset($profile['fav_icon']) && $profile['fav_icon']) ? 'uploads/' . $profile['fav_icon'] : 'favpvt1.png') }}?v=1" />
    <title>@yield('title', 'Pv Travels')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
            }
        }
    }
    </script>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Legacy CSS (for existing components) -->
    <link href="{{ asset('assets/frontend/css/gogies.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/css/front.css') }}" rel="stylesheet">

    <base href="{{ url('/') }}/">
    <meta name="keywords" content="@yield('keywords', 'Jordan tours, Petra tours, travel Jordan')">
    <meta name="description" content="@yield('description', 'Pv Travels - Tour operator in Jordan')">

    <style>
    body { font-family: 'Inter', sans-serif !important; overflow-x: hidden; }
    .carousel ul, .slider ul { list-style: none; }

    /* === Gogies Grid - protect from Tailwind reset === */
    .body-wrap .row { display: table !important; width: 100% !important; clear: both !important; }
    .body-wrap .sd-12 { width: 100% !important; }
    .body-wrap .nopad { padding: 0 !important; }
    .body-wrap [class*="md-"], .body-wrap [class*="bd-"] {
        min-height: 2px; position: relative; box-sizing: border-box;
    }
    @media (min-width: 768px) {
        .body-wrap .md-12, .body-wrap .md-11, .body-wrap .md-10, .body-wrap .md-9, .body-wrap .md-8, .body-wrap .md-7, .body-wrap .md-6, .body-wrap .md-5, .body-wrap .md-4, .body-wrap .md-3, .body-wrap .md-2, .body-wrap .md-1 {
            float: left !important; display: block !important;
        }
        .body-wrap .md-12 { width: 100% !important; }
        .body-wrap .md-9 { width: 75% !important; }
        .body-wrap .md-8 { width: 66.66% !important; }
        .body-wrap .md-7 { width: 58.33% !important; }
        .body-wrap .md-6 { width: 50% !important; }
        .body-wrap .md-5 { width: 41.66% !important; }
        .body-wrap .md-4 { width: 33.33% !important; }
        .body-wrap .md-3 { width: 25% !important; }
    }
    @media (min-width: 992px) {
        .body-wrap .bd-12, .body-wrap .bd-8, .body-wrap .bd-6, .body-wrap .bd-4, .body-wrap .bd-3 {
            float: left !important; display: block !important;
        }
        .body-wrap .bd-12 { width: 100% !important; }
        .body-wrap .bd-8 { width: 66.66% !important; }
        .body-wrap .bd-6 { width: 50% !important; }
        .body-wrap .bd-4 { width: 33.33% !important; }
        .body-wrap .bd-3 { width: 25% !important; }
    }
    @media (max-width: 767px) {
        .body-wrap .md-4, .body-wrap .md-5, .body-wrap .md-6, .body-wrap .md-7 { width: 100% !important; float: none !important; }
    }
    /* Card equal height via flexbox on shadow-box only */
    .body-wrap .shadow-box { display: flex; flex-direction: column; width: 100%; }
    .body-wrap .shadow-box > .align-center:last-child { margin-top: auto; }
    .body-wrap .shadow-box img { width: 100%; height: 200px; object-fit: cover; }
    /* Text */
    .body-wrap .align-justify { text-align: justify !important; }
    .body-wrap .align-center { text-align: center !important; }
    .body-wrap img.full-width { width: 100% !important; height: auto !important; }
    .body-wrap .wrap { width: 100%; max-width: 1095px; margin: auto; position: relative; padding: 0 5px; }
    </style>

    @stack('styles')
    @stack('head')
</head>
<body class="bg-amber-50/30 antialiased" lang="{{ strtolower($lang) }}">

@include('frontend.partials.nav')

@yield('hero')

<div class="">
    @yield('content')
</div>

<div id="ajax"></div>

@include('frontend.partials.footer')

<script src="{{ asset('assets/frontend/js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/gogies.js') }}"></script>
<script>lucide.createIcons();</script>
@stack('scripts')
</body>
</html>
