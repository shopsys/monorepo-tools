<?php

namespace Tests\ShopBundle\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Customer\Exception\EmptyCustomerIdentifierException;

class CustomerIdentifierTest extends PHPUnit_Framework_TestCase
{
    public function testCannotCreateIdentifierForEmptyCartIdentifierAndNullUser()
    {
        $cartIdentifier = '';
        $user = null;

        $this->expectException(EmptyCustomerIdentifierException::class);
        new CustomerIdentifier($cartIdentifier, $user);
    }
}
