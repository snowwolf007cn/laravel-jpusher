<?php

namespace Snowwolf007cn\Broadcasting;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\ServiceProvider;
use JPush\Client;
use Snowwolf007cn\Broadcasting\Broadcasters\JPushBroadcaster;

/**
 * JPush Service Provider.
 */
class JPushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        app(BroadcastManager::class)->extend('jpush', function ($app, $config) {
            $client = new Client(
                config('broadcasting.connections.jpush.app_key'),
                config('broadcasting.connections.jpush.master_secret')
            );

            return new JPushBroadcaster($client);
        });
    }
}
