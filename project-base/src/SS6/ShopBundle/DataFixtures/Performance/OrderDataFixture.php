<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Doctrine\ORM\EntityManager;
use Faker\Generator as Faker;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\PaymentDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\TransportDataFixture;
use SS6\ShopBundle\DataFixtures\Performance\ProductDataFixture as PerformanceProductDataFixture;
use SS6\ShopBundle\DataFixtures\Performance\UserDataFixture as PerformanceUserDataFixture;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\QuantifiedProduct;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditFacade;

class OrderDataFixture {

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
	 * @var \SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade
	 */
	private $sqlLoggerFacade;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService
	 */
	private $persistentReferenceService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory
	 */
	private $orderPreviewFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerEditFacade
	 */
	private $customerEditFacade;

	public function __construct(
		EntityManager $em,
		SqlLoggerFacade $sqlLoggerFacade,
		Faker $faker,
		PersistentReferenceService $persistentReferenceService,
		OrderFacade $orderFacade,
		OrderPreviewFactory $orderPreviewFactory,
		ProductEditFacade $productEditFacade,
		CustomerEditFacade $customerEditFacade
	) {
		$this->performanceProductIds = [];
		$this->em = $em;
		$this->sqlLoggerFacade = $sqlLoggerFacade;
		$this->faker = $faker;
		$this->persistentReferenceService = $persistentReferenceService;
		$this->orderFacade = $orderFacade;
		$this->orderPreviewFactory = $orderPreviewFactory;
		$this->productEditFacade = $productEditFacade;
		$this->customerEditFacade = $customerEditFacade;
	}

	public function load() {
		// Sql logging during mass data import makes memory leak
		$this->sqlLoggerFacade->temporarilyDisableLogging();

		$this->loadPerformanceProductIds();
		$this->loadPerformanceUserIdsOnFirstDomain();

		for ($orderIndex = 0; $orderIndex < self::ORDERS_COUNT; $orderIndex++) {
			$this->createOrder();

			if ($orderIndex % self::BATCH_SIZE === 0) {
				$this->printProgress($orderIndex);
				$this->em->clear();
			}
		}

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
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Model\Order\OrderData
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
			$orderData->companyName = $billingAddress->getCompanyName();
			$orderData->companyNumber = $billingAddress->getCompanyNumber();
			$orderData->companyTaxNumber = $billingAddress->getCompanyTaxNumber();
		} else {
			$orderData->firstName = 'Jan';
			$orderData->lastName = 'Novák';
			$orderData->email = 'no-reply@netdevelo.cz';
			$orderData->telephone = '+420123456789';
			$orderData->street = 'Pouliční 11';
			$orderData->city = 'Městník';
			$orderData->postcode = '12345';
			$orderData->companyName = 'netdevelo s.r.o.';
			$orderData->companyNumber = '123456789';
			$orderData->companyTaxNumber = '987654321';
		}

		$orderData->transport = $this->getRandomTransport();
		$orderData->payment = $this->getRandomPayment();
		$orderData->status = $this->persistentReferenceService->getReference('order_status_done');
		$orderData->deliveryAddressSameAsBillingAddress = false;
		$orderData->deliveryContactPerson = 'Karel Vesela';
		$orderData->deliveryCompanyName = 'Bestcompany';
		$orderData->deliveryTelephone = '+420987654321';
		$orderData->deliveryStreet = 'Zakopaná 42';
		$orderData->deliveryCity = 'Zemín';
		$orderData->deliveryPostcode = '54321';
		$orderData->note = 'Prosím o dodání do pátku. Děkuji.';
		$orderData->createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
		$orderData->domainId = 1;
		$orderData->currency = $this->persistentReferenceService->getReference(CurrencyDataFixture::CURRENCY_CZK);

		return $orderData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[]
	 */
	private function createQuantifiedProducts() {
		$quantifiedProducts = [];

		$randomProductIds = $this->getRandomPerformanceProductIds(self::PRODUCTS_PER_ORDER_COUNT);
		foreach ($randomProductIds as $randomProductId) {
			$product = $this->productEditFacade->getById($randomProductId);
			$quantity = $this->faker->numberBetween(1, 10);

			$quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
		}

		return $quantifiedProducts;
	}

	private function loadPerformanceProductIds() {
		$firstPerformaceProduct = $this->persistentReferenceService->getReference(
			PerformanceProductDataFixture::FIRST_PERFORMANCE_PRODUCT
		);
		/* @var $firstPerformaceProduct \SS6\ShopBundle\Model\Product\Product */

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
		$firstPerformaceUser = $this->persistentReferenceService->getReference(
			PerformanceUserDataFixture::FIRST_PERFORMANCE_USER
		);
		/* @var $firstPerformaceUser \SS6\ShopBundle\Model\Customer\User */

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
	 * @return \SS6\ShopBundle\Model\Customer\User|null
	 */
	private function getRandomUserOrNull() {
		$shouldBeRegisteredUser = $this->faker->boolean(self::PERCENTAGE_OF_ORDERS_BY_REGISTERED_USERS);

		if ($shouldBeRegisteredUser) {
			$userId = $this->faker->randomElement($this->performanceUserIds);
			return $this->customerEditFacade->getUserById($userId);
		} else {
			return null;
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	private function getRandomTransport() {
		$randomTransportReferenceName = $this->faker->randomElement(
			TransportDataFixture::ALL_TRANSPORT_PERSISTENT_REFERECE_NAMES
		);

		return $this->persistentReferenceService->getReference($randomTransportReferenceName);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	private function getRandomPayment() {
		$randomPaymentReferenceName = $this->faker->randomElement(
			PaymentDataFixture::ALL_PAYMENT_PERSISTENT_REFERECE_NAMES
		);

		return $this->persistentReferenceService->getReference($randomPaymentReferenceName);
	}

	/**
	 * @param $orderIndex
	 */
	private function printProgress($orderIndex) {
		echo sprintf("%d/%d\r", $orderIndex, self::ORDERS_COUNT);
	}

}
