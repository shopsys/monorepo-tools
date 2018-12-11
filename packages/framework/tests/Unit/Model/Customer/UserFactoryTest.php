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

    public function testCreateNotDuplicateEmail()
    {
        $customerService = $this->getUserFactory();

        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = $customerService->create(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );
        $this->assertInstanceOf(User::class, $user1);

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'no-reply2@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $user2 = $customerService->create(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );
        $this->assertInstanceOf(User::class, $user2);
    }

    public function testCreateDuplicateEmail()
    {
        $customerService = $this->getUserFactory();

        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = $customerService->create(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'no-reply@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);
        $customerService->create(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );
    }

    public function testCreateDuplicateEmailCaseInsentitive()
    {
        $customerService = $this->getUserFactory();

        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = $customerService->create(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'NO-reply@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);
        $customerService->create(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );
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
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData|null $billingAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress(BillingAddressData $billingAddressData = null)
    {
        return new BillingAddress($billingAddressData ?? new BillingAddressData());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData|null $deliveryAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    private function createDeliveryAddress(DeliveryAddressData $deliveryAddressData = null)
    {
        return new DeliveryAddress($deliveryAddressData ?? new DeliveryAddressData());
    }
}
