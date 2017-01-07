<?php

namespace Mnabialek\LaravelHandyRequest\Providers;

use Illuminate\Support\ServiceProvider;
use Mnabialek\LaravelHandyRequest\HandyRequest;

class HandyRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving(function (HandyRequest $request, $app) {
            $request->initializeFromRequest($app['request'])->setContainer($app);
        });
    }
}
