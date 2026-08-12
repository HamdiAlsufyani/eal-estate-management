<?php

namespace App\Providers;

use App\Policies\NotificationPolicy;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Event listeners in app/Listeners are auto-discovered by their handle()
     * type-hint (Laravel 11+ convention already used elsewhere in this app),
     * so they are not registered explicitly here.
     */
    public function boot(): void
    {
        Gate::policy(DatabaseNotification::class, NotificationPolicy::class);
    }
}
