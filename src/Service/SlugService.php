<?php

namespace Starfruit\BuilderBundle\Service;

use Pimcore\Db;
use Pimcore\Model\Document;
use Pimcore\Tool\Frontend;
use Pimcore\Model\Element;

class SlugService
{
    public static function validatePrettyUrl($checkDocument, string $slug)
    {
        $path = trim($slug);
        $path = rtrim($path, '/');

        if (strlen($path) < 2) {
            return false;
            // $success = false;
            // $message[] = 'URL must be at least 2 characters long.';
        }

        if (!Element\Service::isValidPath($path, 'document')) {
            return false;
            // $success = false;
            // $message[] = 'URL is invalid.';
        }

        $id = $checkDocument->getId();

        $list = new Document\Listing();
        $list->setCondition('(CONCAT(`path`, `key`) = ? OR id IN (SELECT id from documents_page WHERE prettyUrl = ?))
            AND id != ?', [
            $path, $path, $id,
        ]);

        if ($list->getTotalCount() > 0) {
            $checkSite     = Frontend::getSiteForDocument($checkDocument);
            $checkSiteId   = empty($checkSite) ? 0 : $checkSite->getId();

            foreach ($list as $document) {
                if (empty($document)) {
                    continue;
                }

                $site   = Frontend::getSiteForDocument($document);
                $siteId = empty($site) ? 0 : $site->getId();

                if ($siteId === $checkSiteId) {
                    return false;
                    // $success   = false;
                    // $message[] = 'URL path already exists.';

                    // break;
                }
            }
        }

        return true;
    }

    public static function queryByClassname($classname, $locale = null)
    {
        if (!$locale) {
            $query = "SELECT SLUG.`slug` as slug
                FROM `object_url_slugs` as SLUG
                INNER JOIN `classes` as CLASSES
                INNER JOIN `objects` as OBJECTS
                WHERE SLUG.`ownertype` = 'localizedfield'
                AND SLUG.`classId` = CLASSES.`id`
                AND CLASSES.`name` = ?
                AND SLUG.`objectId` = OBJECTS.`id`
                AND OBJECTS.`published` = 1";

            $params = [$classname];
        } else {
            $query = "SELECT SLUG.`slug` as slug
                FROM `object_url_slugs` as SLUG
                INNER JOIN `classes` as CLASSES
                INNER JOIN `objects` as OBJECTS
                WHERE SLUG.`ownertype` = 'localizedfield'
                AND SLUG.`position` = ?
                AND SLUG.`classId` = CLASSES.`id`
                AND CLASSES.`name` = ?
                AND SLUG.`objectId` = OBJECTS.`id`
                AND OBJECTS.`published` = 1";

            $params = [$locale, $classname];
        }

        $data = Db::get()->fetchAllAssociative($query, $params);
        $data = array_column($data, 'slug');

        return $data;
    }
}
