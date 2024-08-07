<?php

namespace Starfruit\BuilderBundle\Sitemap;

use Pimcore\Db;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Tool\TimeTool;
use Starfruit\BuilderBundle\Service\DatabaseService;

class Generator extends File
{
    const PAGE_SECTION_NAME = 'default';
    const LASTMOD_FORMAT = 'Y-m-d\TH:i:sP'; # 2024-06-14T09:11:05+07:00
    const LASTMOD_SQL_FORMAT = '%Y-%m-%dT%H:%i:%s'; # 2024-06-14T09:11:05+07:00

    public static function populate(?string $section = null): void
    {
        if ($section) {
            if ($section == self::PAGE_SECTION_NAME) {
                self::populateWithPage();
            } else {
                self::populateWithObject($section);
            }
        } else {
            self::populateWithPage();
            self::populateWithObject();
        }
    }
    
    public static function populateIndex(): void
    {
        $sitemapKeys = Setting::getSitemapKeys();
        $sections = array_merge([self::PAGE_SECTION_NAME], $sitemapKeys);
        
        $slugs = [];
        foreach ($sections as $section) {
            $slugs[] = [
                'loc' => self::getFileName($section),
                'lastmod' => TimeTool::unixtime2string(filemtime(self::getLocation($section)), self::LASTMOD_FORMAT),
            ];
        }

        $keys = array_column($slugs, 'lastmod');
        array_multisort($keys, Setting::isOrderDesc() ? SORT_DESC : SORT_ASC, $slugs);

        $sitemapType = new Type\Sitemapindex();
        $sitemapType->replaceThenPushContent($slugs);
    }

    private static function populateWithPage()
    {
        $section = self::PAGE_SECTION_NAME;
        $slugs = self::getSlugs($section);

        $sitemapType = new Type\Urlset();
        $sitemapType->replaceThenPushContent($slugs, $section);
    }

    private static function populateWithObject($section = null)
    {
        $classnames = ObjectConfig::getListClassName();
        $classnames = $section ? [$section] : $classnames;

        foreach ($classnames as $class) {
            $section = strtolower($class);
            $slugs = self::getSlugs($class);

            $sitemapType = new Type\Urlset();
            $sitemapType->replaceThenPushContent($slugs, $section);
        }
    }

    protected static function getSlugs(string $sectionOrClassname, ?int $id = null): ?array
    {
        if ($sectionOrClassname == self::PAGE_SECTION_NAME) {
            $slugs = self::getPageSlug($id);
        } else {
            $slugs = self::getSlugsByClassname($sectionOrClassname, $id);
        }

        return $slugs;
    }

    private static function getPageSlug(?int $id = null)
    {
        $timezone = TimeTool::getTimezone();
        $sitemapOrder = Setting::getSitemapOrder();
        if (!$id) {
            $query = "SELECT
                    CASE
                        WHEN PAGES.`prettyUrl` IS NOT NULL
                        THEN PAGES.`prettyUrl`
                        ELSE CONCAT(DOCUMENTS.`path`, DOCUMENTS.`key`)
                        END
                    AS loc,
                    CONCAT(FROM_UNIXTIME(DOCUMENTS.`modificationDate`, ?), ?) as lastmod,
                    DOCUMENTS.`id` as id
                FROM `documents` as DOCUMENTS
                INNER JOIN `documents_page` as PAGES
                INNER JOIN " . DatabaseService::BUILDER_SEO_TABLE . " as SEO
                WHERE DOCUMENTS.`type` = 'page'
                AND DOCUMENTS.`published` = 1
                AND DOCUMENTS.`id` = PAGES.`id`
                AND DOCUMENTS.`id` = SEO.`element`
                AND SEO.`elementType` = 'document'
                AND SEO.`generateSitemap` = 1
                ORDER BY DOCUMENTS.`modificationDate` " . $sitemapOrder;
            $params = [self::LASTMOD_SQL_FORMAT, $timezone];
        } else {
            $query = "SELECT
                    CASE
                        WHEN PAGES.`prettyUrl` IS NOT NULL
                        THEN PAGES.`prettyUrl`
                        ELSE CONCAT(DOCUMENTS.`path`, DOCUMENTS.`key`)
                        END
                    AS loc,
                    CONCAT(FROM_UNIXTIME(DOCUMENTS.`modificationDate`, ?), ?) as lastmod,
                    DOCUMENTS.`id` as id
                FROM `documents` as DOCUMENTS
                INNER JOIN `documents_page` as PAGES
                INNER JOIN " . DatabaseService::BUILDER_SEO_TABLE . " as SEO
                WHERE DOCUMENTS.`type` = 'page'
                AND DOCUMENTS.`published` = 1
                AND DOCUMENTS.`id` = PAGES.`id`
                AND DOCUMENTS.`id` = SEO.`element`
                AND SEO.`elementType` = 'document'
                AND SEO.`generateSitemap` = 1
                AND DOCUMENTS.`id` = ?
                ORDER BY DOCUMENTS.`modificationDate` " . $sitemapOrder;
            $params = [self::LASTMOD_SQL_FORMAT, $timezone, $id];
        }

        $slugs = Db::get()->fetchAllAssociative($query, $params);
        return $slugs;
    }

    private static function getSlugsByClassname(string $classname, ?int $id = null)
    {
        $timezone = TimeTool::getTimezone();
        $sitemapOrder = Setting::getSitemapOrder();
        if (!$id) {
            $query = "SELECT
                    SLUG.`slug` as loc,
                    CONCAT(FROM_UNIXTIME(OBJECTS.`modificationDate`, ?), ?) as lastmod,
                    SLUG.`objectId` as id
                FROM `object_url_slugs` as SLUG
                INNER JOIN `classes` as CLASSES
                INNER JOIN `objects` as OBJECTS
                WHERE SLUG.`ownertype` = 'localizedfield'
                AND SLUG.`classId` = CLASSES.`id`
                AND CLASSES.`name` = ?
                AND SLUG.`objectId` = OBJECTS.`id`
                AND OBJECTS.`published` = 1
                ORDER BY OBJECTS.`modificationDate` " . $sitemapOrder;

            $params = [self::LASTMOD_SQL_FORMAT, $timezone, $classname];
        } else {
            $query = "SELECT
                    SLUG.`slug` as loc,
                    CONCAT(FROM_UNIXTIME(OBJECTS.`modificationDate`, ?), ?) as lastmod,
                    SLUG.`objectId` as id
                FROM `object_url_slugs` as SLUG
                INNER JOIN `classes` as CLASSES
                INNER JOIN `objects` as OBJECTS
                WHERE SLUG.`ownertype` = 'localizedfield'
                AND SLUG.`objectId` = ?
                AND SLUG.`classId` = CLASSES.`id`
                AND CLASSES.`name` = ?
                AND SLUG.`objectId` = OBJECTS.`id`
                AND OBJECTS.`published` = 1
                ORDER BY OBJECTS.`modificationDate` " . $sitemapOrder;

            $params = [self::LASTMOD_SQL_FORMAT, $timezone, $id, $classname];
        }

        $slugs = Db::get()->fetchAllAssociative($query, $params);
        return $slugs;
    }
}
