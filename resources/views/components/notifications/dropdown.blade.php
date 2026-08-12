<x-ui.dropdown align="right" width="96">
    <x-slot name="trigger">
        <button type="button" class="btn-icon relative" aria-label="{{ __('messages.notifications') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            @if ($unreadCount > 0)
                <span class="absolute -top-0.5 end-1 flex h-4.5 min-w-[1.125rem] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-surface">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="flex items-center justify-between border-b border-border px-4 py-3">
            <span class="text-sm font-semibold text-text">{{ __('notifications.title') }}</span>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-primary hover:underline">{{ __('notifications.mark_all_read') }}</button>
                </form>
            @endif
        </div>

        <div class="max-h-96 divide-y divide-border overflow-y-auto">
            @forelse ($notifications as $notification)
                <x-notifications.item :notification="$notification" />
            @empty
                <x-ui.empty-state :title="__('notifications.empty_title')" class="px-6 py-10" />
            @endforelse
        </div>

        <div class="border-t border-border px-4 py-2.5 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-primary hover:underline">{{ __('notifications.view_all') }}</a>
        </div>
    </x-slot>
</x-ui.dropdown>
