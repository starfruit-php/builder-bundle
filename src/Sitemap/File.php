<?php

namespace Starfruit\BuilderBundle\Sitemap;

use Starfruit\BuilderBundle\Tool\SystemTool;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Seo\SeoHelper;

class File
{
    public static function generateXMLFile($content, $section): void
    {
        $location = self::getLocation($section);
        file_put_contents($location, TextTool::getStringAsOneLine($content));
    }

    protected static function getLocation($section = null)
    {
        $filename = self::getFileName($section);
        return PIMCORE_PROJECT_ROOT . '/public' . $filename;
    }

    protected static function getFileName($section = null)
    {
        return '/sitemap' . ($section ? '.' . $section : '') . '.xml';
    }
}
