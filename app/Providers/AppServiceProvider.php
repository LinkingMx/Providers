<?php

namespace App\Providers;

use App\Models\DocumentType;
use App\Models\User;
use App\Observers\DocumentTypeObserver;
use App\Observers\UserObserver;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

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
     */
    public function boot(): void
    {
        // Register Activity policy for Filament Logger Shield integration
        Gate::policy(Activity::class, ActivityPolicy::class);

        // Override Filament's password reset notification
        $this->app->bind(
            \Filament\Notifications\Auth\ResetPassword::class,
            \App\Notifications\CustomResetPasswordNotification::class
        );

        // Manually register Filament auth components to fix Livewire registration issue
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component(
                'filament.pages.auth.password-reset.request-password-reset',
                \Filament\Pages\Auth\PasswordReset\RequestPasswordReset::class
            );
            
            \Livewire\Livewire::component(
                'filament.pages.auth.password-reset.reset-password',
                \Filament\Pages\Auth\PasswordReset\ResetPassword::class
            );
        }
    }
}
