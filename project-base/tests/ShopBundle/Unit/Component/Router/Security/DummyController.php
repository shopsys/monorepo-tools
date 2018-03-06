<?php

namespace Tests\ShopBundle\Unit\Component\Router\Security;

use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;

class DummyController
{
    public function withoutProtectionAction()
    {
    }

    /**
     * @CsrfProtection
     */
    public function withProtectionAction()
    {
    }
}
