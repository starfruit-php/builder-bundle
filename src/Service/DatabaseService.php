<?php

namespace Starfruit\BuilderBundle\Service;

use Pimcore\Db;

class DatabaseService
{
    public static function createBuilderSeo()
    {
        $query = "CREATE TABLE IF NOT EXISTS `builder_seo` (
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
            `imageAsset` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        Db::get()->executeQuery($query);
    }

    public static function updateBuilderSeo()
    {
        self::createBuilderSeo();

        $query = "ALTER TABLE `builder_seo`
            ADD COLUMN IF NOT EXISTS `indexing` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `nofollow` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `canonicalUrl` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `redirectLink` tinyint(1) DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `redirectType` varchar(190) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `destinationUrl` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `schemaBlock` longtext DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `image` varchar(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS `imageAsset` varchar(255) DEFAULT NULL
        ";

        Db::get()->executeQuery($query);
    }
}
