<?php

namespace Starfruit\BuilderBundle\LinkGenerator;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Starfruit\BuilderBundle\Config\ObjectConfig;

class AbstractLinkGenerator implements LinkGeneratorInterface
{
    public function generate(object $object, array $params = []): string
    {
        $objectConfig = new ObjectConfig($object);
        return $objectConfig->getSlug($params);
    }
}
