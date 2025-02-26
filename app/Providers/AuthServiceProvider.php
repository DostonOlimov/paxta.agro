<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\OrganizationCompanies;
use App\Policies\ApplicationPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Application::class => ApplicationPolicy::class,
        OrganizationCompanies::class => OrganizationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
