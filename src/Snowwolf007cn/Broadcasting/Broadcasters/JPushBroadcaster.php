<?php

namespace Snowwolf007cn\Broadcasting\Broadcasters;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Support\Arr;
use JPush\Client;
use JPush\PushPayload;

/**
 * JPush braodcaster.
 */
class JPushBroadcaster extends Broadcaster
{
    /**
     * @var \JPush\Client
     */
    protected $client;

    /**
     * Construtor.
     *
     * @param \JPush\Client $client JPush client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function auth($request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validAuthenticationResponse($request, $result)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $cid = Arr::get($payload, 'cid');
        $platform = Arr::get($payload, 'platform', 'all');
        $audience = Arr::get($payload, 'audience', 'all');
        $push = $this->client->push();
        $push->setPlatform($platform);
        $push = $this->processAudience($audience, $push);
        if (null !== $cid) {
            $push->setCid($cid);
        }
        $push->send();
    }

    /**
     * Process 'audience' part of payload.
     *
     * @param array|string       $payload refer to audience part of JPush REST API
     * @param \JPush\PushPayload $push    current push
     */
    protected function processAudience($payload, PushPayload $push)
    {
        if ('all' === $payload) {
            $push->addAllAudience();
        } else {
            if (!empty($tag = Arr::get($payload, 'tag'))) {
                $push->addTag($tag);
            }
            if (!empty($tagAnd = Arr::get($payload, 'tag_and'))) {
                $push->addTagAnd($tagAnd);
            }
            if (!empty($tagNot = Arr::get($payload, 'tag_not'))) {
                $push->addTagNot($tagNot);
            }
            if (!empty($alias = Arr::get($payload, 'alias'))) {
                $push->addAlias($alias);
            }
            if (!empty($registrationId = Arr::get($payload, 'registration_id'))) {
                $push->addRegistrationId($registrationId);
            }
            if (!empty($segmentId = Arr::get($payload, 'segment'))) {
                $push->addSegmentId($segmentId);
            }
            if (!empty($abTest = Arr::get($payload, 'abtest'))) {
                $push->addAbtest($abTest);
            }
        }

        return $push;
    }

    /**
     * Process 'notification' part of payload.
     *
     * @param array              $payload refer to audience part of JPush REST API
     * @param \JPush\PushPayload $push    current push
     */
    protected function processNotification(array $payload, PushPayload $push)
    {
        $alert = Arr::get($payload, 'alert');
        if (!empty($alert)) {
            $push->setNotificationAlert($alert);
        }
        $iosNotification = Arr::get($payload, 'ios');
        if (!empty($iosNotification)) {
            $iosAlert = Arr::get($iosNotification, 'alert', '');
            $push->iosNotification($iosAlert, $iosNotification);
        }
        $androidNotification = Arr::get($payload, 'android');
        if (!empty($androidNotification)) {
            $iosAlert = Arr::get($androidNotification, 'alert', '');
            $push->iosNotification($iosAlert, $androidNotification);
        }

        return $push;
    }
}
