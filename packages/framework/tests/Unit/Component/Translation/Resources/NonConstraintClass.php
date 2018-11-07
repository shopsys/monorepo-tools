<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Translation\Resources;

class NonConstraintClass
{
    public $message = 'This value will not be extracted (not inside a constraint).';
}
