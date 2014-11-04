<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

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
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Jiří');
		$orderData->setLastName('Ševčík');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420369554147');
		$orderData->setStreet('První 1');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71200');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_9' => 2,
				'product_10' => 3,
			),
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Iva');
		$orderData->setLastName('Jačková');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420367852147');
		$orderData->setStreet('Druhá 2');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71300');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_18' => 2,
				'product_19' => 1,
				'product_20' => 1,
				'product_15' => 5,
			),
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_cp'));
		$orderData->setPayment($this->getReference('payment_cod'));
		$orderData->setFirstName('Jan');
		$orderData->setLastName('Adamovský');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420725852147');
		$orderData->setStreet('Třetí 3');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71200');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_4' => 6,
				'product_11' => 1,
			),
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_ppl'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Iveta');
		$orderData->setLastName('Prvá');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420606952147');
		$orderData->setStreet('Čtvrtá 4');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('70030');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_1' => 1,
			),
			$this->getReference('order_status_in_progress'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Jana');
		$orderData->setLastName('Janíčková');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420739852148');
		$orderData->setStreet('Pátá 55');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71200');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_2' => 8,
				'product_3' => 1,
				'product_1' => 2,
			),
			$this->getReference('order_status_done'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_ppl'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Dominik');
		$orderData->setLastName('Hašek');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420721852152');
		$orderData->setStreet('Šestá 39');
		$orderData->setCity('Pardubice');
		$orderData->setPostcode('58941');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_13' => 2,
				'product_14' => 1,
				'product_15' => 1,
				'product_16' => 1,
				'product_17' => 1,
				'product_18' => 1,
			),
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Jiří');
		$orderData->setLastName('Sovák');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420755872155');
		$orderData->setStreet('Sedmá 1488');
		$orderData->setCity('Opava');
		$orderData->setPostcode('85741');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_7' => 1,
				'product_8' => 1,
				'product_12' => 2,
			),
			$this->getReference('order_status_canceled')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_cp'));
		$orderData->setPayment($this->getReference('payment_cod'));
		$orderData->setFirstName('Josef');
		$orderData->setLastName('Somr');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420369852147');
		$orderData->setStreet('Osmá 1');
		$orderData->setCity('Praha');
		$orderData->setPostcode('30258');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_1' => 6,
				'product_2' => 1,
				'product_12' => 1,
			),
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_cp'));
		$orderData->setPayment($this->getReference('payment_cod'));
		$orderData->setFirstName('Václav');
		$orderData->setLastName('Svěrkoš');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420725711368');
		$orderData->setStreet('Devátá 25');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71200');
		$orderData->setDomainId(2);
		$this->createOrder(
			$orderData,
			array(
				'product_14' => 1,
			),
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Ivan');
		$orderData->setLastName('Horník');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420755496328');
		$orderData->setStreet('Desátá 10');
		$orderData->setCity('Plzeň');
		$orderData->setPostcode('30010');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_9' => 3,
				'product_13' => 2,
			),
			$this->getReference('order_status_canceled')
		);

		$user = $userRepository->findUserByEmailAndDomain('no-reply.2@netdevelo.cz', 2);
		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Jan');
		$orderData->setLastName('Novák');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420123456789');
		$orderData->setStreet('Pouliční 11');
		$orderData->setCity('Městník');
		$orderData->setPostcode('12345');
		$orderData->setCompanyName('netdevelo s.r.o.');
		$orderData->setCompanyNumber('123456789');
		$orderData->setCompanyTaxNumber('987654321');
		$orderData->setDeliveryContactPerson('Karel Vesela');
		$orderData->setDeliveryCompanyName('Bestcompany');
		$orderData->setDeliveryTelephone('+420987654321');
		$orderData->setDeliveryStreet('Zakopaná 42');
		$orderData->setDeliveryCity('Zemín');
		$orderData->setDeliveryPostcode('54321');
		$orderData->setNote('Prosím o dodání do pátku. Děkuji.');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_1' => 2,
				'product_3' => 1,
			),
			$this->getReference('order_status_new'),
			$user
		);

		$user = $userRepository->findUserByEmailAndDomain('no-reply.7@netdevelo.cz', 2);
		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_cp'));
		$orderData->setPayment($this->getReference('payment_cod'));
		$orderData->setFirstName('Jindřich');
		$orderData->setLastName('Němec');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420123456789');
		$orderData->setStreet('Sídlištní 3259');
		$orderData->setCity('Orlová');
		$orderData->setPostcode('65421');
		$orderData->setDomainId(2);
		$this->createOrder(
			$orderData,
			array(
				'product_2' => 2,
				'product_4' => 4,
			),
			$this->getReference('order_status_new'),
			$user
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_ppl'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Adam');
		$orderData->setLastName('Bořič');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420987654321');
		$orderData->setStreet('Cihelní 5');
		$orderData->setCity('Liberec');
		$orderData->setPostcode('65421');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_3' => 1,
			),
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Evžen');
		$orderData->setLastName('Farný');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420456789123');
		$orderData->setStreet('Gagarinova 333');
		$orderData->setCity('Hodonín');
		$orderData->setPostcode('69501');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_1' => 1,
				'product_2' => 1,
				'product_3' => 1,
			),
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Ivana');
		$orderData->setLastName('Janečková');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420369852147');
		$orderData->setStreet('Kalužní 88');
		$orderData->setCity('Lednice');
		$orderData->setPostcode('69144');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_4' => 2,
				'product_3' => 1,
			),
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_cp'));
		$orderData->setPayment($this->getReference('payment_cod'));
		$orderData->setFirstName('Pavel');
		$orderData->setLastName('Novák');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420605123654');
		$orderData->setStreet('Adresní 6');
		$orderData->setCity('Opava');
		$orderData->setPostcode('72589');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_10' => 1,
				'product_20' => 4,
			),
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_ppl'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Pavla');
		$orderData->setLastName('Adámková');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+4206051836459');
		$orderData->setStreet('Výpočetni 16');
		$orderData->setCity('Praha');
		$orderData->setPostcode('30015');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_15' => 1,
				'product_18' => 1,
				'product_19' => 1,
				'product_3' => 1,
			),
			$this->getReference('order_status_done')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Adam');
		$orderData->setLastName('Žitný');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+4206051836459');
		$orderData->setStreet('Přímá 1');
		$orderData->setCity('Plzeň');
		$orderData->setPostcode('30010');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_9' => 1,
				'product_19' => 1,
				'product_6' => 1,
			),
			$this->getReference('order_status_in_progress')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_ppl'));
		$orderData->setPayment($this->getReference('payment_card'));
		$orderData->setFirstName('Radim');
		$orderData->setLastName('Svátek');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420733598748');
		$orderData->setStreet('Křivá 11');
		$orderData->setCity('Jablonec');
		$orderData->setPostcode('78952');
		$orderData->setCompanyName('BestCompanyEver, s.r.o.');
		$orderData->setCompanyNumber('555555');
		$orderData->setNote('Doufám, že vše dorazí v pořádku a co nejdříve :)');
		$orderData->setDomainId(1);
		$this->createOrder(
			$orderData,
			array(
				'product_7' => 1,
				'product_17' => 6,
				'product_9' => 1,
				'product_14' => 1,
				'product_10' => 2,
			),
			$this->getReference('order_status_new')
		);

		$orderData = new OrderData();
		$orderData->setTransport($this->getReference('transport_personal'));
		$orderData->setPayment($this->getReference('payment_cash'));
		$orderData->setFirstName('Viktor');
		$orderData->setLastName('Pátek');
		$orderData->setEmail('no-reply@netdevelo.cz');
		$orderData->setTelephone('+420888777111');
		$orderData->setStreet('Vyhlídková 88');
		$orderData->setCity('Ostrava');
		$orderData->setPostcode('71201');
		$orderData->setDomainId(2);
		$this->createOrder(
			$orderData,
			array(
				'product_3' => 10,
			),
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
		$cartFacade = $this->get('ss6.shop.cart.cart_facade');
		/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		$cartService = $this->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */
		$customerIdentifier = $this->get('ss6.shop.customer.customer_identifier');
		/* @var $customerIdentifier \SS6\ShopBundle\Model\Customer\CustomerIdentifier */
		$cartFacade->cleanCart();

		foreach ($products as $productReferenceName => $quantity) {
			$product = $this->getReference($productReferenceName);
			$cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		}

		$order = $orderFacade->createOrder($orderData, $user);
		/* @var $order \SS6\ShopBundle\Model\Order\Order */
		$order->setStatus($orderStatus);
		$referenceName = 'order_' . $order->getId();
		$this->addReference($referenceName, $order);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			ProductDataFixture::class,
			TransportDataFixture::class,
			PaymentDataFixture::class,
			UserDataFixture::class,
			OrderStatusDataFixture::class,
		);
	}

}
