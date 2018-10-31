<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Translation;

use Symfony\Component\Validator\Constraint;

class ConstraintClass extends Constraint
{
    public $message = 'This value will be extracted.';

    public $otherMessage = 'This value will also be extracted.';

    public $differentProperty = 'This value will not be extracted (not a message).';
}
