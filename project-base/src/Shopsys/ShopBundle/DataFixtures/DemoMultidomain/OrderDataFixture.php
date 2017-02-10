<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\OrderDataFixture as DemoOrderDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture as DemoPaymentDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture as DemoTransportDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\CountryDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\PaymentDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\SettingValueDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\TransportDataFixture;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserRepository;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager) {
        $userRepository = $this->get(UserRepository::class);
        /* @var $userRepository \Shopsys\ShopBundle\Model\Customer\UserRepository */

        $orderData = new OrderData();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'Václav';
        $orderData->lastName = 'Svěrkoš';
        $orderData->email = 'no-reply@netdevelo.cz';
        $orderData->telephone = '+420725711368';
        $orderData->street = 'Devátá 25';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '14' => 1,
            ]
        );

        $user = $userRepository->findUserByEmailAndDomain('no-reply.2@netdevelo.cz', 2);
        $orderData = new OrderData();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jan';
        $orderData->lastName = 'Novák';
        $orderData->email = 'no-reply@netdevelo.cz';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'Pouliční 11';
        $orderData->city = 'Městník';
        $orderData->postcode = '12345';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->companyName = 'netdevelo s.r.o.';
        $orderData->companyNumber = '123456789';
        $orderData->companyTaxNumber = '987654321';
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = 'Karel';
        $orderData->deliveryLastName = 'Vesela';
        $orderData->deliveryCompanyName = 'Bestcompany';
        $orderData->deliveryTelephone = '+420987654321';
        $orderData->deliveryStreet = 'Zakopaná 42';
        $orderData->deliveryCity = 'Zemín';
        $orderData->deliveryPostcode = '54321';
        $orderData->deliveryCountry = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_2);
        $orderData->note = 'Prosím o dodání do pátku. Děkuji.';
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '1' => 2,
                DemoProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ],
            $user
        );

        $user = $userRepository->findUserByEmailAndDomain('no-reply.7@netdevelo.cz', 2);
        $orderData = new OrderData();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jindřich';
        $orderData->lastName = 'Němec';
        $orderData->email = 'no-reply@netdevelo.cz';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'Sídlištní 3259';
        $orderData->city = 'Orlová';
        $orderData->postcode = '65421';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '2' => 2,
                DemoProductDataFixture::PRODUCT_PREFIX . '4' => 4,
            ],
            $user
        );

        $orderData = new OrderData();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
        $orderData->firstName = 'Viktor';
        $orderData->lastName = 'Pátek';
        $orderData->email = 'no-reply@netdevelo.cz';
        $orderData->telephone = '+420888777111';
        $orderData->street = 'Vyhlídková 88';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71201';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '3' => 10,
            ]
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param array $products
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     */
    private function createOrder(
        OrderData $orderData,
        array $products,
        User $user = null
    ) {
        $orderFacade = $this->get(OrderFacade::class);
        /* @var $orderFacade \Shopsys\ShopBundle\Model\Order\OrderFacade */
        $orderPreviewFactory = $this->get(OrderPreviewFactory::class);
        /* @var $orderPreviewFactory \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory */

        $quantifiedProducts = [];
        foreach ($products as $productReferenceName => $quantity) {
            $product = $this->getReference($productReferenceName);
            $quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
        }
        $orderPreview = $orderPreviewFactory->create(
            $orderData->currency,
            $orderData->domainId,
            $quantifiedProducts,
            $orderData->transport,
            $orderData->payment,
            $user,
            null
        );

        $order = $orderFacade->createOrder($orderData, $orderPreview, $user);
        /* @var $order \Shopsys\ShopBundle\Model\Order\Order */

        $referenceName = DemoOrderDataFixture::ORDER_PREFIX . $order->getId();
        $this->addReference($referenceName, $order);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            CountryDataFixture::class,
            PaymentDataFixture::class,
            TransportDataFixture::class,
            SettingValueDataFixture::class,
        ];
    }
}
