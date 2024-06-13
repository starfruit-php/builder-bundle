<?php

namespace Starfruit\BuilderBundle\Service;

use Pimcore\Db;
use Starfruit\BuilderBundle\Model\Option;

class DatabaseService
{
    const BUILDER_SEO_TABLE = 'builder_seo';
    const BUILDER_OPTIONS_TABLE = 'builder_options';

    public static function createTables()
    {
        self::createBuilderSeo();
        self::createBuilderOptions();
    }

    public static function updateTables()
    {
        self::createTables();
        self::updateBuilderSeo();
    }

    public static function createBuilderSeo()
    {
        $query = "CREATE TABLE IF NOT EXISTS " . self::BUILDER_SEO_TABLE . " (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `elementType` varchar(255) NOT NULL,
            `element` int(11) NOT NULL,
            `title` varchar(255) DEFAULT NULL,
            `description` varchar(255) DEFAULT NULL,
            `keyword` varchar(255) DEFAULT NULL,
            `language` varchar(10) DEFAULT NULL,
            `indexing` tinyint(1) DEFAULT 0,
            `nofollow` tinyint(1) DEFAULT 0,
            `canonicalUrl` varchar(255) DEFAULT NULL,
            `redirectLink` tinyint(1) DEFAULT 0,
            `redirectType` varchar(190) DEFAULT NULL,
            `destinationUrl` varchar(255) DEFAULT NULL,
            `schemaBlock` longtext DEFAULT NULL,
            `image` varchar(255) DEFAULT NULL,
            `imageAsset` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        Db::get()->executeQuery($query);
    }

    public static function createBuilderOptions()
    {
        $query = "
        CREATE TABLE IF NOT EXISTS " . self::BUILDER_OPTIONS_TABLE . " (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `content` longtext DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ALTER TABLE `builder_options` ADD PRIMARY KEY (`name`);
        COMMIT;";

        Db::get()->executeQuery($query);
    }

    public static function updateBuilderSeo()
    {
        $query = "ALTER TABLE `builder_seo`
            ADD COLUMN IF NOT EXISTS `indexing` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `nofollow` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `canonicalUrl` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `redirectLink` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `redirectType` varchar(190) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `destinationUrl` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `schemaBlock` longtext DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `image` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `imageAsset` int(11) DEFAULT NULL
        ";

        Db::get()->executeQuery($query);
    }
}
