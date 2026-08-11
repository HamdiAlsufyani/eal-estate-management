@php
    $navLinks = [
        ['label' => __('navigation.home'), 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => __('navigation.buy'), 'url' => route('properties.index', ['purpose' => 'sale']), 'active' => request()->routeIs('properties.*') && request('purpose') === 'sale'],
        ['label' => __('navigation.rent'), 'url' => route('properties.index', ['purpose' => 'rent']), 'active' => request()->routeIs('properties.*') && request('purpose') === 'rent'],
        ['label' => __('navigation.all_properties'), 'url' => route('properties.index'), 'active' => request()->routeIs('properties.*') && ! request('purpose')],
    ];
@endphp

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-30 border-b border-border bg-surface/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="shrink-0">
            <x-brand-logo icon-class="h-9 w-9" text-class="text-text" />
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($navLinks as $link)
                <a
                    href="{{ $link['url'] }}"
                    class="rounded-[var(--radius-control)] px-3.5 py-2 text-sm font-medium transition-colors duration-150 {{ $link['active'] ? 'bg-primary/10 text-primary' : 'text-text-muted hover:bg-black/5 hover:text-text' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <x-language-switcher />

            <a href="{{ route('properties.index') }}" class="btn-icon hidden sm:inline-flex" aria-label="{{ __('messages.search_properties') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </a>

            @auth
                <x-ui.dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center gap-2 rounded-[var(--radius-control)] py-1.5 ps-1.5 pe-2.5 transition-colors duration-150 hover:bg-black/5">
                            <x-ui.avatar :user="auth()->user()" size="sm" />
                            <span class="hidden text-sm font-medium text-text sm:block">{{ auth()->user()->name }}</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-ui.dropdown-item :href="route('dashboard')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            {{ __('My Dashboard') }}
                        </x-ui.dropdown-item>

                        <x-ui.dropdown-item :href="route('favorites.index')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            {{ __('My Favorites') }}
                        </x-ui.dropdown-item>

                        <x-ui.dropdown-item :href="route('profile.edit')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            {{ __('Profile') }}
                        </x-ui.dropdown-item>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.dropdown-item class="text-danger hover:!text-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                {{ __('Log Out') }}
                            </x-ui.dropdown-item>
                        </form>
                    </x-slot>
                </x-ui.dropdown>
            @else
                <a href="{{ route('login') }}" class="hidden text-sm font-medium text-text-muted transition-colors hover:text-primary sm:block">{{ __('navigation.login') }}</a>
                <x-ui.button :href="route('register')" size="sm">{{ __('navigation.register') }}</x-ui.button>
            @endauth

            <button type="button" class="btn-icon lg:hidden" @click="mobileOpen = ! mobileOpen" aria-label="{{ __('messages.open_menu') }}">
                <svg x-show="! mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5.5 w-5.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
                <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5.5 w-5.5" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile nav --}}
    <div
        x-show="mobileOpen"
        x-transition
        style="display: none;"
        class="border-t border-border bg-surface lg:hidden"
    >
        <nav class="flex flex-col gap-1 px-4 py-3">
            @foreach ($navLinks as $link)
                <a
                    href="{{ $link['url'] }}"
                    class="rounded-[var(--radius-control)] px-3.5 py-2.5 text-sm font-medium {{ $link['active'] ? 'bg-primary/10 text-primary' : 'text-text-muted hover:bg-black/5 hover:text-text' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach

            @guest
                <div class="mt-2 flex items-center gap-2 border-t border-border pt-3">
                    <x-ui.button :href="route('login')" variant="outline" class="flex-1 justify-center">{{ __('navigation.login') }}</x-ui.button>
                    <x-ui.button :href="route('register')" class="flex-1 justify-center">{{ __('navigation.register') }}</x-ui.button>
                </div>
            @endguest
        </nav>
    </div>
</header>
