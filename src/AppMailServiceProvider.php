<?php

namespace Willemo\LaravelAppMail;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
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
            __DIR__ . '/../config/config.php',
            'appmail'
        );

        app('swift.transport')->extend('appmail', function () {
            $config = $this->app['config']->get('appmail', []);

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
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('appmail.php'),
        ], 'config');
    }
}
