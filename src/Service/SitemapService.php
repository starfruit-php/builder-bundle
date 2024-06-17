<?php

namespace Starfruit\BuilderBundle\Service;

use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Document\Page;
use Starfruit\BuilderBundle\Model\Seo;
use Starfruit\BuilderBundle\Tool\SystemTool;
use Starfruit\BuilderBundle\Sitemaps\Command;
use Starfruit\BuilderBundle\Sitemaps\Generator;
use Starfruit\BuilderBundle\Config\SitemapConfig;
use Starfruit\BuilderBundle\Config\ObjectConfig;

class SitemapService
{
    const PAGE_SECTION_NAME = 'default';

    private static function generate($element, $section)
    {
        $seo = Seo::getOrCreate($element);

        if ($seo) {
            $domain = SystemTool::getDomain();

            if ($domain) {
                Command::dumpSitemap($domain, $section);
            }
        }
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
                self::generate($object, strtolower($object->getClassname()));
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
                self::generate($document, self::PAGE_SECTION_NAME);
            }
        }
    }
}
