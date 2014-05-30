<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\OrderStatusData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Payment\Payment;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		$userRepository = $this->container->get('ss6.shop.customer.user_repository');
		/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */

		$user = $userRepository->findUserByEmail('no-reply@netdevelo.cz');

		$this->createOrder(
			array(
				'product_1' => 2,
				'product_3' => 1,
			),
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_new'),
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

		$this->createOrder(
			array(
				'product_2' => 2,
				'product_4' => 4,
			),
			$this->referenceRepository->getReference('transport_cp'),
			$this->referenceRepository->getReference('payment_cod'),
			$this->referenceRepository->getReference('order_status_new'),
			'Jan',
			'Novák',
			'no-reply@netdevelo.cz',
			'+420123456789',
			'Pouliční 11',
			'Městník',
			'12345',
			$user
		);

		$this->createOrder(
			array(
				'product_3' => 1,
			),
			$this->referenceRepository->getReference('transport_ppl'),
			$this->referenceRepository->getReference('payment_card'),
			$this->referenceRepository->getReference('order_status_new'),
			'Adam',
			'Bořič',
			'adam@netdevelo.cz',
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
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_in_progress'),
			'Evžen',
			'Farný',
			'evzen@netdevelo.cz',
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
			$this->referenceRepository->getReference('transport_personal'),
			$this->referenceRepository->getReference('payment_cash'),
			$this->referenceRepository->getReference('order_status_done'),
			'Ivana',
			'Janečková',
			'janeckova@netdevelo.cz',
			'+420369852147',
			'Kalužní 88',
			'Lednice',
			'69144',
			null
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
	 * @param string|null $deliveryFirstName
	 * @param string|null $deliveryLastName
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
			$deliveryFirstName = null, $deliveryLastName = null, $deliveryCompanyName = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null,
			$note = null) {

		$orderFacade = $this->container->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

		$cartFacade = $this->container->get('ss6.shop.cart.cart_facade');
		/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */

		$cart = $this->container->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */

		$cartService = $this->container->get('ss6.shop.cart.cart_service');
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */

		$customerIdentifier = $this->container->get('ss6.shop.customer.customer_identifier');
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
		$orderFormData->setDeliveryFirstName($deliveryFirstName);
		$orderFormData->setDeliveryLastName($deliveryLastName);
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
			ProductData::class,
			TransportData::class,
			PaymentData::class,
			UserData::class,
			OrderStatusData::class,
		);
	}

}
