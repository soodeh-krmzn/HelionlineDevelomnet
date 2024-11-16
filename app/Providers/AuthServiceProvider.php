<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('unlimited', function($user) {
            return $user->access == 1;
        });
        Gate::define('create', function($user) {
            return ($user->access == 1 || $user->group?->hasPermission('create'));
        });
        Gate::define('update', function($user) {
            return ($user->access == 1 || $user->group?->hasPermission('update'));
        });
        Gate::define('delete', function($user) {
            return ($user->access == 1 || $user->group?->hasPermission('delete'));
        });
    }
}
