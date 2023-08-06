<!doctype html>
<html lang="vi">
<head>
    <title>{{ $title_page ?? config('layouts.title_page_default') }}</title>
    @include('partials.meta')
    <link rel="icon" type="image/png" href="https://checkoto.vn/assets/storage/images/favicon_WC8.png" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('css')
</head>
<body>
<div class="relative bg-gray-50 overflow-hidden">
    @include('partials.fade')

    <div class="relative pt-6 pb-16 sm:pb-24">
        <div>
            @include('partials.header')
            @include('partials.header-mobile')
        </div>

        <main class="mt-16 mx-auto max-w-7xl px-4 sm:mt-24">
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('js')
</body>
</html>
