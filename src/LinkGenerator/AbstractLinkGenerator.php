<?php

namespace Starfruit\BuilderBundle\LinkGenerator;

use Pimcore\Model\DataObject\Data\UrlSlug;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\ParameterTool;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;

class AbstractLinkGenerator implements LinkGeneratorInterface
{
    public function generate(object $object, array $params = []): string
    {
        $linkGenerateObjects = ParameterTool::getLinkGenerateObjects();

        if (!empty($linkGenerateObjects)) {
            foreach ($linkGenerateObjects as $config) {
                $class = "\\Pimcore\\Model\\DataObject\\" . $config['class_name'];
                $function = 'get' . ucfirst($config['field_for_slug']);

                if ($object instanceof $class && method_exists($object, $function)) {
                    $locale = isset($params['locale']) ? $params['locale'] : null;
                    $slugs = $locale ? $object->$function($locale) : $object->$function();

                    if (!is_array($slugs) || empty($slugs)) {
                        return '';
                    }

                    $slug = reset($slugs);
                    return $slug instanceof UrlSlug ? $slug->getSlug() : '';
                }
            }
        }

        throw new \Exception('Invalid object. Checking starfruit_builder.link_generate_objects config again!');
    }
}
