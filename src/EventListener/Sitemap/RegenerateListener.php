<?php

namespace Starfruit\BuilderBundle\EventListener\Sitemap;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Starfruit\BuilderBundle\Sitemap\Regenerate;
use Starfruit\BuilderBundle\EventListener\Object\BaseListener;

class RegenerateListener extends BaseListener
{
    public function postObjectUpdate(DataObjectEvent $event)
    {
        if (!$this->isSaveVersion($event)) {
            Regenerate::generateObject($event->getObject());
        }
    }

    public function postObjectDelete(DataObjectEvent $event)
    {
        Regenerate::generateObject($event->getObject());
    }

    public function postDocumentUpdate(DocumentEvent $event)
    {
        if (!$this->isSaveVersion($event)) {
            Regenerate::generateDocument($event->getDocument());
        }
    }

    public function postDocumentDelete(DocumentEvent $event)
    {
        Regenerate::generateDocument($event->getDocument());
    }
}
