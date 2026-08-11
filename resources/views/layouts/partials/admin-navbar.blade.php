<header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b border-border bg-surface/80 px-4 backdrop-blur sm:px-6">
    {{-- Mobile sidebar toggle --}}
    <button type="button" class="btn-icon lg:hidden" @click="sidebarOpen = true" aria-label="{{ __('messages.open_sidebar') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5.5 w-5.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
    </button>

    <div class="min-w-0 flex-1">
        @isset($header)
            {{ $header }}
        @endisset
    </div>

    <div class="flex shrink-0 items-center gap-2">
        <x-language-switcher />

        <button type="button" class="btn-icon" aria-label="{{ __('messages.notifications') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </button>

        <div class="mx-1 hidden h-6 w-px bg-border sm:block"></div>

        <x-ui.dropdown align="right" width="56">
            <x-slot name="trigger">
                <button type="button" class="flex items-center gap-2.5 rounded-[var(--radius-control)] py-1.5 ps-1.5 pe-3 transition-colors duration-150 hover:bg-black/5">
                    <x-ui.avatar :user="auth()->user()" size="sm" />
                    <span class="hidden text-start sm:block">
                        <span class="block text-sm font-medium leading-tight text-text">{{ auth()->user()->name }}</span>
                        <span class="block text-xs leading-tight text-text-muted">{{ auth()->user()->getRoleNames()->first() ?? __('users.member') }}</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="hidden h-4 w-4 text-text-subtle sm:block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
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
    </div>
</header>
