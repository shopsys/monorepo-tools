<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Customer\Exception\EmptyCustomerIdentifierException;

/**
 * @UglyTest
 */
class CustomerIdentifierTest extends PHPUnit_Framework_TestCase {

	public function testCreateEmpty() {
		$sessionId = '';
		$user = null;

		$this->setExpectedException(EmptyCustomerIdentifierException::class);
		new CustomerIdentifier($sessionId, $user);
	}

}
