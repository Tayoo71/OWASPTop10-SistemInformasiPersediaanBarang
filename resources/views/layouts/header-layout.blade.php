<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="robots" content="noindex,nofollow,noarchive">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('images/logo/favicon.png') }}">
        @vite(['resources/css/app.css', 'resources/css/inter.css', 'resources/js/app.js'])
        <title>{{ $title }} - Aplikasi Persediaan Toko X</title>
    </head>

    <body class="h-full">
        {{ $slot }}
        @stack('scripts')
    </body>

</html>
