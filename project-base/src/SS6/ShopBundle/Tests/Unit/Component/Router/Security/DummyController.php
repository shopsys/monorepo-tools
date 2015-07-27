<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Router\Security;

use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;

class DummyController {

	public function withoutProtectionAction() {

	}

	/**
	 * @CsrfProtection
	 */
	public function withProtectionAction() {

	}

}
