<?php

namespace App\Providers;

use App\Models\DocumentType;
use App\Models\User;
use App\Observers\DocumentTypeObserver;
use App\Observers\UserObserver;
use App\Policies\ActivityPolicy;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Update `Activity::class` with the one defined in `config/activitylog.php`
        Activity::class => ActivityPolicy::class,
    ];

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
        User::observe(UserObserver::class);
        DocumentType::observe(DocumentTypeObserver::class);
    }
}
