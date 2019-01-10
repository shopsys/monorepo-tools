<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Customer\UserFactory;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserFactoryTest extends TestCase
{
    public function testCreate()
    {
        $customerService = $this->getUserFactory();

        $billingAddress = $this->createBillingAddress();
        $deliveryAddress = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData = new UserData();
        $userData->firstName = 'firstName';
        $userData->lastName = 'lastName';
        $userData->email = 'no-reply@shopsys.com';
        $userData->password = 'pa55w0rd';

        $user = $customerService->create(
            $userData,
            $billingAddress,
            $deliveryAddress,
            $userByEmail
        );

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\UserFactory
     */
    private function getUserFactory(): UserFactory
    {
        $encoderFactory = $this->getEncoderFactory();

        return new UserFactory(new EntityNameResolver([]), $encoderFactory);
    }

    /**
     * @return \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private function getEncoderFactory(): EncoderFactory
    {
        $encoder = new BCryptPasswordEncoder(12);

        return new EncoderFactory([User::class => $encoder]);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress()
    {
        return new BillingAddress(new BillingAddressData());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    private function createDeliveryAddress()
    {
        return new DeliveryAddress(new DeliveryAddressData());
    }
}
