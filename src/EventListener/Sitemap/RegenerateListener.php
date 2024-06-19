<?php

namespace Starfruit\BuilderBundle\EventListener\Sitemap;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Starfruit\BuilderBundle\Service\SitemapService;

class RegenerateListener
{
    private function isSaveVersion($event)
    {
        $args = $event->getArguments();
        $saveVersionOnly = isset($args['saveVersionOnly']) && $args['saveVersionOnly'];

        return $saveVersionOnly;
    }

    public function postObjectUpdate(DataObjectEvent $event)
    {
        if (!$this->isSaveVersion($event)) {
            SitemapService::generateObject($event->getObject());
        }
    }

    public function postObjectDelete(DataObjectEvent $event)
    {
        SitemapService::generateObject($event->getObject());
    }

    public function postDocumentUpdate(DocumentEvent $event)
    {
        if (!$this->isSaveVersion($event)) {
            SitemapService::generateDocument($event->getDocument());
        }
    }

    public function postDocumentDelete(DocumentEvent $event)
    {
        SitemapService::generateDocument($event->getDocument());
    }
}
