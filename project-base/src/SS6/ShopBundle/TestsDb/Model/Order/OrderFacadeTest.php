<?php

namespace SS6\ShopBundle\TestsDb\Model\Order;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\OrderData;

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
		$orderData->transport = $transport;
		$orderData->payment = $payment;
		$orderData->firstName = 'firstName';
		$orderData->lastName = 'lastName';
		$orderData->email = 'email';
		$orderData->telephone = 'telephone';
		$orderData->companyCustomer = true;
		$orderData->companyName = 'companyName';
		$orderData->companyNumber = 'companyNumber';
		$orderData->companyTaxNumber = 'companyTaxNumber';
		$orderData->street = 'street';
		$orderData->city = 'city';
		$orderData->postcode = 'postcode';
		$orderData->deliveryAddressFilled = true;
		$orderData->deliveryContactPerson = 'deliveryContanctPerson';
		$orderData->deliveryCompanyName = 'deliveryCompanyName';
		$orderData->deliveryTelephone = 'deliveryTelephone';
		$orderData->deliveryStreet = 'deliveryStreet';
		$orderData->deliveryCity = 'deliveryCity';
		$orderData->deliveryPostcode = 'deliveryPostcode';
		$orderData->note = 'note';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);

		$order = $orderFacade->createOrderFromCart($orderData);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertSame($orderData->transport->getId(), $orderFromDb->getTransport()->getId());
		$this->assertSame($orderData->payment->getId(), $orderFromDb->getPayment()->getId());
		$this->assertSame($orderData->firstName, $orderFromDb->getFirstName());
		$this->assertSame($orderData->lastName, $orderFromDb->getLastName());
		$this->assertSame($orderData->email, $orderFromDb->getEmail());
		$this->assertSame($orderData->telephone, $orderFromDb->getTelephone());
		$this->assertSame($orderData->companyName, $orderFromDb->getCompanyName());
		$this->assertSame($orderData->companyNumber, $orderFromDb->getCompanyNumber());
		$this->assertSame($orderData->companyTaxNumber, $orderFromDb->getCompanyTaxNumber());
		$this->assertSame($orderData->street, $orderFromDb->getStreet());
		$this->assertSame($orderData->city, $orderFromDb->getCity());
		$this->assertSame($orderData->postcode, $orderFromDb->getPostcode());
		$this->assertSame($orderData->deliveryContactPerson, $orderFromDb->getDeliveryContactPerson());
		$this->assertSame($orderData->deliveryCompanyName, $orderFromDb->getDeliveryCompanyName());
		$this->assertSame($orderData->deliveryTelephone, $orderFromDb->getDeliveryTelephone());
		$this->assertSame($orderData->deliveryStreet, $orderFromDb->getDeliveryStreet());
		$this->assertSame($orderData->deliveryCity, $orderFromDb->getDeliveryCity());
		$this->assertSame($orderData->deliveryPostcode, $orderFromDb->getDeliveryPostcode());
		$this->assertSame($orderData->note, $orderFromDb->getNote());
		$this->assertSame($orderData->domainId, $orderFromDb->getDomainId());

		$this->assertCount(3, $orderFromDb->getItems());
	}

	public function testEdit() {
		$orderFacade = $this->getContainer()->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderRepository = $this->getContainer()->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */

		$order = $this->getReference('order_1');
		/* @var $order \SS6\ShopBundle\Model\Order\Order */

		$this->assertCount(4, $order->getItems());

		$orderData = new OrderData();
		$orderData->setFromEntity($order);

		$orderItemsData = $orderData->items;
		array_pop($orderItemsData);

		$orderItemData1 = new OrderItemData();
		$orderItemData1->name = 'itemName1';
		$orderItemData1->priceWithoutVat = 100;
		$orderItemData1->priceWithVat = 121;
		$orderItemData1->vatPercent = 21;
		$orderItemData1->quantity = 3;

		$orderItemData2 = new OrderItemData();
		$orderItemData2->name = 'itemName2';
		$orderItemData2->priceWithoutVat = 333;
		$orderItemData2->priceWithVat = 333;
		$orderItemData2->vatPercent = 0;
		$orderItemData2->quantity = 1;

		$orderItemsData['new_1'] = $orderItemData1;
		$orderItemsData['new_2'] = $orderItemData2;

		$orderData->items = $orderItemsData;
		$orderFacade->edit($order->getId(), $orderData);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertCount(5, $orderFromDb->getItems());
	}

}
