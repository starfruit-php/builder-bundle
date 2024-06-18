<?php

namespace Starfruit\BuilderBundle\EventListener\Object;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Folder;
use Starfruit\BuilderBundle\Config\ObjectConfig;

class SlugListener
{
    public function preUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if (!($object instanceof Folder)) {
            $objectConfig = new ObjectConfig($object);
            $objectConfig->setSlugs();
        }
    }
}
