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
        Gate::policy(Activity::class, ActivityPolicy::class);
        User::observe(UserObserver::class);
        DocumentType::observe(DocumentTypeObserver::class);
    }
}
