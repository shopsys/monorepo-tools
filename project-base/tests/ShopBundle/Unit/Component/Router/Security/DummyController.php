<?php

namespace Tests\ShopBundle\Unit\Component\Router\Security;

use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;

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
