<?php

namespace Starfruit\BuilderBundle\EventListener\Object;

class BaseListener
{
    public function isSaveVersion($event)
    {
        $args = $event->getArguments();
        $saveVersionOnly = isset($args['saveVersionOnly']) && $args['saveVersionOnly'];

        return $saveVersionOnly;
    }
}
