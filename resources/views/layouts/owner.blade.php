<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Al Saad Realty') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|tajawal:400,500,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ app()->getLocale() === 'ar' ? 'font-arabic' : 'font-sans' }} antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-background">
        @include('layouts.partials.owner-sidebar')

        <div class="flex min-w-0 flex-1 flex-col">
            @include('layouts.partials.admin-navbar')

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @isset($breadcrumbs)
                    <div class="mb-4">
                        <x-ui.breadcrumb :items="$breadcrumbs" />
                    </div>
                @endisset

                {{ $slot }}
            </main>

            @include('layouts.partials.admin-footer')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
