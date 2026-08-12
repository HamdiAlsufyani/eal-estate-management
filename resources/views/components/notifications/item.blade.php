@props(['notification'])

@php
    $data = $notification->data;
    $isUnread = is_null($notification->read_at);
@endphp

<div class="group relative flex items-stretch {{ $isUnread ? 'bg-primary-soft/40' : '' }}">
    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="min-w-0 flex-1">
        @csrf
        <button type="submit" class="flex w-full items-start gap-3 px-4 py-3 text-start transition-colors duration-150 hover:bg-black/5">
            <x-notifications.icon :type="$data['type'] ?? 'system'" />

            <span class="min-w-0 flex-1">
                <span class="flex items-center gap-2">
                    <span class="truncate text-sm font-medium text-text">{{ $data['title'] ?? '' }}</span>
                    @if ($isUnread)
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-primary" aria-hidden="true"></span>
                    @endif
                </span>
                @if (! empty($data['message']))
                    <span class="mt-0.5 block text-xs text-text-muted line-clamp-2">{{ $data['message'] }}</span>
                @endif
                <span class="mt-1 block text-[11px] text-text-subtle">{{ $notification->created_at->diffForHumans() }}</span>
            </span>
        </button>
    </form>

    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="flex shrink-0 items-start pe-3 pt-3">
        @csrf
        @method('DELETE')
        <button
            type="submit"
            class="btn-icon h-7 w-7 opacity-0 transition-opacity duration-150 group-hover:opacity-100 focus-visible:opacity-100"
            aria-label="{{ __('messages.delete') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>
    </form>
</div>
