<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueSlugsOnDomains extends Constraint
{
    public $message = 'Address {{ url }} already exists.';

    public $messageDuplicate = 'Address {{ url }} can be entered only once.';
}
