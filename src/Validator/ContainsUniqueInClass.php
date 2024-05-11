<?php

namespace Starfruit\BuilderBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsUniqueInClass extends Constraint
{
    public $message = '';

    public $class;
    public $field;

    public function validatedBy()
    {
        return static::class.'Validator';
    } 
}
