<?php

namespace Starfruit\BuilderBundle\Tool;

use DateTime;
use onesignal\client\api\DefaultApi;
use onesignal\client\Configuration;
use onesignal\client\model\Notification;
use onesignal\client\model\StringMap;
use GuzzleHttp;

class NotificationTool
{
    public static function init()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setAppKeyToken($_ENV['ONESIGNAL_APP_AUTH_KEY'])
            ->setUserKeyToken($_ENV['ONESIGNAL_AUTH_KEY']);

        $apiInstance = new DefaultApi(
            new GuzzleHttp\Client(),
            $config
        );

        return $apiInstance;
    }

    public static function push($notification)
    {
        $apiInstance = self::init();

        try {
            $result = $apiInstance->createNotification($notification);
        } catch (Exception $e) {
        }
    }

    public static function pushPlayers(array $playerIds, $content): void
    {
        $target = compact('playerIds');
        $notification = self::createNotification($content, $target);
        self::push($notification);
    }

    public static function pushSegments(array $segments, $content): void
    {
        $target = compact('segments');
        $notification = self::createNotification($content, $target);
        self::push($notification);
    }

    public static function createNotification($content, array $target): Notification
    {
        $contents = new StringMap();
        $contents->setEn($content);

        $notification = new Notification();
        $notification->setAppId($_ENV['ONESIGNAL_APP_ID']);
        $notification->setContents($contents);

        $group = 'playerIds';
        if (isset($target[$group]) && is_array($target[$group]) && !empty($target[$group])) {
            $notification->setIncludePlayerIds($target[$group]);
        }

        $group = 'segments';
        if (isset($target[$group]) && is_array($target[$group]) && !empty($target[$group])) {
            $notification->setIncludedSegments($target[$group]);
        }

        return $notification;
    }
}
