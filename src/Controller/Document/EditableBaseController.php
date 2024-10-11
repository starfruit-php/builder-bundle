<?php

namespace Starfruit\BuilderBundle\Controller\Document;

class EditableBaseController extends \Pimcore\Controller\FrontendController
{
    public static function getEditables()
    {
        $trace = debug_backtrace();
        $call = $trace[1];

        return self::getEditablesFromClass($call['class'], $call['function']);
    }

    public static function getEditablesFromClass($originClass, $function)
    {
        $classes = explode("\\", $originClass);
        $class = end($classes);

        // add `Editable` to prefix of class name
        $editableClass = str_replace($class, 'Editable' . $class, $originClass);

        if (class_exists($editableClass)) {
            if (method_exists($editableClass, $function)) {
                return call_user_func($editableClass . '::' . $function);
            }
        }

        return null;
    }
}
