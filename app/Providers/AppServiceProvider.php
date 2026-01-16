<?php

namespace App\Providers;

use App\Mixins\ResponseMixin;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class  AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        ResponseFactory::mixin(new ResponseMixin());
        $this->app->singleton(\App\Services\MenuService::class);
    }
}
