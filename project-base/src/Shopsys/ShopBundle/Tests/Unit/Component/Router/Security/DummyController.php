<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Router\Security;

use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;

class DummyController {

    public function withoutProtectionAction() {

    }

    /**
     * @CsrfProtection
     */
    public function withProtectionAction() {

    }

}
