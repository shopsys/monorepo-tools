<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\Exception\EmptyCustomerIdentifierException;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CustomerIdentifierTest extends PHPUnit_Framework_TestCase {

	public function testCreateEmpty() {
		$sessionId = '';
		$user = null;

		$this->setExpectedException(EmptyCustomerIdentifierException::class);
		$customerIdentifier = new CustomerIdentifier($sessionId, $user);
	}

}
