<x-public-layout :title="__('messages.error_500_title')">
    <div class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-secondary-hover">500</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-text sm:text-4xl">{{ __('messages.error_500_title') }}</h1>
        <p class="mt-3 text-text-muted">
            {{ __('messages.error_500_message') }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button :href="route('home')">{{ __('messages.go_home') }}</x-ui.button>
        </div>
    </div>
</x-public-layout>
