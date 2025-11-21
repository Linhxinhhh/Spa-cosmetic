<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
            Gate::define('index',    fn($user) => $user->hasAnyRole(['admin','staff']));
            Gate::define('edit',    fn($user) => $user->hasAnyRole(['admin']));
            Gate::define('update',  fn($user) => $user->hasAnyRole(['admin']));
            Gate::define('create',  fn($user) => $user->hasAnyRole(['admin'])); // chỉ admin
            Gate::define('store',   fn($user) => $user->hasAnyRole(['admin']));
            Gate::define('destroy', fn($user) => $user->hasAnyRole(['admin']));
            }
}
