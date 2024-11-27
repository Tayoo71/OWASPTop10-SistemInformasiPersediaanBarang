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
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>

    <body class="h-full" x-data="{ loading: true }" x-init="Promise.all([
        document.fonts.ready,
        new Promise(resolve => window.addEventListener('load', resolve))
    ]).then(() => loading = false);">

        <div x-show="loading" class="fixed inset-0 z-[9990] flex items-center justify-center bg-white">
            <div class="w-16 h-16 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
        </div>

        <div x-show="!loading" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="h-full" x-bind:class="{ 'hidden': loading, 'block': !loading }"
            x-cloak>
            {{ $slot }}
        </div>

        @stack('scripts')
        <noscript>
            <div class="fixed inset-0 z-[9991] flex items-center justify-center bg-white">
                <p class="text-lg font-bold text-gray-700">
                    JavaScript is disabled in your browser. Please enable JavaScript to view this page.
                </p>
            </div>
        </noscript>
    </body>

</html>
