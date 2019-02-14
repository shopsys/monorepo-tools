<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Translation\Resources;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConstraintViolationClass
{
    /**
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function workingExample(ExecutionContextInterface $context): void
    {
        $context->addViolation('This message will be extracted into "validators" translation domain');
    }
}
