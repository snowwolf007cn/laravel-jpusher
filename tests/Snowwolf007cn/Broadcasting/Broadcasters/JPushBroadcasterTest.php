<?php

namespace Test\Snowwolf007cn\Broadcasting\Broadcasters;

use Illuminate\Broadcasting\BroadcastManager;
use JPush\Client;
use Snowwolf007cn\Broadcasting\Broadcasters\JPushBroadcaster;
use Test\TestCase;

/**
 * JPush broadcaster test.
 */
class JPushBroadcasterTest extends TestCase
{
    /**
     * Test broadcast by jpush.
     */
    public function testBroadcast()
    {
        $app = $this->createApplication();
        app(BroadcastManager::class)->extend('jpush', function ($app, $config) {
            $client = new Client(
                config('broadcasting.connections.jpush.app_key'),
                config('broadcasting.connections.jpush.master_secret')
            );

            return new JPushBroadcaster($client);
        });

        $connection = $app->make(BroadcastManager::class)->connection('jpush');
        $this->assertInstanceOf(JPushBroadcaster::class, $connection);
    }
}
