<?php

namespace Snowwolf007cn\Broadcasting\Broadcasters;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use JPush\Client;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;
use JPush\Exceptions\ServiceNotAvaliable;
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
        try {
            $push = $this->client->push();

            try {
                $cid = Arr::get($payload, 'cid', Arr::get(($push->getCid()), 'body.cdlist.0'));
            } catch (\Exception $e) {
                $e;
            }
            $platform = Arr::get($payload, 'platform', 'all');
            $push->setPlatform($platform);

            $audience = [];
            $formatChannels = $this->formatChannels($channels);
            if (in_array('all', $formatChannels, true)) {
                $audience = 'all';
            } else {
                foreach ($formatChannels as $channel) {
                    list($type, $value) = explode('.', $channel);
                    if (!array_has($type, $audience)) {
                        $audience[$type] = [];
                    }
                    $audience[$type][] = $value;
                }
            }
            $push = $this->processAudience($audience, $push);

            $notification = Arr::get($payload, 'notification');
            if (!empty($notification)) {
                $push = $this->processNotification($notification, $push);
            }

            $message = Arr::get($payload, 'message');
            if (!empty($message)) {
                $messageContent = Arr::pull($message, 'msg_content');
                if (is_string($messageContent)) {
                    $push->message($messageContent, $message);
                }
            }

            $options = Arr::get($payload, 'options');
            if (null !== $options) {
                $push->options($options);
            }
            if (null !== $cid) {
                $push->setCid($cid);
            }

            $push->send();
        } catch (APIRequestException $e) {
            Log::error($e->getMessage());
            throw $e;
        } catch (APIConnectionException | ServiceNotAvaliable $e) {
            Log::error($e->getMessage());
            throw $e;
        }
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
            $androidAlert = Arr::get($androidNotification, 'alert', '');
            $push->androidNotification($androidAlert, $androidNotification);
        }

        return $push;
    }
}
