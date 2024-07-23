<?php

namespace Starfruit\BuilderBundle\LinkGenerator;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Pimcore\Model\Document;

class AbstractLinkGenerator implements LinkGeneratorInterface
{
    public function generate(object $object, array $params = []): string
    {
        try {
            $trace = debug_backtrace();
            $call = $trace[1];
            $isPreview = $call['function'] == 'generatePreviewUrl';

            if ($isPreview) {
                $locale = $params['locale'];

                $existDocument = Document::getByPath("/$locale");
                if (!($existDocument && $existDocument->getPublished())) {
                    unset($params['locale']);
                }
            }
        } catch (\Throwable $e) {
            
        }
            
        $objectConfig = new ObjectConfig($object);
        return $objectConfig->getSlug($params);
    }
}
