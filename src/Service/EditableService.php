<?php

namespace Starfruit\BuilderBundle\Service;

class EditableService
{
    public static function getEditdata($editable)
    {
        $type = $editable?->getType();

        if ($type) {
            $function = 'get' . ucfirst($type);
            if (method_exists(__CLASS__, $function)) {
                return call_user_func_array(__CLASS__ .'::'. $function, [$editable]);
            }
        }

        return null;
    }

    public static function getBlock($editable, $fields)
    {
        $data = [];
        $elements = $editable?->getElements();

        if (!empty($elements)) {
            foreach ($elements as $key => $element) {
                foreach ($fields as $field) {
                    $data[$key][$field] = self::getEditdata($element->getEditable($field));
                }
            }
        }
    
        return $data;
    }

    private static function getInput($editable)
    {
        return $editable->getData();
    }

    private static function getTextarea($editable)
    {
        return $editable->getData();
    }

    private static function getWysiwyg($editable)
    {
        return $editable->getData();
    }

    private static function getImage($editable)
    {
        return $editable->isEmpty() ? null : $editable->getImage();
    }

    private static function getCheckbox($editable)
    {
        return (bool) $editable->getData();
    }

    private static function getVideo($editable)
    {
        if ($editable->isEmpty()) {
            return null;
        }

        $data = $editable->getData();

        return [
            'type' => $data['type'],
            'path' => $data['path'],
            'videoAsset' => $editable->getVideoAsset(),
        ];
    }

    private static function getRelation($editable)
    {
        return $editable->isEmpty() ? null : $editable?->getElement();
    }

    private static function getRelations($editable)
    {
        return $editable->isEmpty() ? [] : (array) $editable?->getElements();
    }

    private static function getLink($editable)
    {
        if ($editable->isEmpty()) {
            return null;
        }

        $data = [
            'text' => $editable->getText(),
            'href' => $editable->getHref(),
        ];

        return $data;
    }

    private static function getSelect($editable)
    {
        return $editable->isEmpty() ? null : $editable->getData();
    }

    private static function getNumeric($editable)
    {
        return $editable->isEmpty() ? null : $editable->getData();
    }

    private static function getDate($editable)
    {
        return $editable->isEmpty() ? null : $editable->getData();
    }
}
