<?php

namespace Starfruit\BuilderBundle\Tool;

use Symfony\Component\HttpFoundation\Request;
use Starfruit\BuilderBundle\Model\Option;

class SystemTool
{
    public static function getRequest()
    {
        return Request::createFromGlobals();
    }

    public static function getCurrentUrl()
    {
        return self::getDomain() . self::getRequest()->getPathInfo();
    }

    public static function getDomain()
    {
        return Option::getMainDomain() ?: self::getRequest()->getSchemeAndHttpHost();
    }

    public static function getHost()
    {
        $domain = self::getDomain();
        if ($domain) {
            $hosts = parse_url($domain);
            if (isset($hosts['host']) && $hosts['host']) {
                return $hosts['host'];
            }
        }

        return self::getRequest()->getHost();
    }

    public static function getUrl($path)
    {
        return preg_match('/^http(s)?:\\/\\/.+/', $path) ? $path : self::getDomain() . $path;
    }
}
