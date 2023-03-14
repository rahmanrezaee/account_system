<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isManager', function ($user){
            return $user->user_level === 1;
        });
        Gate::define('isAdmin', function ($user){
            return $user->user_level === 2;
        });
        Gate::define('isUser', function ($user){
            return $user->user_level === 3;
        });

        //
    }
}
