<?php

namespace Willemo\LaravelAppMail;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Willemo\LaravelAppMail\AppMailTransport;
use GuzzleHttp\Client as HttpClient;

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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-appmail.php',
            'laravel-appmail'
        );

        app('swift.transport')->extend('appmail', function () {
            $config = $this->app['config']->get('laravel-appmail', []);

            $guzzleConfig = Arr::get($config, 'guzzle', []);

            return new AppMailTransport(
                new HttpClient(Arr::add($guzzleConfig, 'connect_timeout', 60)),
                $config['api_key'],
                $config['api_host'],
                $config['api_version']
            );
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
            $configPath => config_path('laravel-appmail.php'),
        ], 'config');
    }
}
