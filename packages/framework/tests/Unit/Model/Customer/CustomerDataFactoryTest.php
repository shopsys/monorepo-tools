<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use DateTime;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactory;
use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactory;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactory;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactory;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;

class CustomerDataFactoryTest extends TestCase
{
    /** @access private */
    const DOMAIN_ID = 1;

    public function testGetAmendedCustomerDataByOrderWithoutChanges()
    {
        $customerDataFactory = $this->getCustomerDataFactory();

        $userData = new UserData();
        $userData->firstName = 'firstName';
        $userData->lastName = 'lastName';
        $userData->createdAt = new DateTime();
        $userData->telephone = 'telephone';
        $userData->email = 'no-reply@shopsys.com';
        $userData->domainId = 1;

        $billingCountryData = new CountryData();
        $billingCountryData->names = ['cs' => 'Česká republika'];
        $billingCountry = new Country($billingCountryData);
        $billingAddressData = new BillingAddressData();
        $billingAddressData->street = 'street';
        $billingAddressData->city = 'city';
        $billingAddressData->postcode = 'postcode';
        $billingAddressData->companyCustomer = true;
        $billingAddressData->companyName = 'companyName';
        $billingAddressData->companyNumber = 'companyNumber';
        $billingAddressData->companyTaxNumber = 'companyTaxNumber';
        $billingAddressData->country = $billingCountry;

        $deliveryCountryData = new CountryData();
        $deliveryCountryData->names = ['cs' => 'Slovenská republika'];
        $deliveryCountry = new Country($deliveryCountryData);
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
        $user = new User($userData, $billingAddress, $deliveryAddress, null);

        $transportData = new TransportData();
        $transportData->name = ['cs' => 'transportName'];
        $transport = new Transport($transportData);
        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $payment = new Payment($paymentData);
        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['en' => 'orderStatusName'];
        $orderStatus = new OrderStatus($orderStatusData, OrderStatus::TYPE_NEW);
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

        $customerData = $customerDataFactory->createAmendedByOrder($user, $order);

        $this->assertEquals($userData, $customerData->userData);
        $this->assertEquals($billingAddressData, $customerData->billingAddressData);
        $this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
    }

    public function testGetAmendedCustomerDataByOrder()
    {
        $customerDataFactory = $this->getCustomerDataFactory();

        $billingCountryData = new CountryData();
        $billingCountryData->names = ['cs' => 'Česká republika'];

        $deliveryCountryData = new CountryData();
        $deliveryCountryData->names = ['cs' => 'Slovenská republika'];

        $billingCountry = new Country($billingCountryData);
        $deliveryCountry = new Country($deliveryCountryData);
        $userData = new UserData();
        $userData->firstName = 'firstName';
        $userData->lastName = 'lastName';
        $userData->email = 'no-reply@shopsys.com';
        $userData->createdAt = new DateTime();
        $userData->domainId = 1;

        $billingAddress = $this->createBillingAddress();
        $user = new User($userData, $billingAddress, null, null);

        $transportData = new TransportData();
        $transportData->name = ['cs' => 'transportName'];
        $transport = new Transport($transportData);
        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $payment = new Payment($paymentData);
        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['en' => 'orderStatusName'];
        $orderStatus = new OrderStatus($orderStatusData, OrderStatus::TYPE_NEW);
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

        $customerData = $customerDataFactory->createAmendedByOrder($user, $order);

        $this->assertEquals($userData, $customerData->userData);
        $this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
        $this->assertTrue($customerData->billingAddressData->companyCustomer);
        $this->assertSame($order->getCompanyName(), $customerData->billingAddressData->companyName);
        $this->assertSame($order->getCompanyNumber(), $customerData->billingAddressData->companyNumber);
        $this->assertSame($order->getCompanyTaxNumber(), $customerData->billingAddressData->companyTaxNumber);
        $this->assertSame($order->getStreet(), $customerData->billingAddressData->street);
        $this->assertSame($order->getCity(), $customerData->billingAddressData->city);
        $this->assertSame($order->getPostcode(), $customerData->billingAddressData->postcode);
        $this->assertSame($order->getCountry(), $customerData->billingAddressData->country);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactory
     */
    private function getCustomerDataFactory(): CustomerDataFactory
    {
        $billingAddressDataFactory = new BillingAddressDataFactory();
        $deliveryAddressDataFactory = new DeliveryAddressDataFactory();
        $userDataFactory = new UserDataFactory($this->createMock(PricingGroupSettingFacade::class));
        $customerDataFactory = new CustomerDataFactory($billingAddressDataFactory, $deliveryAddressDataFactory, $userDataFactory);

        return $customerDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData|null $billingAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress(?BillingAddressData $billingAddressData = null)
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
    private function createDeliveryAddress(?DeliveryAddressData $deliveryAddressData = null)
    {
        if ($deliveryAddressData === null) {
            $deliveryAddressData = new DeliveryAddressData();
        }

        return new DeliveryAddress($deliveryAddressData);
    }
}
