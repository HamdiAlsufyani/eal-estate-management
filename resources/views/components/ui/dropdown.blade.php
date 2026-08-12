@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:left-0 rtl:right-0',
        'top' => 'origin-top',
        default => 'ltr:right-0 rtl:left-0',
    };

    $widthClasses = match ((string) $width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        '80' => 'w-80',
        '96' => 'w-96',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="dropdown-panel {{ $widthClasses }} {{ $alignmentClasses }}"
        style="display: none;"
        @click="open = false"
    >
        {{ $content }}
    </div>
</div>
