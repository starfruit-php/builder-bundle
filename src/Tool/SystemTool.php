<?php

namespace Starfruit\BuilderBundle\Tool;

use Symfony\Component\HttpFoundation\Request;

class SystemTool
{
    public static function getRequest()
    {
        return Request::createFromGlobals();
    }

    public static function getCurrentUrl()
    {
        $request = self::getRequest();
        return $request->getSchemeAndHttpHost() . $request->getPathInfo();
    }

    public static function getDomain()
    {
        return self::getRequest()->getSchemeAndHttpHost();
    }

    public static function getUrl($path)
    {
        return preg_match('/^http(s)?:\\/\\/.+/', $path) ? $path : self::getDomain() . $path;
    }
}
