<x-public-layout :title="__('messages.error_403_title')">
    <div class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-secondary-hover">403</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-text sm:text-4xl">{{ __('messages.error_403_title') }}</h1>
        <p class="mt-3 text-text-muted">
            {{ __('messages.error_403_message') }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button :href="route('home')">{{ __('messages.go_home') }}</x-ui.button>
        </div>
    </div>
</x-public-layout>
