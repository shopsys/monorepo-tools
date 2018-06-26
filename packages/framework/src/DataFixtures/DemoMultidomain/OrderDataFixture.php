<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\OrderStatusDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\OrderDataFixture as DemoOrderDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PaymentDataFixture as DemoPaymentDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\TransportDataFixture as DemoTransportDataFixture;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserRepository;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;

class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface
     */
    private $orderDataFactory;

    public function __construct(
        UserRepository $userRepository,
        Generator $faker,
        OrderFacade $orderFacade,
        OrderPreviewFactory $orderPreviewFactory,
        OrderDataFactoryInterface $orderDataFactory
    ) {
        $this->userRepository = $userRepository;
        $this->faker = $faker;
        $this->orderFacade = $orderFacade;
        $this->orderPreviewFactory = $orderPreviewFactory;
        $this->orderDataFactory = $orderDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'Václav';
        $orderData->lastName = 'Svěrkoš';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420725711368';
        $orderData->street = 'Devátá 25';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $orderData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '14' => 1,
            ]
        );

        $user = $this->userRepository->findUserByEmailAndDomain('no-reply.2@shopsys.com', 2);
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jan';
        $orderData->lastName = 'Novák';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'Pouliční 11';
        $orderData->city = 'Městník';
        $orderData->postcode = '12345';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->companyName = 'shopsys s.r.o.';
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
        $orderData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '1' => 2,
                DemoProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ],
            $user
        );

        $user = $this->userRepository->findUserByEmailAndDomain('no-reply.7@shopsys.com', 2);
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jindřich';
        $orderData->lastName = 'Němec';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'Sídlištní 3259';
        $orderData->city = 'Orlová';
        $orderData->postcode = '65421';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $orderData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '2' => 2,
                DemoProductDataFixture::PRODUCT_PREFIX . '4' => 4,
            ],
            $user
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(DemoPaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
        $orderData->firstName = 'Viktor';
        $orderData->lastName = 'Pátek';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420888777111';
        $orderData->street = 'Vyhlídková 88';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71201';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = 2;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $orderData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $this->createOrder(
            $orderData,
            [
                DemoProductDataFixture::PRODUCT_PREFIX . '3' => 10,
            ]
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param array $products
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    private function createOrder(
        OrderData $orderData,
        array $products,
        User $user = null
    ) {
        $quantifiedProducts = [];
        foreach ($products as $productReferenceName => $quantity) {
            $product = $this->getReference($productReferenceName);
            $quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
        }
        $orderPreview = $this->orderPreviewFactory->create(
            $orderData->currency,
            $orderData->domainId,
            $quantifiedProducts,
            $orderData->transport,
            $orderData->payment,
            $user,
            null
        );

        $order = $this->orderFacade->createOrder($orderData, $orderPreview, $user);
        /* @var $order \Shopsys\FrameworkBundle\Model\Order\Order */

        $referenceName = DemoOrderDataFixture::ORDER_PREFIX . $order->getId();
        $this->addReference($referenceName, $order);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            CountryDataFixture::class,
            SettingValueDataFixture::class,
        ];
    }
}
