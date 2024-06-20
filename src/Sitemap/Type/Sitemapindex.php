<?php

namespace Starfruit\BuilderBundle\Sitemap\Type;

class Sitemapindex extends BaseType
{
    const TAG = 'sitemap';

    public function getTag(): string
    {
        return self::TAG;
    }

    public static function getStruct()
    {
        $replace = self::REPLACE;

        $struct = '<?xml version="1.0" encoding="UTF-8"?>';
        $struct .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $struct .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' .
                   ' http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"';
        $struct .= ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $replace . '</sitemapindex>';

        return $struct;
    }
}
