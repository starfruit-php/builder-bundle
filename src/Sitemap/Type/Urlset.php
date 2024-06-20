<?php

namespace Starfruit\BuilderBundle\Sitemap\Type;

use Starfruit\BuilderBundle\Seo\SeoHelper;

class Urlset extends BaseType
{
    const TAG = 'url';

    public function getTag(): string
    {
        return self::TAG;
    }

    public static function getStruct()
    {
        $replace = self::REPLACE;

        $struct = '<?xml version="1.0" encoding="UTF-8"?>';
        $struct .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $replace . '</urlset>';

        return $struct;
    }

    public function getReplaceFromXmlContent($xmlContent)
    {
        $pattern = '/<urlset[^>]*>(.+?)<\/urlset>/i';
        return SeoHelper::getAllValues($pattern, $xmlContent, 1);
    }
}
