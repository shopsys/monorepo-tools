<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerEditService;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class CustomerEditServiceTest extends PHPUnit_Framework_TestCase {

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testGetAmendedCustomerDataByOrderWithoutChanges() {
		$customerEditService = new CustomerEditService();

		$userData = new UserData();
		$userData->firstName = 'firstName';
		$userData->lastName = 'lastName';
		$billingAddressData = new BillingAddressData(
			'street',
			'city',
			'postcode',
			true,
			'companyName',
			'companyNumber',
			'companyTaxNumber',
			'telephone'
		);
		$deliveryAddressData = new DeliveryAddressData(
			true,
			'deliveryStreet',
			'deliveryCity',
			'deliveryPostcode',
			'deliveryCompanyName',
			'deliveryContactPerson',
			'deliveryTelephone'
		);

		$billingAddress = new BillingAddress($billingAddressData);
		$deliveryAddress = new DeliveryAddress($deliveryAddressData);
		$user = new User($userData, $billingAddress, $deliveryAddress);

		$transport = new Transport(new TransportData(['cs' => 'transportName']));
		$payment = new Payment(new PaymentData(['cs' => 'paymentName']));
		$orderStatus = new OrderStatus(new OrderStatusData(['en' => 'orderStatusName']), OrderStatus::TYPE_NEW);
		$orderData = new OrderData();
		$orderData->transport = $transport;
		$orderData->payment = $payment;
		$orderData->firstName = 'orderFirstName';
		$orderData->lastName = 'orderLastName';
		$orderData->email = 'order@email.com';
		$orderData->telephone = 'orderTelephone';
		$orderData->street = 'orderStreet';
		$orderData->city = 'orderCity';
		$orderData->postcode = 'orderPostcode';
		$order = new Order(
			$orderData,
			'123456',
			$orderStatus,
			'7ebafe9fe'
		);
		$order->setCompanyInfo(
			'companyName',
			'companyNumber',
			'companyTaxNumber'
		);
		$order->setDeliveryAddress(
			'orderDeliveryContactPerson',
			'orderDeliveryCompanyName',
			'orderDeliveryTelephone',
			'orderDeliveryStreet',
			'orderDeliveryCity',
			'orderDeliveryPostcode'
		);

		$customerData = $customerEditService->getAmendedCustomerDataByOrder($user, $order);

		$this->assertEquals($userData, $customerData->userData);
		$this->assertEquals($billingAddressData, $customerData->billingAddressData);
		$this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testGetAmendedCustomerDataByOrder() {
		$customerEditService = new CustomerEditService();

		$userData = new UserData();
		$userData->firstName = 'firstName';
		$userData->lastName = 'lastName';
		$billingAddressData = new BillingAddressData();

		$billingAddress = new BillingAddress($billingAddressData);
		$user = new User($userData, $billingAddress, null);

		$transport = new Transport(new TransportData(['cs' => 'transportName']));
		$payment = new Payment(new PaymentData(['cs' => 'paymentName']));
		$orderStatus = new OrderStatus(new OrderStatusData(['en' => 'orderStatusName']), OrderStatus::TYPE_NEW);
		$orderData = new OrderData();
		$orderData->transport = $transport;
		$orderData->payment = $payment;
		$orderData->firstName = 'orderFirstName';
		$orderData->lastName = 'orderLastName';
		$orderData->email = 'order@email.com';
		$orderData->telephone = 'orderTelephone';
		$orderData->street = 'orderStreet';
		$orderData->city = 'orderCity';
		$orderData->postcode = 'orderPostcode';
		$order = new Order(
			$orderData,
			'123456',
			$orderStatus,
			'7eba123456fe9fe'
		);
		$order->setCompanyInfo(
			'companyName',
			'companyNumber',
			'companyTaxNumber'
		);
		$order->setDeliveryAddress(
			'orderDeliveryContactPerson',
			'orderDeliveryCompanyName',
			'orderDeliveryTelephone',
			'orderDeliveryStreet',
			'orderDeliveryCity',
			'orderDeliveryPostcode'
		);

		$deliveryAddressData = new DeliveryAddressData(
			true,
			$order->getDeliveryStreet(),
			$order->getDeliveryCity(),
			$order->getDeliveryPostcode(),
			$order->getDeliveryCompanyName(),
			$order->getDeliveryContactPerson(),
			$order->getDeliveryTelephone()
		);

		$customerData = $customerEditService->getAmendedCustomerDataByOrder($user, $order);

		$this->assertEquals($userData, $customerData->userData);
		$this->assertEquals($deliveryAddressData, $customerData->deliveryAddressData);
		$this->assertTrue($customerData->billingAddressData->companyCustomer);
		$this->assertSame($order->getCompanyName(), $customerData->billingAddressData->companyName);
		$this->assertSame($order->getCompanyNumber(), $customerData->billingAddressData->companyNumber);
		$this->assertSame($order->getCompanyTaxNumber(), $customerData->billingAddressData->companyTaxNumber);
		$this->assertSame($order->getStreet(), $customerData->billingAddressData->street);
		$this->assertSame($order->getCity(), $customerData->billingAddressData->city);
		$this->assertSame($order->getPostcode(), $customerData->billingAddressData->postcode);
		$this->assertSame($order->getTelephone(), $customerData->billingAddressData->telephone);
	}

}
