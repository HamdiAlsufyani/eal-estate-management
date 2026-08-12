<div class="space-y-6">
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
    @endif

    <x-ui.card :title="__('notifications.title')">
        @if ($notifications->getCollection()->contains(fn ($notification) => is_null($notification->read_at)))
            <x-slot name="actions">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-primary hover:underline">{{ __('notifications.mark_all_read') }}</button>
                </form>
            </x-slot>
        @endif

        <div class="-mx-6 -my-6 divide-y divide-border">
            @forelse ($notifications as $notification)
                <x-notifications.item :notification="$notification" />
            @empty
                <x-ui.empty-state :title="__('notifications.empty_title')" />
            @endforelse
        </div>
    </x-ui.card>

    @if ($notifications->hasPages())
        <x-ui.card class="!shadow-none">
            {{ $notifications->links() }}
        </x-ui.card>
    @endif
</div>
