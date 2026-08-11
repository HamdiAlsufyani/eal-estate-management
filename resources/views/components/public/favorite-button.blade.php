@props([
    'property',
    'favorited' => false,
    'size' => 'md',
])

@php
    $sizeClasses = $size === 'lg' ? 'h-11 w-11' : 'h-9 w-9';
    $iconClasses = $size === 'lg' ? 'h-5.5 w-5.5' : 'h-4.5 w-4.5';
@endphp

<button
    type="button"
    x-data="{ favorited: {{ $favorited ? 'true' : 'false' }}, loading: false }"
    @if (auth()->check())
        @click.stop.prevent="
            if (loading) return;
            loading = true;
            fetch('{{ route('properties.favorite', $property) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            }).then(r => r.json()).then(data => { favorited = data.favorited; loading = false; }).catch(() => { loading = false; })
        "
    @else
        @click.stop.prevent="window.location = '{{ route('login') }}'"
    @endif
    :class="favorited ? 'bg-danger text-white shadow-soft' : 'bg-white/90 text-text shadow-soft hover:bg-white'"
    {{ $attributes->class(["$sizeClasses inline-flex shrink-0 items-center justify-center rounded-full backdrop-blur transition-colors duration-150"]) }}
    :aria-pressed="favorited"
    aria-label="{{ $favorited ? __('properties.remove_from_favorites') : __('properties.add_to_favorites') }}"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :fill="favorited ? 'currentColor' : 'none'" class="{{ $iconClasses }}">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
    </svg>
</button>
