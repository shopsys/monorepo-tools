<?php

namespace SS6\ShopBundle\TestsDb\Model\Order;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Form\Front\Order\OrderFormData;
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

		$orderFormData = new OrderFormData();
		$orderFormData->setTransport($transport);
		$orderFormData->setPayment($payment);
		$orderFormData->setFirstName('firstName');
		$orderFormData->setLastName('lastName');
		$orderFormData->setEmail('email');
		$orderFormData->setTelephone('email');
		$orderFormData->setCompanyCustomer(true);
		$orderFormData->setCompanyName('companyName');
		$orderFormData->setCompanyNumber('companyNumber');
		$orderFormData->setCompanyTaxNumber('companyTaxNumber');
		$orderFormData->setStreet('street');
		$orderFormData->setCity('city');
		$orderFormData->setPostcode('postcode');
		$orderFormData->setDeliveryAddressFilled(true);
		$orderFormData->setDeliveryFirstName('deliveryFirstName');
		$orderFormData->setDeliveryLastName('deliveryLastName');
		$orderFormData->setDeliveryCompanyName('deliveryCompanyName');
		$orderFormData->setDeliveryTelephone('deliveryTelephone');
		$orderFormData->setDeliveryStreet('deliveryStreet');
		$orderFormData->setDeliveryCity('deliveryCity');
		$orderFormData->setDeliveryPostcode('deliveryPostcode');
		$orderFormData->setNote('note');

		$order = $orderFacade->createOrder($orderFormData);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertEquals($orderFormData->getTransport()->getId(), $orderFromDb->getTransport()->getId());
		$this->assertEquals($orderFormData->getPayment()->getId(), $orderFromDb->getPayment()->getId());
		$this->assertEquals($orderFormData->getFirstName(), $orderFromDb->getFirstName());
		$this->assertEquals($orderFormData->getLastName(), $orderFromDb->getLastName());
		$this->assertEquals($orderFormData->getEmail(), $orderFromDb->getEmail());
		$this->assertEquals($orderFormData->getTelephone(), $orderFromDb->getTelephone());
		$this->assertEquals($orderFormData->getCompanyName(), $orderFromDb->getCompanyName());
		$this->assertEquals($orderFormData->getCompanyNumber(), $orderFromDb->getCompanyNumber());
		$this->assertEquals($orderFormData->getCompanyTaxNumber(), $orderFromDb->getCompanyTaxNumber());
		$this->assertEquals($orderFormData->getStreet(), $orderFromDb->getStreet());
		$this->assertEquals($orderFormData->getCity(), $orderFromDb->getCity());
		$this->assertEquals($orderFormData->getPostcode(), $orderFromDb->getPostcode());
		$this->assertEquals($orderFormData->getDeliveryFirstName(), $orderFromDb->getDeliveryFirstName());
		$this->assertEquals($orderFormData->getDeliveryLastName(), $orderFromDb->getDeliveryLastName());
		$this->assertEquals($orderFormData->getDeliveryCompanyName(), $orderFromDb->getDeliveryCompanyName());
		$this->assertEquals($orderFormData->getDeliveryTelephone(), $orderFromDb->getDeliveryTelephone());
		$this->assertEquals($orderFormData->getDeliveryStreet(), $orderFromDb->getDeliveryStreet());
		$this->assertEquals($orderFormData->getDeliveryCity(), $orderFromDb->getDeliveryCity());
		$this->assertEquals($orderFormData->getDeliveryPostcode(), $orderFromDb->getDeliveryPostcode());
		$this->assertEquals($orderFormData->getNote(), $orderFromDb->getNote());

		$this->assertCount(3, $orderFromDb->getItems());
	}

}
