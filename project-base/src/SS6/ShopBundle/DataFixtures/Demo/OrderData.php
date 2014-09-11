<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\OrderStatusData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderData extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		$userRepository = $this->get('ss6.shop.customer.user_repository');
		/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */

		$user = $userRepository->findUserByEmail('no-reply@netdevelo.cz');
		$this->createOrder(
			array(
				'product_9' => 2,
				'product_10' => 3,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_done'),
			'Jiří',
			'3evčík',
			'no-reply@netdevelo.cz',
			'+420369554147',
			'První 1',
			'Ostrava',
			'71200',
			$user
		);

		$this->createOrder(
			array(
				'product_18' => 2,
				'product_19' => 1,
				'product_20' => 1,
				'product_30' => 5,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_card'),
			$this->referenceRepository->getReference('order_status_new'),
			'Iva',
			'Jačková',
			'no-reply@netdevelo.cz',
			'+420367852147',
			'Druhá 2',
			'Ostrava',
			'71300',
			$user
		);

		$this->createOrder(
			array(
				'product_44' => 6,
				'product_34' => 1,
			),
			$this->referenceRepository->getReference('transport_cp'),
			$this->referenceRepository->getReference('payment_cod'),
			$this->referenceRepository->getReference('order_status_done'),
			'Jan',
			'Adamovský',
			'no-reply@netdevelo.cz',
			'+420725852147',
			'Třetí 3',
			'Ostrava',
			'71200',
			$user
		);

		$this->createOrder(
			array(
				'product_41' => 1,
			),
			$this->referenceRepository->getReference('transport_ppl'),
			$this->referenceRepository->getReference('payment_card'),
			$this->referenceRepository->getReference('order_status_in_progress'),
			'Iveta',
			'Prvá',
			'no-reply@netdevelo.cz',
			'+420606952147',
			'Čtvrtá 4',
			'Ostrava',
			'70030',
			$user
		);

		$this->createOrder(
			array(
				'product_42' => 8,
				'product_3' => 1,
				'product_1' => 2,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_done'),
			'Jana',
			'Janíčková',
			'no-reply@netdevelo.cz',
			'+420739852148',
			'Pátá 55',
			'Ostrava',
			'71200',
			$user
		);

		$this->createOrder(
			array(
				'product_13' => 2,
				'product_14' => 1,
				'product_15' => 1,
				'product_16' => 1,
				'product_17' => 1,
				'product_18' => 1,
			),
			$this->referenceRepository->getReference('transport_ppl'),
			$this->referenceRepository->getReference('payment_card'),
			$this->referenceRepository->getReference('order_status_new'),
			'Dominik',
			'Hašek',
			'no-reply@netdevelo.cz',
			'+420721852152',
			'Šestá 39',
			'Pardubice',
			'58941',
			$user
		);

		$this->createOrder(
			array(
				'product_7' => 1,
				'product_36' => 1,
				'product_22' => 2,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_canceled'),
			'Jiří',
			'Sovák',
			'no-reply@netdevelo.cz',
			'+420755872155',
			'Sedmá 1488',
			'Opava',
			'85741',
			null
		);

		$this->createOrder(
			array(
				'product_1' => 6,
				'product_2' => 1,
				'product_12' => 1,
			),
			$this->referenceRepository->getReference('transport_cp'),
			$this->referenceRepository->getReference('payment_cod'),
			$this->referenceRepository->getReference('order_status_done'),
			'Josef',
			'Somr',
			'no-reply@netdevelo.cz',
			'+420369852147',
			'Osmá 1',
			'Praha',
			'30258',
			null
		);

		$this->createOrder(
			array(
				'product_24' => 1,
			),
			$this->referenceRepository->getReference('transport_ppl'),
			$this->referenceRepository->getReference('payment_card'),
			$this->referenceRepository->getReference('order_status_in_progress'),
			'Jaromír',
			'Jágr',
			'no-reply@netdevelo.cz',
			'+420725711368',
			'Devátá 68',
			'Kladno',
			'69174',
			null
		);

		$this->createOrder(
			array(
				'product_49' => 3,
				'product_31' => 2,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_canceled'),
			'Ivan',
			'Horník',
			'no-reply@netdevelo.cz',
			'+420755496328',
			'Desátá 10',
			'Plzeň',
			'30001',
			null
		);

		$user = $userRepository->findUserByEmail('no-reply.1@netdevelo.cz');

		$this->createOrder(
			array(
				'product_1' => 2,
				'product_3' => 1,
			),
			$this->getReference('transport_personal'),
			$this->getReference('payment_cash'),
			$this->getReference('order_status_new'),
			'Jan',
			'Novák',
			'no-reply@netdevelo.cz',
			'+420123456789',
			'Pouliční 11',
			'Městník',
			'12345',
			$user,
			'netdevelo s.r.o.',
			'123456789',
			'987654321',
			'Karel',
			'Veselý',
			'Bestcompany',
			'+420987654321',
			'Zakopaná 42',
			'Zemín',
			'54321',
			'Prosím o dodání do pátku. Děkuji.'
		);

		$user = $userRepository->findUserByEmail('no-reply.7@netdevelo.cz');
		$this->createOrder(
			array(
				'product_2' => 2,
				'product_4' => 4,
			),
			$this->getReference('transport_cp'),
			$this->getReference('payment_cod'),
			$this->getReference('order_status_new'),
			'Jindřich',
			'Němec',
			'no-reply@netdevelo.cz',
			'+420725651245',
			'Sídlištní 3259',
			'Orlová',
			'65432',
			$user
		);

		$this->createOrder(
			array(
				'product_3' => 1,
			),
			$this->getReference('transport_ppl'),
			$this->getReference('payment_card'),
			$this->getReference('order_status_new'),
			'Adam',
			'Bořič',
			'no-reply@netdevelo.cz',
			'+420987654321',
			'Cihelní 5',
			'Damašek',
			'99999',
			null
		);

		$this->createOrder(
			array(
				'product_1' => 1,
				'product_2' => 1,
				'product_3' => 1,
			),
			$this->getReference('transport_personal'),
			$this->getReference('payment_cash'),
			$this->getReference('order_status_in_progress'),
			'Evžen',
			'Farný',
			'no-reply@netdevelo.cz',
			'+420456789123',
			'Gagarinova 333',
			'Hodonín',
			'69501',
			null
		);

		$this->createOrder(
			array(
				'product_4' => 2,
				'product_3' => 1,
			),
			$this->getReference('transport_personal'),
			$this->getReference('payment_cash'),
			$this->getReference('order_status_done'),
			'Ivana',
			'Janečková',
			'no-reply@netdevelo.cz',
			'+420369852147',
			'Kalužní 88',
			'Lednice',
			'69144',
			null
		);
		
		$this->createOrder(
			array(
				'product_10' => 1,
				'product_20' => 4,
			),
			$this->getReference('transport_cp'),
			$this->getReference('payment_cod'),
			$this->getReference('order_status_new'),
			'Pavel',
			'Novák',
			'no-reply@netdevelo.cz',
			'+420605123654',
			'Adresní 6',
			'Opava',
			'72589'
		);

		$this->createOrder(
			array(
				'product_15' => 1,
				'product_18' => 1,
				'product_29' => 1,
				'product_30' => 1,
			),
			$this->getReference('transport_ppl'),
			$this->getReference('payment_card'),
			$this->getReference('order_status_done'),
			'Pavla',
			'Adámková',
			'no-reply@netdevelo.cz',
			'+4206051836459',
			'Výpočetni 16',
			'Praha',
			'30015'
		);

		$this->createOrder(
			array(
				'product_9' => 1,
				'product_19' => 1,
				'product_26' => 1,
			),
			$this->getReference('transport_personal'),
			$this->getReference('payment_cash'),
			$this->getReference('order_status_in_progress'),
			'Adam',
			'Žitný',
			'no-reply@netdevelo.cz',
			'+4206051836459',
			'Přímá 1',
			'Plzeň',
			'30010'
		);

		$this->createOrder(
			array(
				'product_7' => 1,
				'product_17' => 6,
				'product_27' => 1,
				'product_37' => 1,
				'product_47' => 2,
			),
			$this->getReference('transport_ppl'),
			$this->getReference('payment_card'),
			$this->getReference('order_status_new'),
			'Radim',
			'Svátek',
			'no-reply@netdevelo.cz',
			'+420733598748',
			'Křivá 11',
			'Jablonec',
			'78952',
			null,
			'BestCompanyEver, s.r.o.',
			'555555',
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			'Doufám, že vše dorazí v pořádku a co nejdříve :)'
		);

		$this->createOrder(
			array(
				'product_33' => 10,
			),
			$this->getReference('transport_personal'),
			$this->getReference('payment_cash'),
			$this->getReference('order_status_canceled'),
			'Viktor',
			'Pátek',
			'no-reply@netdevelo.cz',
			'+420888777',
			'Vyhlídková 88',
			'Ostrava',
			'71201'
		);
		
		$manager->flush();
	}
	
	/**
	 * 
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param array $productReferenceNames
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $telephone
	 * @param string $street
	 * @param string $city
	 * @param string $postcode
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $note
	 */
	private function createOrder(array $products,
			Transport $transport, Payment $payment,	OrderStatus $orderStatus,
			$firstName, $lastName, $email, $telephone, $street, $city, $postcode,
			User $user = null, $companyName = null,	$companyNumber = null, $companyTaxNumber = null,
			$deliveryContactPerson = null, $deliveryCompanyName = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null,
			$note = null) {

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

		$orderFormData = new \SS6\ShopBundle\Form\Front\Order\OrderFormData();
		$orderFormData->setTransport($transport);
		$orderFormData->setPayment($payment);
		$orderFormData->setFirstName($firstName);
		$orderFormData->setLastName($lastName);
		$orderFormData->setEmail($email);
		$orderFormData->setTelephone($telephone);
		$orderFormData->setStreet($street);
		$orderFormData->setCity($city);
		$orderFormData->setPostcode($postcode);
		$orderFormData->setCompanyName($companyName);
		$orderFormData->setCompanyNumber($companyNumber);
		$orderFormData->setCompanyTaxNumber($companyTaxNumber);
		$orderFormData->setDeliveryContactPerson($deliveryContactPerson);
		$orderFormData->setDeliveryCompanyName($deliveryCompanyName);
		$orderFormData->setDeliveryTelephone($deliveryTelephone);
		$orderFormData->setDeliveryStreet($deliveryStreet);
		$orderFormData->setDeliveryCity($deliveryCity);
		$orderFormData->setDeliveryPostcode($deliveryPostcode);
		$orderFormData->setNote($note);

		$cartFacade->cleanCart();

		foreach ($products as $productReferenceName => $quantity) {
			$product = $this->getReference($productReferenceName);
			$cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		}

		$order = $orderFacade->createOrder($orderFormData, $user);
		$order->setStatus($orderStatus);
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
			OrderStatusData::class,
		);
	}

}
