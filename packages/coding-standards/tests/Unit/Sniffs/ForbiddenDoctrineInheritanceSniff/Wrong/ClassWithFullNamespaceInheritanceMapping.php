<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\Sniffs\ForbiddenDoctrineInheritanceSniff\Wrong;

/**
 * @Doctrine\ORM\Mapping\InheritanceType("SINGLE_TABLE")
 */
class ClassWithFullNamespaceInheritanceMapping
{
}
