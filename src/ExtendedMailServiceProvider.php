<?php

namespace Willemo\LaravelAppMailTransport;

use Illuminate\Support\Arr;
use Illuminate\Mail\MailServiceProvider;
use GuzzleHttp\Client as HttpClient;

class ExtendedMailServiceProvider extends MailServiceProvider
{
    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        parent::registerSwiftTransport();

        app('swift.transport')->extend('appmail', function ($app) {
            $config = $app['config']->get('services.appmail', []);

            $guzzleConfig = Arr::get($config, 'guzzle', []);

            return new AppMailTransport(
                new HttpClient(Arr::add($guzzleConfig, 'connect_timeout', 60)),
                $config['key']
            );
        });
    }
}
