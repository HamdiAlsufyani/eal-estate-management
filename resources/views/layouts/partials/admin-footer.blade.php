<footer class="border-t border-border bg-surface px-4 py-4 sm:px-6">
    <div class="flex flex-col items-center justify-between gap-2 text-xs text-text-muted sm:flex-row">
        <p>&copy; {{ now()->year }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}</p>
        <p>{{ __('messages.version') }} 1.0.0</p>
    </div>
</footer>
