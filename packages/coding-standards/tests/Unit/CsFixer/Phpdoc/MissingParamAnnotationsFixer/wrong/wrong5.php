<?php

namespace Shopsys\CodingStandards\Tests;

use stdClass;
use Tests\CodingStandards\Unit\CsFixer\Phpdoc\MissingParamAnnotationsFixer\Source\NamespacedType;

final class SomeClass
{
    public function function1(?stdClass $value)
    {
    }

    public function function2(?NamespacedType $value)
    {
    }

    public function function3(int $value = null)
    {
    }
}
