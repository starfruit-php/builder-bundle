<?php

namespace Starfruit\BuilderBundle\EventListener\Object;

use Pimcore\Event\Model\DataObjectEvent;
use Starfruit\BuilderBundle\Config\ObjectConfig;

class SlugListener
{
    public function preUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        $objectConfig = new ObjectConfig($object);
        $objectConfig->setSlug();
    }
}
