<?php

namespace Starfruit\BuilderBundle\EventListener\Sitemap;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Starfruit\BuilderBundle\Sitemap\Regenerate;

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
