<?php

namespace Shopsys\ShopBundle\DataFixtures\Performance;

use Doctrine\ORM\EntityManager;
use Faker\Generator as Faker;
use Shopsys\ShopBundle\Component\Console\ProgressBar;
use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture;
use Shopsys\ShopBundle\DataFixtures\Performance\ProductDataFixture as PerformanceProductDataFixture;
use Shopsys\ShopBundle\DataFixtures\Performance\UserDataFixture as PerformanceUserDataFixture;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Symfony\Component\Console\Output\OutputInterface;

class OrderDataFixture
{
    const ORDERS_COUNT = 50000;
    const PRODUCTS_PER_ORDER_COUNT = 6;
    const PERCENTAGE_OF_ORDERS_BY_REGISTERED_USERS = 25;

    const BATCH_SIZE = 10;

    /**
     * @var int[]
     */
    private $performanceProductIds;

    /**
     * @var int[]
     */
    private $performanceUserIds;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade
     */
    private $sqlLoggerFacade;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    public function __construct(
        EntityManager $em,
        EntityManagerFacade $entityManagerFacade,
        SqlLoggerFacade $sqlLoggerFacade,
        Faker $faker,
        PersistentReferenceFacade $persistentReferenceFacade,
        OrderFacade $orderFacade,
        OrderPreviewFactory $orderPreviewFactory,
        ProductFacade $productFacade,
        CustomerFacade $customerFacade
    ) {
        $this->performanceProductIds = [];
        $this->em = $em;
        $this->entityManagerFacade = $entityManagerFacade;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->faker = $faker;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->orderFacade = $orderFacade;
        $this->orderPreviewFactory = $orderPreviewFactory;
        $this->productFacade = $productFacade;
        $this->customerFacade = $customerFacade;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function load(OutputInterface $output) {
        // Sql logging during mass data import makes memory leak
        $this->sqlLoggerFacade->temporarilyDisableLogging();

        $this->loadPerformanceProductIds();
        $this->loadPerformanceUserIdsOnFirstDomain();

        $progressBar = new ProgressBar($output, self::ORDERS_COUNT);
        $progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%%,%speed:6.1f% orders/s (%step_duration:.3f% s/ord.),'
            . ' Elapsed: %elapsed_hms%, Remaining: %remaining_hms%, MEM:%memory:9s%'
        );
        $progressBar->setRedrawFrequency(10);
        $progressBar->start();

        for ($orderIndex = 0; $orderIndex < self::ORDERS_COUNT; $orderIndex++) {
            $this->createOrder();

            $progressBar->advance();

            if ($orderIndex % self::BATCH_SIZE === 0) {
                $this->entityManagerFacade->clear();
            }
        }

        $progressBar->finish();

        $this->sqlLoggerFacade->reenableLogging();
    }

    private function createOrder() {
        $user = $this->getRandomUserOrNull();
        $orderData = $this->createOrderData($user);
        $quantifiedProducts = $this->createQuantifiedProducts();

        $orderPreview = $this->orderPreviewFactory->create(
            $orderData->currency,
            $orderData->domainId,
            $quantifiedProducts,
            $orderData->transport,
            $orderData->payment,
            $user,
            null
        );

        $this->orderFacade->createOrder($orderData, $orderPreview, $user);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Order\OrderData
     */
    private function createOrderData(User $user = null) {
        $orderData = new OrderData();

        if ($user !== null) {
            $orderData->firstName = $user->getFirstName();
            $orderData->lastName = $user->getLastName();
            $orderData->email = $user->getEmail();

            $billingAddress = $user->getBillingAddress();
            $orderData->telephone = $billingAddress->getTelephone();
            $orderData->street = $billingAddress->getStreet();
            $orderData->city = $billingAddress->getCity();
            $orderData->postcode = $billingAddress->getPostcode();
            $orderData->country = $billingAddress->getCountry();
            $orderData->companyName = $billingAddress->getCompanyName();
            $orderData->companyNumber = $billingAddress->getCompanyNumber();
            $orderData->companyTaxNumber = $billingAddress->getCompanyTaxNumber();
        } else {
            $orderData->firstName = $this->faker->firstName;
            $orderData->lastName = $this->faker->lastName;
            $orderData->email = $this->faker->safeEmail;
            $orderData->telephone = $this->faker->phoneNumber;
            $orderData->street = $this->faker->streetAddress;
            $orderData->city = $this->faker->city;
            $orderData->postcode = $this->faker->postcode;
            $orderData->country = $this->getRandomCountryFromFirstDomain();
            $orderData->companyName = $this->faker->company;
            $orderData->companyNumber = $this->faker->randomNumber(6);
            $orderData->companyTaxNumber = $this->faker->randomNumber(6);
        }

        $orderData->transport = $this->getRandomTransport();
        $orderData->payment = $this->getRandomPayment();
        $orderData->status = $this->persistentReferenceFacade->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = $this->faker->firstName;
        $orderData->deliveryLastName = $this->faker->lastName;
        $orderData->deliveryCompanyName = $this->faker->company;
        $orderData->deliveryTelephone = $this->faker->phoneNumber;
        $orderData->deliveryStreet = $this->faker->streetAddress;
        $orderData->deliveryCity = $this->faker->city;
        $orderData->deliveryPostcode = $this->faker->postcode;
        $orderData->deliveryCountry = $this->getRandomCountryFromFirstDomain();
        $orderData->note = $this->faker->text(200);
        $orderData->createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $orderData->domainId = 1;
        $orderData->currency = $this->persistentReferenceFacade->getReference(CurrencyDataFixture::CURRENCY_CZK);

        return $orderData;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[]
     */
    private function createQuantifiedProducts() {
        $quantifiedProducts = [];

        $randomProductIds = $this->getRandomPerformanceProductIds(self::PRODUCTS_PER_ORDER_COUNT);
        foreach ($randomProductIds as $randomProductId) {
            $product = $this->productFacade->getById($randomProductId);
            $quantity = $this->faker->numberBetween(1, 10);

            $quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
        }

        return $quantifiedProducts;
    }

    private function loadPerformanceProductIds() {
        $firstPerformaceProduct = $this->persistentReferenceFacade->getReference(
            PerformanceProductDataFixture::FIRST_PERFORMANCE_PRODUCT
        );
        /* @var $firstPerformaceProduct \Shopsys\ShopBundle\Model\Product\Product */

        $qb = $this->em->createQueryBuilder()
            ->select('p.id')
            ->from(Product::class, 'p')
            ->where('p.id >= :firstPerformanceProductId')
            ->andWhere('p.variantType != :mainVariantType')
            ->setParameter('firstPerformanceProductId', $firstPerformaceProduct->getId())
            ->setParameter('mainVariantType', Product::VARIANT_TYPE_MAIN);

        $this->performanceProductIds = array_map('array_pop', $qb->getQuery()->getResult());
    }

    /**
     * @param int $count
     * @return int[]
     */
    private function getRandomPerformanceProductIds($count) {
        return $this->faker->randomElements($this->performanceProductIds, $count);
    }

    private function loadPerformanceUserIdsOnFirstDomain() {
        $firstPerformaceUser = $this->persistentReferenceFacade->getReference(
            PerformanceUserDataFixture::FIRST_PERFORMANCE_USER
        );
        /* @var $firstPerformaceUser \Shopsys\ShopBundle\Model\Customer\User */

        $qb = $this->em->createQueryBuilder()
            ->select('u.id')
            ->from(User::class, 'u')
            ->where('u.id >= :firstPerformanceUserId')
            ->andWhere('u.domainId = :domainId')
            ->setParameter('firstPerformanceUserId', $firstPerformaceUser->getId())
            ->setParameter('domainId', 1);

        $this->performanceUserIds = array_map('array_pop', $qb->getQuery()->getResult());
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Customer\User|null
     */
    private function getRandomUserOrNull() {
        $shouldBeRegisteredUser = $this->faker->boolean(self::PERCENTAGE_OF_ORDERS_BY_REGISTERED_USERS);

        if ($shouldBeRegisteredUser) {
            $userId = $this->faker->randomElement($this->performanceUserIds);
            return $this->customerFacade->getUserById($userId);
        } else {
            return null;
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    private function getRandomTransport() {
        $randomTransportReferenceName = $this->faker->randomElement([
            TransportDataFixture::TRANSPORT_CZECH_POST,
            TransportDataFixture::TRANSPORT_PPL,
            TransportDataFixture::TRANSPORT_PERSONAL,
        ]);

        return $this->persistentReferenceFacade->getReference($randomTransportReferenceName);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
     */
    private function getRandomPayment() {
        $randomPaymentReferenceName = $this->faker->randomElement([
            PaymentDataFixture::PAYMENT_CARD,
            PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY,
            PaymentDataFixture::PAYMENT_CASH,
        ]);

        return $this->persistentReferenceFacade->getReference($randomPaymentReferenceName);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Country\Country
     */
    private function getRandomCountryFromFirstDomain() {
        $randomPaymentReferenceName = $this->faker->randomElement([
            CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1,
            CountryDataFixture::COUNTRY_SLOVAKIA_1,
        ]);

        return $this->persistentReferenceFacade->getReference($randomPaymentReferenceName);
    }
}
