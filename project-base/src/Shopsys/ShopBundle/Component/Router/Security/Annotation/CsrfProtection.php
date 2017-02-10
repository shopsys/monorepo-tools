<?php

namespace Shopsys\ShopBundle\Component\Router\Security\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("METHOD")
 */
class CsrfProtection extends Annotation
{
}
