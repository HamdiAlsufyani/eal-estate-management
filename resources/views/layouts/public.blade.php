<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' | ' : '' }}{{ config('app.name', 'Al Saad Realty') }}</title>

    @php
        $description = $metaDescription ?? 'Browse apartments, villas, offices and land for sale or rent with Al Saad Realty — a trusted, modern real estate platform.';
    @endphp
    <meta name="description" content="{{ $description }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|tajawal:400,500,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-background {{ app()->getLocale() === 'ar' ? 'font-arabic' : 'font-sans' }} text-text antialiased">
    @include('layouts.partials.public-header')

    <main class="flex-1">
        @if (session('success'))
            <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('layouts.partials.public-footer')

    @stack('scripts')
</body>
</html>
