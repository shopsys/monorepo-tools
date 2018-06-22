<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use DateTime;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService;
use Shopsys\FrameworkBundle\Model\Customer\CustomerService;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactory;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Customer\UserFactory;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;

class CustomerServiceTest extends TestCase
{
    public function testCreate()
    {
        $customerService = $this->getCustomerService();

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
        $customerService = $this->getCustomerService();

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
        $customerService = $this->getCustomerService();

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
        $customerService = $this->getCustomerService();

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

    const DOMAIN_ID = 1;

    public function testGetAmendedCustomerDataByOrderWithoutChanges()
    {
        $customerService = $this->getCustomerService();

        $userData = new UserData();
        $userData->firstName = 'firstName';
        $userData->lastName = 'lastName';
        $userData->createdAt = new DateTime();

        $billingCountryData = new CountryData();
        $billingCountryData->name = 'Česká republika';
        $billingCountry = new Country($billingCountryData, self::DOMAIN_ID);
        $billingAddressData = new BillingAddressData();
        $billingAddressData->street = 'street';
        $billingAddressData->city = 'city';
        $billingAddressData->postcode = 'postcode';
        $billingAddressData->companyCustomer = true;
        $billingAddressData->companyName = 'companyName';
        $billingAddressData->companyNumber = 'companyNumber';
        $billingAddressData->companyTaxNumber = 'companyTaxNumber';
        $billingAddressData->telephone = 'telephone';
        $billingAddressData->country = $billingCountry;

        $deliveryCountryData = new CountryData();
        $deliveryCountryData->name = 'Slovenská republika';
        $deliveryCountry = new Country($deliveryCountryData, self::DOMAIN_ID);
        $deliveryAddressData = new DeliveryAddressData();
        $deliveryAddressData->addressFilled = true;
        $deliveryAddressData->street = 'deliveryStreet';
        $deliveryAddressData->city = 'deliveryCity';
        $deliveryAddressData->postcode = 'deliveryPostcode';
        $deliveryAddressData->companyName = 'deliveryCompanyName';
        $deliveryAddressData->firstName = 'deliveryFirstName';
        $deliveryAddressData->lastName = 'deliveryLastName';
        $deliveryAddressData->telephone = 'deliveryTelephone';
        $deliveryAddressData->country = $deliveryCountry;

        $billingAddress = $this->createBillingAddress($billingAddressData);
        $deliveryAddress = $this->createDeliveryAddress($deliveryAddressData);
        $user = new User($userData, $billingAddress, $deliveryAddress);

        $transport = new Transport(new TransportData(['cs' => 'transportName']));
        $payment = new Payment(new PaymentData(['cs' => 'paymentName']));
        $orderStatus = new OrderStatus(new OrderStatusData(['en' => 'orderStatusName']), OrderStatus::TYPE_NEW);
        $orderData = new OrderData();
        $orderData->transport = $transport;
        $orderData->payment = $payment;
        $orderData->status = $orderStatus;
        $orderData->firstName = 'orderFirstName';
        $orderData->lastName = 'orderLastName';
        $orderData->email = 'order@email.com';
        $orderData->telephone = 'orderTelephone';
        $orderData->street = 'orderStreet';
        $orderData->city = 'orderCity';
        $orderData->postcode = 'orderPostcode';
        $orderData->country = $billingCountry;
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = 'orderDeliveryFirstName';
        $orderData->deliveryLastName = 'orderDeliveryLastName';
        $orderData->deliveryCompanyName = 'orderDeliveryCompanyName';
        $orderData->deliveryTelephone = 'orderDeliveryTelephone';
        $orderData->deliveryStreet = 'orderDeliveryStreet';
        $orderData->deliveryCity = 'orderDeliveryCity';
        $orderData->deliveryPostcode = 'orderDeliveryPostcode';
        $orderData->deliveryCountry = $deliveryCountry;
        $orderData->domainId = self::DOMAIN_ID;
        $order = new Order(
            $orderData,
            '123456',
            '7ebafe9fe'
        );
        $order->setCompanyInfo(
            'companyName',
            'companyNumber',
            'companyTaxNumber'
        );

        $customerData = $customerService->getAmendedCustomerDataByOrder($user, $order);

        $this->assertEquals($userData, $customerData->userData);
        $this->assertEquals($billingAddressData, $customerData->billingAddressData);
        $this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
    }

    public function testGetAmendedCustomerDataByOrder()
    {
        $customerService = $this->getCustomerService();

        $billingCountryData = new CountryData();
        $billingCountryData->name = 'Česká republika';

        $deliveryCountryData = new CountryData();
        $deliveryCountryData->name = 'Slovenská republika';

        $billingCountry = new Country($billingCountryData, self::DOMAIN_ID);
        $deliveryCountry = new Country($deliveryCountryData, self::DOMAIN_ID);
        $userData = new UserData();
        $userData->firstName = 'firstName';
        $userData->lastName = 'lastName';
        $userData->createdAt = new DateTime();

        $billingAddress = $this->createBillingAddress();
        $user = new User($userData, $billingAddress, null);

        $transport = new Transport(new TransportData(['cs' => 'transportName']));
        $payment = new Payment(new PaymentData(['cs' => 'paymentName']));
        $orderStatus = new OrderStatus(new OrderStatusData(['en' => 'orderStatusName']), OrderStatus::TYPE_NEW);
        $orderData = new OrderData();
        $orderData->transport = $transport;
        $orderData->payment = $payment;
        $orderData->status = $orderStatus;
        $orderData->firstName = 'orderFirstName';
        $orderData->lastName = 'orderLastName';
        $orderData->email = 'order@email.com';
        $orderData->telephone = 'orderTelephone';
        $orderData->street = 'orderStreet';
        $orderData->city = 'orderCity';
        $orderData->postcode = 'orderPostcode';
        $orderData->country = $billingCountry;
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = 'orderDeliveryFirstName';
        $orderData->deliveryLastName = 'orderDeliveryLastName';
        $orderData->deliveryCompanyName = 'orderDeliveryCompanyName';
        $orderData->deliveryTelephone = 'orderDeliveryTelephone';
        $orderData->deliveryStreet = 'orderDeliveryStreet';
        $orderData->deliveryCity = 'orderDeliveryCity';
        $orderData->deliveryPostcode = 'orderDeliveryPostcode';
        $orderData->deliveryCountry = $deliveryCountry;
        $orderData->domainId = self::DOMAIN_ID;
        $order = new Order(
            $orderData,
            '123456',
            '7eba123456fe9fe'
        );
        $order->setCompanyInfo(
            'companyName',
            'companyNumber',
            'companyTaxNumber'
        );

        $deliveryAddressData = new DeliveryAddressData();
        $deliveryAddressData->addressFilled = true;
        $deliveryAddressData->street = $order->getDeliveryStreet();
        $deliveryAddressData->city = $order->getDeliveryCity();
        $deliveryAddressData->postcode = $order->getDeliveryPostcode();
        $deliveryAddressData->companyName = $order->getDeliveryCompanyName();
        $deliveryAddressData->firstName = $order->getDeliveryFirstName();
        $deliveryAddressData->lastName = $order->getDeliveryLastName();
        $deliveryAddressData->telephone = $order->getDeliveryTelephone();
        $deliveryAddressData->country = $deliveryCountry;

        $customerData = $customerService->getAmendedCustomerDataByOrder($user, $order);

        $this->assertEquals($userData, $customerData->userData);
        $this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
        $this->assertTrue($customerData->billingAddressData->companyCustomer);
        $this->assertSame($order->getCompanyName(), $customerData->billingAddressData->companyName);
        $this->assertSame($order->getCompanyNumber(), $customerData->billingAddressData->companyNumber);
        $this->assertSame($order->getCompanyTaxNumber(), $customerData->billingAddressData->companyTaxNumber);
        $this->assertSame($order->getStreet(), $customerData->billingAddressData->street);
        $this->assertSame($order->getCity(), $customerData->billingAddressData->city);
        $this->assertSame($order->getPostcode(), $customerData->billingAddressData->postcode);
        $this->assertSame($order->getTelephone(), $customerData->billingAddressData->telephone);
        $this->assertSame($order->getCountry(), $customerData->billingAddressData->country);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerService
     */
    private function getCustomerService()
    {
        $customerPasswordServiceMock = $this->createMock(CustomerPasswordService::class);
        $deliveryAddressFactory = new DeliveryAddressFactory();
        $userFactory = new UserFactory();

        return new CustomerService($customerPasswordServiceMock, $deliveryAddressFactory, $userFactory);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData|null $billingAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress(BillingAddressData $billingAddressData = null)
    {
        if ($billingAddressData === null) {
            $billingAddressData = new BillingAddressData();
        }

        return new BillingAddress($billingAddressData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData|null $deliveryAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    private function createDeliveryAddress(DeliveryAddressData $deliveryAddressData = null)
    {
        if ($deliveryAddressData === null) {
            $deliveryAddressData = new DeliveryAddressData();
        }

        return new DeliveryAddress($deliveryAddressData);
    }
}
