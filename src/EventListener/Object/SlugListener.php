<?php

namespace Starfruit\BuilderBundle\EventListener\Object;

use Pimcore\Tool;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Event\Model\DataObjectEvent;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\ParameterTool;

class SlugListener
{
    private function getLanguages()
    {
        $list = Tool::getValidLanguages();
        return $list;
    }

    private function getDefaultLanguage()
    {
        return Tool::getDefaultLanguage();
    }

    public function preUpdate(DataObjectEvent $event)
    {
        try {
            
        } catch (\Throwable $e) {
            
        }
        
        $linkGenerateObjects = ParameterTool::getLinkGenerateObjects();
        if (!empty($linkGenerateObjects)) {
            foreach ($linkGenerateObjects as $config) {
                $class = "\\Pimcore\\Model\\DataObject\\" . $config['class_name'];

                $object = $event->getObject();
                if ($object instanceof $class) {
                    $setSlugFunc = 'set' . ucfirst($config['field_for_slug']);
                    $getSlugFunc = 'get' . ucfirst($config['field_for_slug']);
                    $getValueFunc = 'get' . ucfirst($config['field_create_slug']);

                    if (method_exists($object, $setSlugFunc)
                        && method_exists($object, $getSlugFunc)
                        && method_exists($object, $getValueFunc)
                    ) {
                        $id = $object->getId();
                        $default = $this->getDefaultLanguage();
                        $defaultValue = $object->$getValueFunc($default);

                        $languages = $this->getLanguages();
                        foreach ($languages as $language) {
                            $currentSlug = $object->$getSlugFunc($language);

                            if (empty($currentSlug)) {
                                $value = $object->$getValueFunc($language) ?: $defaultValue;

                                if ($value) {
                                    $slug = strtolower(TextTool::getPretty($value));
                                    $slug = "/$language/$slug-$id";

                                    $urlslug = new UrlSlug($slug);
                                    $object->$setSlugFunc([$urlslug], $language);
                                }
                            }
                        }

                        break;
                    }
                }
            }
        }
    }
}
