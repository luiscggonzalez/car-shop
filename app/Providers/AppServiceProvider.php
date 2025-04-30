<?php

namespace App\Providers;

use CarShop\Backoffice\Users\Domain\UserRepository;
use CarShop\Backoffice\Users\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use CarShop\Shared\Domain\Bus\Event\EventBus;
use CarShop\Shared\Infrastructure\Bus\Event\InMemory\InMemorySymfonyEventBus;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // event bus
        $this->app->singleton(EventBus::class, function ($app) {
            $subscribers = $app->tagged('event.subscribers');
            return new InMemorySymfonyEventBus($subscribers);
        });

        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super-Admin')) {
                return true;
            }

            return null;
        });
    }
}
