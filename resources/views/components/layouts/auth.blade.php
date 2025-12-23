<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Authentication' }} - {{ config('app.name', 'Laravel') }}</title>

    <x-head />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="h-full antialiased">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md mb-8">
            <a href="{{ route('dashboard') }}" class="flex justify-center">
                <img src="{{ asset('images/techdash-v2-logo-big.webp') }}" alt="{{ config('app.name') }}" class="h-6 w-auto">
            </a>
        </div>
        {{ $slot }}
    </div>
</body>
</html>

