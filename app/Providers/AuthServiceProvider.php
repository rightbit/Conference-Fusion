<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function(User $user) {
            return $user->hasPermission('admin');
        });

        Gate::define('view_admin', function(User $user) {
            return $user->hasPermission('view_admin');
        });

        Gate::define('edit_conference', function(User $user) {
            return $user->hasPermission('edit_conference');
        });

        Gate::define('edit_schedules', function(User $user) {
            return $user->hasPermission('edit_schedules');
        });

        Gate::define('edit_sessions', function(User $user) {
            return $user->hasPermission('edit_sessions');
        });

        Gate::define('edit_users', function(User $user, ?int $profile_id = null) {
            if($user->id === $profile_id){
                return true;
            }
            return $user->hasPermission('edit_users');
        });
    }
}
