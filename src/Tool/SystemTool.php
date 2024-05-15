<?php

namespace Starfruit\BuilderBundle\Tool;

use Symfony\Component\HttpFoundation\Request;

class SystemTool
{
    public static function getDomain()
    {
        return Request::createFromGlobals()->getSchemeAndHttpHost();
    }

    public static function getUrl($path)
    {
        return preg_match('/^http(s)?:\\/\\/.+/', $path) ? $path : self::getDomain() . $path;
    }
}
