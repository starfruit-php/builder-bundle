<?php

namespace Starfruit\BuilderBundle\EventListener\Object;

use Pimcore\Model\DataObject;
use Pimcore\Event\Model\DataObjectEvent;

class SearchListener
{
    const SEARCH_FIELD = 'searchData';

    // create a textarea field with name `searchData` to store all string then search by it
    // all string fields are listed in Tooltip of above field, ex: name,content,price
    public function preUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if (!($object instanceof DataObject\Folder)) {
            $method = 'get' . ucfirst(self::SEARCH_FIELD);

            if (method_exists($object, $method)) {
                $searchData = [];

                $class = $object->getClass();
                $fieldDefinition = $class->getFieldDefinition(self::SEARCH_FIELD);

                $tooltip = $fieldDefinition->tooltip;
                $fields = explode("\n", $tooltip);
                if (!empty($tooltip)) {
                    foreach ($fields as $field) {
                        $searchData[] = $object->{'get' . ucfirst($field)}();
                    }
                }

                $method = 'set' . ucfirst(self::SEARCH_FIELD);
                $object->{$method}(implode(';;;;', $searchData));
            }
        }
    }
}
