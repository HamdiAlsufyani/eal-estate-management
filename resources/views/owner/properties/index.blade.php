<x-owner-layout title="My Properties" :breadcrumbs="[['label' => 'My Properties']]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">My Properties</h1>
                <p class="text-sm text-text-muted">Manage your property listings.</p>
            </div>

            @can('create', \App\Models\Property::class)
                <x-ui.button :href="route('owner.properties.create')" variant="primary" class="hidden sm:inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Property
                </x-ui.button>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger" dismissible>{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card>
            @include('owner.properties.partials.filters')

            @include('owner.properties.partials.table', ['properties' => $properties])
        </x-ui.card>

        @if ($properties->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $properties->links() }}
            </x-ui.card>
        @endif
    </div>
</x-owner-layout>
