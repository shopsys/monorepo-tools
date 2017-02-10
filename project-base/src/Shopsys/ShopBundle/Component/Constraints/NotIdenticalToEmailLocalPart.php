<?php

namespace Shopsys\ShopBundle\Component\Constraints;

use Symfony\Component\Validator\Constraint;

class NotIdenticalToEmailLocalPart extends Constraint
{

    public $password;

    public $email;

    public $errorPath;

    public $message = 'Password cannot be local part of e-mail.';
}
