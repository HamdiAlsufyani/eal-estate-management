<x-public-layout title="Page Not Found">
    <div class="mx-auto flex max-w-2xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-wider text-secondary-hover">404</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-text sm:text-4xl">Property Not Found</h1>
        <p class="mt-3 text-text-muted">
            The page or property you're looking for doesn't exist, is no longer available, or hasn't been approved yet.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button :href="route('properties.index')">Browse Properties</x-ui.button>
            <x-ui.button :href="route('home')" variant="outline">Back to Home</x-ui.button>
        </div>
    </div>
</x-public-layout>
