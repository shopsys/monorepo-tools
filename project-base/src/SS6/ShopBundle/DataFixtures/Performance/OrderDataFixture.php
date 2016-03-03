<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Doctrine\ORM\EntityManager;
use Faker\Generator as Faker;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use SS6\ShopBundle\Model\Order\Item\QuantifiedProduct;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory;

class OrderDataFixture {

	const ORDERS_COUNT = 50000;
	const BATCH_SIZE = 10;

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

	public function __construct(
		EntityManager $em,
		SqlLoggerFacade $sqlLoggerFacade,
		Faker $faker,
		PersistentReferenceService $persistentReferenceService,
		OrderFacade $orderFacade,
		OrderPreviewFactory $orderPreviewFactory
	) {
		$this->em = $em;
		$this->sqlLoggerFacade = $sqlLoggerFacade;
		$this->faker = $faker;
		$this->persistentReferenceService = $persistentReferenceService;
		$this->orderFacade = $orderFacade;
		$this->orderPreviewFactory = $orderPreviewFactory;
	}

	public function load() {
		// Sql logging during mass data import makes memory leak
		$this->sqlLoggerFacade->temporarilyDisableLogging();

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
		$user = null;
		$orderData = $this->createOrderData();
		$products = [
				DemoProductDataFixture::PRODUCT_PREFIX . '1' => 2,
				DemoProductDataFixture::PRODUCT_PREFIX . '3' => 1,
		];

		$quantifiedProducts = [];
		foreach ($products as $productReferenceName => $quantity) {
			$product = $this->persistentReferenceService->getReference($productReferenceName);
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

		$this->orderFacade->createOrder($orderData, $orderPreview, $user);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\OrderData
	 */
	private function createOrderData() {
		$orderData = new OrderData();
		$orderData->transport = $this->persistentReferenceService->getReference('transport_personal');
		$orderData->payment = $this->persistentReferenceService->getReference('payment_cash');
		$orderData->status = $this->persistentReferenceService->getReference('order_status_done');
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
	 * @param $orderIndex
	 */
	private function printProgress($orderIndex) {
		echo sprintf("%d/%d\r", $orderIndex, self::ORDERS_COUNT);
	}

}
