@props(['type' => 'spinner', 'rows' => 4, 'label' => 'Loading...'])

@if ($type === 'skeleton')
    <div {{ $attributes->class(['space-y-3']) }}>
        @for ($i = 0; $i < $rows; $i++)
            <div class="skeleton h-10 w-full"></div>
        @endfor
    </div>
@else
    <div {{ $attributes->class(['flex items-center justify-center gap-3 py-10 text-text-muted']) }}>
        <svg class="h-5 w-5 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
        </svg>
        <span class="text-sm font-medium">{{ $label }}</span>
    </div>
@endif
