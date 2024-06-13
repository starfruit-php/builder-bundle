<?php

namespace Starfruit\BuilderBundle\Sitemaps;

use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Model\Option;

class Setting
{
    const OPTION_NAME = 'sitemap_setting';

    private static function getOption(): Option
    {
        $option = Option::getOrCreate(self::OPTION_NAME);
        return $option;
    }

    public static function setSitemap(array $keys)
    {
        $option = self::getOption();
        $content = json_encode($keys);
        $option->setContent($content);
        $option->save();
    }

    public static function getSitemapKeys()
    {
        $option = self::getOption();
        $keys = empty($option) ? [] : json_decode($option->getContent(), true);

        return $keys;
    }

    public static function getSitemap()
    {
        $keys = self::getSitemapKeys();
        $classNames = ObjectConfig::getListClassName();

        if (empty($classNames)) {
            return [];
        }

        $data = [];
        foreach ($classNames as $className) {
            $key = strtolower($className);
            $check = in_array($key, $keys);

            $data[] = [
                'key' => $key,
                'name' => $className,
                'check' => $check,
            ];
        }

        return $data;
    }
}
