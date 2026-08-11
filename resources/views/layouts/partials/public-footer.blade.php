<footer class="border-t border-border bg-primary text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <x-brand-logo icon-class="h-9 w-9" text-class="text-white" />
                <p class="mt-4 max-w-xs text-sm text-white/70">
                    {{ __('messages.footer_tagline') }}
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white/50">{{ __('messages.explore') }}</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-white/80">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-secondary">{{ __('navigation.home') }}</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'sale']) }}" class="transition-colors hover:text-secondary">{{ __('messages.properties_for_sale') }}</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'rent']) }}" class="transition-colors hover:text-secondary">{{ __('messages.properties_for_rent') }}</a></li>
                    <li><a href="{{ route('properties.index') }}" class="transition-colors hover:text-secondary">{{ __('navigation.all_properties') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white/50">{{ __('messages.account') }}</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-white/80">
                    @guest
                        <li><a href="{{ route('login') }}" class="transition-colors hover:text-secondary">{{ __('navigation.login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="transition-colors hover:text-secondary">{{ __('navigation.register') }}</a></li>
                    @else
                        <li><a href="{{ route('dashboard') }}" class="transition-colors hover:text-secondary">{{ __('navigation.my_dashboard') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="transition-colors hover:text-secondary">{{ __('navigation.profile') }}</a></li>
                    @endguest
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white/50">{{ __('navigation.contact') }}</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-white/80">
                    <li>{{ __('messages.riyadh_saudi_arabia') }}</li>
                    <li>info@{{ strtolower(config('app.name')) }}.com</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row">
            <p>&copy; {{ now()->year }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}</p>
            <p>{{ __('messages.footer_experience_tagline') }}</p>
        </div>
    </div>
</footer>
