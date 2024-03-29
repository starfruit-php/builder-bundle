<?php

namespace Starfruit\BuilderBundle\EventListener\Asset;

use Starfruit\BuilderBundle\Model\Asset\AdminImageModel;

class AdminImagePreviewListener
{
    public function onResolveElementAdminStyle(\Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent $event): void
    {
        $element = $event->getElement();
        // decide which default styles you want to override
        if ($element instanceof \Pimcore\Model\DataObject\Menu) {
        }

        if ($element instanceof \Pimcore\Model\Asset\Image) {
            $event->setAdminStyle(new AdminImageModel($element));
        }
    }
}
