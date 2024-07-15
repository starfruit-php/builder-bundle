<?php

namespace Starfruit\BuilderBundle\Sitemap;

use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Document\Page;
use Starfruit\BuilderBundle\Config\SitemapConfig;
use Starfruit\BuilderBundle\Config\ObjectConfig;

class Regenerate extends Generator
{
    private static function generate($element, $section)
    {
        $location = self::getLocation($section);
        if (!file_exists($location)) {
            self::populate($section);
            self::populateIndex();
            return;
        }

        $sitemapType = new Type\Urlset();

        // get xml content
        $sitemapContent = file_get_contents($location);
        $sitemapReplaceContent = $sitemapType->getReplaceFromXmlContent($sitemapContent);

        // convert xml to array
        $content = simplexml_load_string($sitemapContent, "SimpleXMLElement", LIBXML_NOCDATA);
        $content = json_encode($content);
        $content = json_decode($content, TRUE)[$sitemapType::TAG];

        // get old content to remove
        $elementId = $element->getId();
        $filterSlugs = array_filter($content, fn($e) => $e['id'] == $elementId);
        // remove
        $removeContent = $sitemapType->getReplaceContent($filterSlugs);
        $contentAfterRemove = str_replace($removeContent, '', $sitemapReplaceContent);

        // create new content then concat
        $slugs = self::getSlugs(
            $element instanceof Page ? $section : $element->getClassname(),
            $element->getId()
        );
        $newContent = $sitemapType->getReplaceContent($slugs);
        // concat with order
        if (Setting::isOrderDesc()) {
            $replaceContent = $newContent . $contentAfterRemove;
        } else {
            $replaceContent = $contentAfterRemove . $newContent;
        }

        $sitemapType->pushContent($replaceContent, $section);
    }

    public static function generateObject($object, $forceGenerate = false)
    {
        if (!($object instanceof Folder)) {
            $generate = $forceGenerate;
            if (!$generate) {
                $config = new ObjectConfig($object);
                $generate = $config->sitemapAutoGenerate();
            }

            if ($generate) {
                self::populate(strtolower($object->getClassname()));
                // self::generate($object, strtolower($object->getClassname()));
            }
        }
    }

    public static function generateDocument($document, $forceGenerate = false)
    {
        if ($document instanceof Page) {
            $generate = $forceGenerate;
            if (!$generate) {
                $config = new SitemapConfig();
                $generate = $config->getDocumentAutoGenerate();
            }

            if ($generate) {
                self::populate(self::PAGE_SECTION_NAME);
                // self::generate($document, self::PAGE_SECTION_NAME);
            }
        }
    }
}
