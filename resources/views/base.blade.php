<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @if(file_exists(public_path('img/icon.png')))
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/png">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
    @stack('head-scripts')
</head>
<body>
    @include('partials.header')
    @yield('content')
    @stack('scripts')
</body>
</html> 