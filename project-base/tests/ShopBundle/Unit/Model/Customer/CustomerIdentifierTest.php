<?php

namespace Tests\ShopBundle\Unit\Model\Customer;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Customer\Exception\EmptyCustomerIdentifierException;

class CustomerIdentifierTest extends TestCase
{
    public function testCannotCreateIdentifierForEmptyCartIdentifierAndNullUser()
    {
        $cartIdentifier = '';
        $user = null;

        $this->expectException(EmptyCustomerIdentifierException::class);
        new CustomerIdentifier($cartIdentifier, $user);
    }
}
