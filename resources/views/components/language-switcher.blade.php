@php
    $currentUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
@endphp

<x-ui.dropdown align="right" width="48">
    <x-slot name="trigger">
        <button type="button" class="btn-icon" aria-label="{{ __('messages.change_language') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 0 0 0-18m0 18a9 9 0 0 1 0-18m0 18a8.997 8.997 0 0 0 7.843-4.582M12 21a8.997 8.997 0 0 1-7.843-4.582m15.686 0A8.997 8.997 0 0 0 12 3a8.997 8.997 0 0 0-7.843 13.418m15.686 0H3.157" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <a
            href="{{ route('language.switch', ['locale' => 'en', 'redirect' => $currentUrl]) }}"
            class="dropdown-item {{ app()->getLocale() === 'en' ? 'font-semibold text-primary' : '' }}"
        >
            English
        </a>
        <a
            href="{{ route('language.switch', ['locale' => 'ar', 'redirect' => $currentUrl]) }}"
            class="dropdown-item {{ app()->getLocale() === 'ar' ? 'font-semibold text-primary' : '' }}"
        >
            العربية
        </a>
    </x-slot>
</x-ui.dropdown>
