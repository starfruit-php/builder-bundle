<?php

namespace  Starfruit\BuilderBundle\EventListener\Admin;

use Pimcore\Event\BundleManager\PathsEvent;

class StyleListener
{
    public function addCSSFiles(PathsEvent $event)
    {
        $cssFiles = [
            '/bundles/starfruitbuilder/css/admin/custom.css',
        ];

        $event->setPaths(
            array_merge(
                $event->getPaths(), $cssFiles
            )
        );
    }

    public function addJSFiles(PathsEvent $event)
    {
        $jsFiles = [
            '/bundles/starfruitbuilder/js/jquery.min.js',
            '/bundles/starfruitbuilder/js/admin/custom.js',
        ];

        $event->setPaths(
            array_merge(
                $event->getPaths(), $jsFiles
            )
        );
    }
}
