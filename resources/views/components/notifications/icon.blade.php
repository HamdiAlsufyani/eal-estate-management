@props(['type' => 'system'])

@php
    [$bg, $text, $path] = match ($type) {
        'property_submitted' => ['bg-warning-soft', 'text-warning-hover', 'M3 9.75 12 4l9 5.75M4.5 10.5V19.5A1.5 1.5 0 0 0 6 21h3.75v-6h4.5v6H18a1.5 1.5 0 0 0 1.5-1.5V10.5'],
        'property_approved' => ['bg-success-soft', 'text-success-hover', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'property_rejected' => ['bg-danger-soft', 'text-danger-hover', 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'inquiry_created', 'inquiry_status_updated' => ['bg-info-soft', 'text-info-hover', 'M8.25 10.5h7.5m-7.5 3h4.5m8.25-1.5c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z'],
        'user_registered' => ['bg-primary-soft', 'text-primary', 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
        default => ['bg-primary-soft', 'text-primary', 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0'],
    };
@endphp

<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $bg }} {{ $text }}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4.5 w-4.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
    </svg>
</span>
