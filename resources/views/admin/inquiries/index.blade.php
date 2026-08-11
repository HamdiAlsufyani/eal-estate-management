<x-admin-layout title="{{ __('inquiries.title') }}" :breadcrumbs="[['label' => __('inquiries.title')]]">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('inquiries.title') }}</h1>
            <p class="text-sm text-text-muted">{{ __('inquiries.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <x-ui.card>
            @include('admin.inquiries.partials.filters')

            @include('admin.inquiries.partials.table', ['inquiries' => $inquiries])
        </x-ui.card>

        @if ($inquiries->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $inquiries->links() }}
            </x-ui.card>
        @endif
    </div>
</x-admin-layout>
