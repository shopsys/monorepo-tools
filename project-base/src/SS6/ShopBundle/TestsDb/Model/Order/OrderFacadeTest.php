<?php

namespace SS6\ShopBundle\TestsDb\Model\Order;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class OrderFacadeTest extends DatabaseTestCase {

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testCreate() {
		$cart = $this->getContainer()->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		$cartService = $this->getContainer()->get('ss6.shop.cart.cart_service');
		/* @var $cart \SS6\ShopBundle\Model\Cart\CartService */
		$orderFacade = $this->getContainer()->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderRepository = $this->getContainer()->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$transportRepository = $this->getContainer()->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentRepository = $this->getContainer()->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */

		$customerIdentifier = new CustomerIdentifier('randomString');
		
		$product = $productRepository->getById(1);

		$cartService->addProductToCart($cart, $customerIdentifier, $product, 1);

		$transport = $transportRepository->getById(1);
		$payment = $paymentRepository->getById(1);

		$orderData = new OrderData();
		$orderData->setTransport($transport);
		$orderData->setPayment($payment);
		$orderData->setFirstName('firstName');
		$orderData->setLastName('lastName');
		$orderData->setEmail('email');
		$orderData->setTelephone('email');
		$orderData->setCompanyCustomer(true);
		$orderData->setCompanyName('companyName');
		$orderData->setCompanyNumber('companyNumber');
		$orderData->setCompanyTaxNumber('companyTaxNumber');
		$orderData->setStreet('street');
		$orderData->setCity('city');
		$orderData->setPostcode('postcode');
		$orderData->setDeliveryAddressFilled(true);
		$orderData->setDeliveryContactPerson('deliveryContanctPerson');
		$orderData->setDeliveryCompanyName('deliveryCompanyName');
		$orderData->setDeliveryTelephone('deliveryTelephone');
		$orderData->setDeliveryStreet('deliveryStreet');
		$orderData->setDeliveryCity('deliveryCity');
		$orderData->setDeliveryPostcode('deliveryPostcode');
		$orderData->setNote('note');
		$orderData->setDomainId(1);

		$order = $orderFacade->createOrder($orderData);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertEquals($orderData->getTransport()->getId(), $orderFromDb->getTransport()->getId());
		$this->assertEquals($orderData->getPayment()->getId(), $orderFromDb->getPayment()->getId());
		$this->assertEquals($orderData->getFirstName(), $orderFromDb->getFirstName());
		$this->assertEquals($orderData->getLastName(), $orderFromDb->getLastName());
		$this->assertEquals($orderData->getEmail(), $orderFromDb->getEmail());
		$this->assertEquals($orderData->getTelephone(), $orderFromDb->getTelephone());
		$this->assertEquals($orderData->getCompanyName(), $orderFromDb->getCompanyName());
		$this->assertEquals($orderData->getCompanyNumber(), $orderFromDb->getCompanyNumber());
		$this->assertEquals($orderData->getCompanyTaxNumber(), $orderFromDb->getCompanyTaxNumber());
		$this->assertEquals($orderData->getStreet(), $orderFromDb->getStreet());
		$this->assertEquals($orderData->getCity(), $orderFromDb->getCity());
		$this->assertEquals($orderData->getPostcode(), $orderFromDb->getPostcode());
		$this->assertEquals($orderData->getDeliveryContactPerson(), $orderFromDb->getDeliveryContactPerson());
		$this->assertEquals($orderData->getDeliveryCompanyName(), $orderFromDb->getDeliveryCompanyName());
		$this->assertEquals($orderData->getDeliveryTelephone(), $orderFromDb->getDeliveryTelephone());
		$this->assertEquals($orderData->getDeliveryStreet(), $orderFromDb->getDeliveryStreet());
		$this->assertEquals($orderData->getDeliveryCity(), $orderFromDb->getDeliveryCity());
		$this->assertEquals($orderData->getDeliveryPostcode(), $orderFromDb->getDeliveryPostcode());
		$this->assertEquals($orderData->getNote(), $orderFromDb->getNote());
		$this->assertEquals($orderData->getDomainId(), $orderFromDb->getDomainId());

		$this->assertCount(3, $orderFromDb->getItems());
	}

}
