<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateDuJourMinimum extends Constraint
{
    public $message = "La date doit être au moins égale à la date d'aujourd'hui";
}
