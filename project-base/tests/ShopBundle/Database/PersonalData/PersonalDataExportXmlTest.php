<?php

namespace Tests\ShopBundle\Database\PersonalData;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Xml\XmlNormalizer;
use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\DatabaseTestCase;

class PersonalDataExportXmlTest extends DatabaseTestCase
{
    const EMAIL = 'no-reply@shopsys.com';
    const EXPECTED_XML_FILE_NAME = 'test.xml';
    const DOMAIN_ID_FIRST = Domain::FIRST_DOMAIN_ID;

    public function testExportXml()
    {
        $country = $this->createCountry();
        $billingAddress = $this->createBillingAddress($country);
        $deliveryAddress = $this->createDeliveryAddress($country);
        $user = $this->createUser($billingAddress, $deliveryAddress);
        $status = $this->createMock(OrderStatus::class);
        $currencyData = new CurrencyData();
        $currencyData->name = 'CZK';
        $currencyData->code = 'CZK';
        $currency = new Currency($currencyData);
        $order = $this->createOrder($currency, $status, $country);
        $product = $this->createMock(Product::class);
        $price = new Price(1, 1);
        $orderItem = new OrderProduct($order, 'test', $price, 1, 1, 'ks', 'cat', $product);
        $order->addItem($orderItem);
        $order->setStatus($status);

        $twig = $this->getContainer()->get('twig');

        $generatedXml = $twig->render('@ShopsysShop/Front/Content/PersonalData/export.xml.twig', [
            'user' => $user,
            'orders' => [0 => $order],
            'newsletterSubscriber' => null,
        ]);

        $generatedXml = XmlNormalizer::normalizeXml($generatedXml);

        $expectedXml = file_get_contents(__DIR__ . '/Resources/' . self::EXPECTED_XML_FILE_NAME);
        $this->assertEquals($expectedXml, $generatedXml);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    private function createCountry()
    {
        $countryData = new CountryData();
        $countryData->name = 'Czech Republic';
        $countryData->code = 'CZ';
        $country = new Country($countryData, self::DOMAIN_ID_FIRST);

        return $country;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress(Country $country)
    {
        $billingAddressData = new BillingAddressData();
        $billingAddressData->country = $country;
        $billingAddressData->city = 'Ostrava';
        $billingAddressData->street = 'Hlubinská';
        $billingAddressData->companyCustomer = true;
        $billingAddressData->companyName = 'Shopsys';
        $billingAddressData->companyNumber = 123456;
        $billingAddressData->companyTaxNumber = 123456;
        $billingAddressData->postcode = 70200;
        $billingAddressData->telephone = '+420987654321';

        $billingAddress = new BillingAddress($billingAddressData);

        return $billingAddress;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    private function createDeliveryAddress(Country $country)
    {
        $deliveryAddressData = new DeliveryAddressData();
        $deliveryAddressData->country = $country;
        $deliveryAddressData->telephone = '+420987654321';
        $deliveryAddressData->postcode = 70200;
        $deliveryAddressData->companyName = 'Shopsys';
        $deliveryAddressData->street = 'Hlubinská';
        $deliveryAddressData->city = 'Ostrava';
        $deliveryAddressData->lastName = 'Fero';
        $deliveryAddressData->firstName = 'Mrkva';
        $deliveryAddress = new DeliveryAddress($deliveryAddressData);

        return $deliveryAddress;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress $deliveryAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    private function createUser(BillingAddress $billingAddress, DeliveryAddress $deliveryAddress)
    {
        $userData = new UserData();
        $userData->firstName = 'Jaromír';
        $userData->lastName = 'Jágr';
        $userData->domainId = self::DOMAIN_ID_FIRST;
        $userData->createdAt = new \DateTime('2018-04-13');
        $userData->email = 'no-reply@shopsys.com';

        $user = new User($userData, $billingAddress, $deliveryAddress);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $status
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    private function createOrder(Currency $currency, OrderStatus $status, Country $country)
    {
        $orderData = new OrderData();
        $orderData->currency = $currency;
        $orderData->status = $status;
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->createdAt = new \DateTime('2018-04-13');
        $orderData->domainId = self::DOMAIN_ID_FIRST;
        $orderData->lastName = 'Bořič';
        $orderData->firstName = 'Adam';
        $orderData->city = 'Liberec';
        $orderData->street = 'Cihelní 5';
        $orderData->companyName = 'Shopsys';
        $orderData->postcode = 65421;
        $orderData->telephone = '+420987654321';
        $orderData->companyTaxNumber = 123456;
        $orderData->companyNumber = 123456;
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->country = $country;

        $order = new Order($orderData, 1523596513, 'hash');

        return $order;
    }
}
