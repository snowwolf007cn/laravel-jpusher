# laravel-jpusher

JPush integration to laravel framework

[![Latest Stable Version](https://poser.pugx.org/snowwolf007cn/laravel-jpusher/v/stable)](https://packagist.org/packages/snowwolf007cn/laravel-jpusher)
[![Total Downloads](https://poser.pugx.org/snowwolf007cn/laravel-jpusher/downloads)](https://packagist.org/packages/snowwolf007cn/laravel-jpusher)
[![Latest Unstable Version](https://poser.pugx.org/snowwolf007cn/laravel-jpusher/v/unstable)](https://packagist.org/packages/snowwolf007cn/laravel-jpusher)
[![License](https://poser.pugx.org/snowwolf007cn/laravel-jpusher/license)](https://packagist.org/packages/snowwolf007cn/laravel-jpusher)

## Table of Contents

-   <a href="#installation">Installation</a>
    -   <a href="#composer">Composer</a>
    -   <a href="#laravel">Laravel</a>
-   <a href="#usage">braodcaster</a>
    - <a href="#config-jpush-broadcaster">Config JPush Broadcasterl</a>
    - <a href="#set-audience">Set Audience</a>
    - <a href="#add-payload">Add Payload</a>

## Installation

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require snowwolf007cn/laravel-jpusher
```

### Laravel

#### >= laravel5.5

ServiceProvider will be attached automatically

## Broadcaster

### Config JPush Broadcaster

Add following code into your **config/broadcasting.php** under **'connection'**

```php
'jpush' => [
    'driver' => 'jpush',
    'app_key' => env('JPUSH_APP_KEY'),
    'master_secret' => env('JPUSH_MASTER_SECRET'),
],
```

fill your app key and secret in **.env**

### Set Audience

Audiences are mapped to channels in laravel, and you can config channels like this.

```php
Broadcast::channel('all', function ($user, $id) {
    return true;
});

Broadcast::channel('tag.{tag_name}', function ($user, $id) {
    return true;
});
```

examples above set two channels for diffrent audience configuration, **All** and **Tag**

### Add Payload

Platforms, Notifications, Message and Options are send to broadcaster as payload in an array, which will be serialized in json and send to server.Platforms are default to 'all', i.e.

```php
/**
 * Add payload to broadcast.
 */
public function broadcastWith()
{
    return [
        'message' => [
            'msg_content' => 'Hi,JPush',
            'content_type' => 'text',
            'title' => 'msg',
        ],
    ];
}
```

Read more on how to [broadcasting event](https://laravel.com/docs/5.5/broadcasting) and [JPush API](https://docs.jiguang.cn/jpush/server/push/server_overview/)
