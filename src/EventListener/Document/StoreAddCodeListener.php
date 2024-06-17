<?php

namespace Starfruit\BuilderBundle\EventListener\Document;

use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Model\Document\Snippet;
use Starfruit\BuilderBundle\Model\Option;

class StoreAddCodeListener
{
    const EDITMODE_TEMPLATE = "@StarfruitBuilder\config\script.html.twig";

    public function postUpdate(DocumentEvent $event)
    {
        $document = $event->getDocument();
        if ($document instanceof Snippet) {
            $template = $document->getTemplate();
            $template = str_replace('/', '\\', $template);

            if ($template == self::EDITMODE_TEMPLATE) {
                Option::setCodeHead($document->getEditable('headCode')?->getData());
                Option::setCodeBody($document->getEditable('bodyCode')?->getData());
            }
        }
    }
}
