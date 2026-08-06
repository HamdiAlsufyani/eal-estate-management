@props(['iconClass' => 'h-9 w-9', 'textClass' => 'text-white'])

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <x-application-logo class="{{ $iconClass }} shrink-0 text-secondary" />
    <span class="flex flex-col leading-none">
        <span class="text-[15px] font-semibold tracking-tight {{ $textClass }}">{{ config('app.name') }}</span>
        <span class="text-[11px] font-medium uppercase tracking-wider {{ $textClass }}/50">Real Estate Suite</span>
    </span>
</span>
