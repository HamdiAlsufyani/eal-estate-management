@php
    $user = auth()->user();
    $isAdminArea = $user->hasRole('Admin') || $user->hasRole('Staff');
    $isOwner = $user->hasRole('Owner');
@endphp

@if ($isAdminArea)
    <x-admin-layout :title="__('notifications.title')" :breadcrumbs="[['label' => __('notifications.title')]]">
        @include('notifications.partials.content')
    </x-admin-layout>
@elseif ($isOwner)
    <x-owner-layout :title="__('notifications.title')" :breadcrumbs="[['label' => __('notifications.title')]]">
        @include('notifications.partials.content')
    </x-owner-layout>
@else
    <x-public-layout :title="__('notifications.title')">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            @include('notifications.partials.content')
        </div>
    </x-public-layout>
@endif
