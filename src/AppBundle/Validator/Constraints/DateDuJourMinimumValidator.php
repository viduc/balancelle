<?php

namespace AppBundle\Validator\Constraints;

use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateDuJourMinimumValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $aujourdhui = new DateTime();
        $eval = $value > $aujourdhui;
        if (!$eval) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
