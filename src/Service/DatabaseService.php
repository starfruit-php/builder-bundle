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
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        Db::get()->executeQuery($query);
    }
}
