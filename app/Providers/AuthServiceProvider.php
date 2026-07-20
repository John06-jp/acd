<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', fn (User $user) => $user->role === 'admin');

        Gate::define('isAdminDeveloper', fn (User $user) => $user->role === 'admindeveloper');

        Gate::define('isAdminOrDeveloper', fn (User $user) => in_array($user->role, ['admin', 'admindeveloper'], true));

        Gate::define('isStaff', fn (User $user) => $user->role === 'staff');

        Gate::define('isAdminOrStaff', fn (User $user) => in_array($user->role, ['admin', 'staff'])
        );

        Gate::define('isStudent', fn (User $user) => in_array($user->role, ['student', 'faculty']) // treat faculty same
        );
    }
}
