<x-public-layout :title="__('messages.error_429_title')">
    <div class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-secondary-hover">429</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-text sm:text-4xl">{{ __('messages.error_429_title') }}</h1>
        <p class="mt-3 text-text-muted">
            {{ __('messages.error_429_message') }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button :href="route('home')">{{ __('messages.go_home') }}</x-ui.button>
        </div>
    </div>
</x-public-layout>
