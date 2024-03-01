<?php

namespace Starfruit\BuilderBundle\LinkGenerator;

use Pimcore\Model\DataObject\Data\UrlSlug;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\ParameterTool;

class AbstractLinkGenerator
{
    public static function generate(object $object, array $params = [])
    {
        $linkGenerateObjects = ParameterTool::getLinkGenerateObjects();

        if (!empty($linkGenerateObjects)) {
            foreach ($linkGenerateObjects as $config) {
                $class = "\\Pimcore\\Model\\DataObject\\" . $config['class_name'];
                $function = 'get' . ucfirst($config['field_for_slug']);

                if ($object instanceof $class && method_exists($object, $function)) {
                    $slugs = $object->$function();

                    if (!is_array($slugs) || empty($slugs)) {
                        return null;
                    }

                    $slug = reset($slugs);
                    return $slug instanceof UrlSlug ? $slug->getSlug() : null;
                }
            }
        }

        throw new \Exception('Invalid object. Checking starfruit_builder.link_generate_objects config again!');
    }
}
