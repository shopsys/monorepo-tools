<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\Sniffs\ForbiddenDoctrineInheritanceSniff\Correct;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * Doctrine\ORM\Mapping\InheritanceType
 */
class EntityWithoutInheritanceMapping
{
}
