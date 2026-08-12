@php
    $menu = [
        [
            'label' => __('navigation.dashboard'),
            'route' => 'customer.dashboard',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />',
        ],
        [
            'label' => __('navigation.browse_properties'),
            'route' => 'properties.index',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        ],
        [
            'label' => __('navigation.my_favorites'),
            'route' => 'favorites.index',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />',
        ],
        [
            'label' => __('navigation.my_inquiries'),
            'route' => 'customer.inquiries.index',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />',
        ],
        [
            'label' => __('navigation.recently_viewed'),
            'route' => 'customer.recently-viewed',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        ],
        [
            'label' => __('messages.notifications'),
            'route' => 'notifications.index',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />',
        ],
        [
            'label' => __('navigation.profile'),
            'route' => 'profile.edit',
            'permission' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />',
        ],
    ];
@endphp

<aside
    class="sidebar-panel"
    :class="sidebarOpen ? 'is-open' : 'is-closed'"
>
    <div class="flex h-16 shrink-0 items-center border-b border-white/10 px-5">
        <a href="{{ route('customer.dashboard') }}">
            <x-brand-logo />
        </a>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
        @foreach ($menu as $item)
            @php
                $canView = is_null($item['permission']) || auth()->user()?->can($item['permission']);
                $href = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#';
                $active = \Illuminate\Support\Facades\Route::has($item['route']) && request()->routeIs($item['route'] . '*');
            @endphp

            @if ($canView)
                <a href="{{ $href }}" class="sidebar-link {{ $active ? 'is-active' : '' }}">
                    <span class="sidebar-link-indicator h-1.5 w-1.5 shrink-0 rounded-full bg-transparent"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="truncate">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-full">
                <span class="sidebar-link-indicator h-1.5 w-1.5 shrink-0 rounded-full bg-transparent"></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                <span class="truncate">{{ __('navigation.logout') }}</span>
            </button>
        </form>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="rounded-[var(--radius-control)] bg-white/5 px-3.5 py-3 text-xs text-white/50">
            <p class="font-medium text-white/70">{{ config('app.name') }}</p>
            <p class="mt-0.5">{{ __('navigation.customer_portal') }}</p>
        </div>
    </div>
</aside>

<div
    x-show="sidebarOpen"
    x-transition.opacity
    class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
    @click="sidebarOpen = false"
    style="display: none;"
></div>
