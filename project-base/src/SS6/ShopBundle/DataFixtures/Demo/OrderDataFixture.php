<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\QuantifiedItem;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const ORDER_PREFIX = 'order_';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		$userRepository = $this->get('ss6.shop.customer.user_repository');
		/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */

		$user = $userRepository->findUserByEmailAndDomain('no-reply@netdevelo.cz', 1);
		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Jiří';
		$orderData->lastName = 'Ševčík';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369554147';
		$orderData->street = 'První 1';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_9' => 2,
				'product_10' => 3,
			],
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Iva';
		$orderData->lastName = 'Jačková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420367852147';
		$orderData->street = 'Druhá 2';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71300';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_18' => 2,
				'product_19' => 1,
				'product_20' => 1,
				'product_15' => 5,
			],
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_cp');
		$orderData->payment = $this->getReference('payment_cod');
		$orderData->firstName = 'Jan';
		$orderData->lastName = 'Adamovský';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420725852147';
		$orderData->street = 'Třetí 3';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_4' => 6,
				'product_11' => 1,
			],
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_ppl');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Iveta';
		$orderData->lastName = 'Prvá';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420606952147';
		$orderData->street = 'Čtvrtá 4';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '70030';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_1' => 1,
			],
			$this->getReference('order_status_in_progress'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Jana';
		$orderData->lastName = 'Janíčková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420739852148';
		$orderData->street = 'Pátá 55';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_2' => 8,
				'product_3' => 1,
				'product_1' => 2,
			],
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_ppl');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Dominik';
		$orderData->lastName = 'Hašek';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420721852152';
		$orderData->street = 'Šestá 39';
		$orderData->city = 'Pardubice';
		$orderData->postcode = '58941';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_13' => 2,
				'product_14' => 1,
				'product_15' => 1,
				'product_16' => 1,
				'product_17' => 1,
				'product_18' => 1,
			],
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Jiří';
		$orderData->lastName = 'Sovák';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420755872155';
		$orderData->street = 'Sedmá 1488';
		$orderData->city = 'Opava';
		$orderData->postcode = '85741';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_7' => 1,
				'product_8' => 1,
				'product_12' => 2,
			],
			$this->getReference('order_status_canceled')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_cp');
		$orderData->payment = $this->getReference('payment_cod');
		$orderData->firstName = 'Josef';
		$orderData->lastName = 'Somr';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369852147';
		$orderData->street = 'Osmá 1';
		$orderData->city = 'Praha';
		$orderData->postcode = '30258';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_1' => 6,
				'product_2' => 1,
				'product_12' => 1,
			],
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_cp');
		$orderData->payment = $this->getReference('payment_cod');
		$orderData->firstName = 'Václav';
		$orderData->lastName = 'Svěrkoš';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420725711368';
		$orderData->street = 'Devátá 25';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->domainId = 2;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
		$this->createOrder(
			$orderData,
			[
				'product_14' => 1,
			],
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Ivan';
		$orderData->lastName = 'Horník';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420755496328';
		$orderData->street = 'Desátá 10';
		$orderData->city = 'Plzeň';
		$orderData->postcode = '30010';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_9' => 3,
				'product_13' => 2,
			],
			$this->getReference('order_status_canceled')
		);

		$user = $userRepository->findUserByEmailAndDomain('no-reply.2@netdevelo.cz', 2);
		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Jan';
		$orderData->lastName = 'Novák';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420123456789';
		$orderData->street = 'Pouliční 11';
		$orderData->city = 'Městník';
		$orderData->postcode = '12345';
		$orderData->companyCustomer = true;
		$orderData->companyName = 'netdevelo s.r.o.';
		$orderData->companyNumber = '123456789';
		$orderData->companyTaxNumber = '987654321';
		$orderData->deliveryContactPerson = 'Karel Vesela';
		$orderData->deliveryAddressFilled = true;
		$orderData->deliveryCompanyName = 'Bestcompany';
		$orderData->deliveryTelephone = '+420987654321';
		$orderData->deliveryStreet = 'Zakopaná 42';
		$orderData->deliveryCity = 'Zemín';
		$orderData->deliveryPostcode = '54321';
		$orderData->note = 'Prosím o dodání do pátku. Děkuji.';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_1' => 2,
				'product_3' => 1,
			],
			$this->getReference('order_status_new'),
			$user
		);

		$user = $userRepository->findUserByEmailAndDomain('no-reply.7@netdevelo.cz', 2);
		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_cp');
		$orderData->payment = $this->getReference('payment_cod');
		$orderData->firstName = 'Jindřich';
		$orderData->lastName = 'Němec';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420123456789';
		$orderData->street = 'Sídlištní 3259';
		$orderData->city = 'Orlová';
		$orderData->postcode = '65421';
		$orderData->domainId = 2;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
		$this->createOrder(
			$orderData,
			[
				'product_2' => 2,
				'product_4' => 4,
			],
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_ppl');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Adam';
		$orderData->lastName = 'Bořič';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420987654321';
		$orderData->street = 'Cihelní 5';
		$orderData->city = 'Liberec';
		$orderData->postcode = '65421';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_3' => 1,
			],
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Evžen';
		$orderData->lastName = 'Farný';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420456789123';
		$orderData->street = 'Gagarinova 333';
		$orderData->city = 'Hodonín';
		$orderData->postcode = '69501';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_1' => 1,
				'product_2' => 1,
				'product_3' => 1,
			],
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Ivana';
		$orderData->lastName = 'Janečková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369852147';
		$orderData->street = 'Kalužní 88';
		$orderData->city = 'Lednice';
		$orderData->postcode = '69144';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_4' => 2,
				'product_3' => 1,
			],
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_cp');
		$orderData->payment = $this->getReference('payment_cod');
		$orderData->firstName = 'Pavel';
		$orderData->lastName = 'Novák';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420605123654';
		$orderData->street = 'Adresní 6';
		$orderData->city = 'Opava';
		$orderData->postcode = '72589';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_10' => 1,
				'product_20' => 4,
			],
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_ppl');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Pavla';
		$orderData->lastName = 'Adámková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+4206051836459';
		$orderData->street = 'Výpočetni 16';
		$orderData->city = 'Praha';
		$orderData->postcode = '30015';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_15' => 1,
				'product_18' => 1,
				'product_19' => 1,
				'product_3' => 1,
			],
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Adam';
		$orderData->lastName = 'Žitný';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+4206051836459';
		$orderData->street = 'Přímá 1';
		$orderData->city = 'Plzeň';
		$orderData->postcode = '30010';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_9' => 1,
				'product_19' => 1,
				'product_6' => 1,
			],
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_ppl');
		$orderData->payment = $this->getReference('payment_card');
		$orderData->firstName = 'Radim';
		$orderData->lastName = 'Svátek';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420733598748';
		$orderData->street = 'Křivá 11';
		$orderData->city = 'Jablonec';
		$orderData->postcode = '78952';
		$orderData->companyCustomer = true;
		$orderData->companyName = 'BestCompanyEver, s.r.o.';
		$orderData->companyNumber = '555555';
		$orderData->note = 'Doufám, že vše dorazí v pořádku a co nejdříve :)';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				'product_7' => 1,
				'product_17' => 6,
				'product_9' => 1,
				'product_14' => 1,
				'product_10' => 2,
			],
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference('transport_personal');
		$orderData->payment = $this->getReference('payment_cash');
		$orderData->firstName = 'Viktor';
		$orderData->lastName = 'Pátek';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420888777111';
		$orderData->street = 'Vyhlídková 88';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71201';
		$orderData->domainId = 2;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
		$this->createOrder(
			$orderData,
			[
				'product_3' => 10,
			],
			$this->getReference('order_status_canceled')
		);

		$manager->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param array $products
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function createOrder(
		OrderData $orderData,
		array $products,
		OrderStatus $orderStatus,
		User $user = null
	) {
		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

		$quantifiedItems = [];
		foreach ($products as $productReferenceName => $quantity) {
			$product = $this->getReference($productReferenceName);
			$quantifiedItems[] = new QuantifiedItem($product, $quantity);
		}

		$order = $orderFacade->createOrder($orderData, $quantifiedItems, $user);
		/* @var $order \SS6\ShopBundle\Model\Order\Order */
		$order->setStatus($orderStatus);
		$referenceName = self::ORDER_PREFIX . $order->getId();
		$this->addReference($referenceName, $order);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
			TransportDataFixture::class,
			PaymentDataFixture::class,
			UserDataFixture::class,
			OrderStatusDataFixture::class,
		];
	}

}
