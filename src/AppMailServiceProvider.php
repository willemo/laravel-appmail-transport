<?php

namespace Willemo\LaravelAppMail;

use Willemo\LaravelAppMail\AppMailTransport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use AppMail\Client;

class AppMailServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('swift.transport')->extend('appmail', function ($app) {
            $apiKey = $app['config']->get('laravel-appmail.api_key');

            return new AppMailTransport(new Client($apiKey));
        });
    }

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        $configPath = __DIR__ . '/../config/laravel-appmail.php';

        $this->publishes([
            $configPath => config_path('laravel-appmail.php')
        ], 'config');

        $this->mergeConfigFrom($configPath, 'laravel-appmail');
    }
}
