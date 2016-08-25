<?php

namespace Exfriend\Overseer;

use Illuminate\Support\ServiceProvider;

class OverseerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ( !$this->app->routesAreCached() )
        {
            require __DIR__ . '/routes.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
