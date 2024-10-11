<?php

namespace Starfruit\BuilderBundle\Controller\Document;

class EditableBaseController extends \Pimcore\Controller\FrontendController
{
    public static function getEditables()
    {
        $trace = debug_backtrace();
        $call = $trace[1];

        $classes = explode("\\", $call['class']);
        $class = end($classes);
        // add `Editable` to prefix of class name
        $editableClass = str_replace($class, 'Editable' . $class, $call['class']);

        if (class_exists($editableClass)) {
            if (method_exists($editableClass, $call['function'])) {
                $editableCustom = call_user_func($editableClass . '::' . $call['function']);

                return $editableCustom;
            }
        }

        return null;
    }
}
