<?php

namespace Starfruit\BuilderBundle\Sitemap;

use Pimcore\Model\Document\Page;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Model\Option;
use Starfruit\BuilderBundle\Service\DatabaseService;
use Starfruit\BuilderBundle\Model\Seo;

class Setting
{
    const OPTION_NAME = 'sitemap_setting';
    const OPTION_ORDER_NAME = 'sitemap_setting_order';
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    private static function getOptionKeys(): Option
    {
        $option = Option::getOrCreate(self::OPTION_NAME);
        return $option;
    }

    private static function getOptionOrder(): Option
    {
        $option = Option::getOrCreate(self::OPTION_ORDER_NAME);
        if (!$option->getContent()) {
            $option->setContent(self::ORDER_DESC);
            $option->save();
        }
        return $option;
    }

    public static function getSitemapKeys(): ?array
    {
        $option = self::getOptionKeys();
        $keys = empty($option) ? [] : json_decode($option->getContent(), true);

        return empty($keys) ? [] : $keys;
    }

    public static function getSitemapOrder(): string
    {
        $option = self::getOptionOrder();
        return $option->getContent();
    }

    public static function isOrderDesc(): bool
    {
        return self::getSitemapOrder() == self::ORDER_DESC;
    }

    public static function getOrder(): array
    {
        $currentOrder = self::getSitemapOrder();
        $data = [
            [
                'key' => self::ORDER_DESC,
                'name' => self::ORDER_DESC,
                'check' => self::ORDER_DESC == $currentOrder,
            ],
            [
                'key' => self::ORDER_ASC,
                'name' => self::ORDER_ASC,
                'check' => self::ORDER_ASC == $currentOrder,
            ]
        ];

        return $data;
    }

    public static function setOrder(string $order): void
    {
        if (in_array($order, [self::ORDER_DESC, self::ORDER_ASC])) {
            $option = self::getOptionOrder();
            $option->setContent($order);
            $option->save();
        }
    }

    public static function setKeys(array $keys): void
    {
        $option = self::getOptionKeys();
        $content = json_encode($keys);
        $option->setContent($content);
        $option->save();
    }

    public static function getKeys(): ?array
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

    public static function getPages(): ?array
    {
        $pages = new \Pimcore\Model\Document\Listing();
        $pages->setCondition("type = 'page'");
        $pages->setOrderKey('key');
        $pages->setOrder('asc');

        $data = [];
        foreach ($pages as $page) {
            $seo = Seo::getOrCreate($page);

            $data[] = [
                'id' => $page->getId(),
                'key' => $page->getKey() ?: "Home",
                'language' => $page->getProperty('language'),
                'generateSitemap' => $seo->getGenerateSitemap(),
            ];
        }

        array_multisort(
            array_column($data, 'language'), SORT_ASC,
            array_column($data, 'key'), SORT_ASC,
            $data
        );

        return $data;
    }

    public static function setPages(array $ids): void
    {
        $pages = new \Pimcore\Model\Document\Listing();
        $pages->setCondition("type = 'page'");

        foreach ($pages as $page) {
            $seo = Seo::getOrCreate($page);
            $generateSitemap = empty($ids) ? false : in_array($page->getId(), $ids);
            $seo->setGenerateSitemap($generateSitemap);
            $seo->save();
        }
    }
}
