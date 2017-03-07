<?php

namespace Tests\ShopBundle\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Customer\Exception\EmptyCustomerIdentifierException;

class CustomerIdentifierTest extends PHPUnit_Framework_TestCase
{
    public function testCannotCreateIdentifierForEmptyCartIdentifierAndNullUser()
    {
        $cartIdentifier = '';
        $user = null;

        $this->setExpectedException(EmptyCustomerIdentifierException::class);
        new CustomerIdentifier($cartIdentifier, $user);
    }
}
