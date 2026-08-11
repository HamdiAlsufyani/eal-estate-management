<x-public-layout :title="__('messages.error_419_title')">
    <div class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-secondary-hover">419</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-text sm:text-4xl">{{ __('messages.error_419_title') }}</h1>
        <p class="mt-3 text-text-muted">
            {{ __('messages.error_419_message') }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button :href="route('home')">{{ __('messages.go_home') }}</x-ui.button>
        </div>
    </div>
</x-public-layout>
