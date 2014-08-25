<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerEditService;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
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

		$userData = new UserData('firstName', 'lastName');
		$billingAddressData = new BillingAddressData(
			'street',
			'city',
			'postcode',
			'country',
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
			'deliveryCountry',
			'deliveryCompanyName',
			'deliveryContactPerson',
			'deliveryTelephone'
		);

		$billingAddress = new BillingAddress($billingAddressData);
		$deliveryAddress = new DeliveryAddress($deliveryAddressData);
		$user = new User($userData, $billingAddress, $deliveryAddress);

		$transport = new Transport(new TransportData('transportName', '0'));
		$payment = new Payment(new PaymentData('paymentName', '0'));
		$orderStatus = new OrderStatus('orderStatusName', OrderStatus::TYPE_NEW);
		$order = new Order(
			'123456',
			$transport,
			$payment,
			$orderStatus,
			'orderFirstName',
			'orderLastName',
			'order@email.com',
			'orderTelephone',
			'orderStreet',
			'orderCity',
			'orderPostcode'
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

		$this->assertEquals($userData, $customerData->getUserData());
		$this->assertEquals($billingAddressData, $customerData->getBillingAddressData());
		$this->assertEquals($deliveryAddressData, $customerData->getDeliveryAddressData());
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testGetAmendedCustomerDataByOrder() {
		$customerEditService = new CustomerEditService();

		$userData = new UserData('firstName', 'lastName');
		$billingAddressData = new BillingAddressData();

		$billingAddress = new BillingAddress($billingAddressData);
		$user = new User($userData, $billingAddress, null);

		$transport = new Transport(new TransportData('transportName', '0'));
		$payment = new Payment(new PaymentData('paymentName', '0'));
		$orderStatus = new OrderStatus('orderStatusName', OrderStatus::TYPE_NEW);
		$order = new Order(
			'123456',
			$transport,
			$payment,
			$orderStatus,
			'orderFirstName',
			'orderLastName',
			'order@email.com',
			'orderTelephone',
			'orderStreet',
			'orderCity',
			'orderPostcode'
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
			null,
			$order->getDeliveryCompanyName(),
			$order->getDeliveryContactPerson(),
			$order->getDeliveryTelephone()
		);

		$customerData = $customerEditService->getAmendedCustomerDataByOrder($user, $order);

		$this->assertEquals($userData, $customerData->getUserData());
		$this->assertEquals($deliveryAddressData, $customerData->getDeliveryAddressData());
		$this->assertTrue($customerData->getBillingAddressData()->getCompanyCustomer());
		$this->assertEquals($order->getCompanyName(), $customerData->getBillingAddressData()->getCompanyName());
		$this->assertEquals($order->getCompanyNumber(), $customerData->getBillingAddressData()->getCompanyNumber());
		$this->assertEquals($order->getCompanyTaxNumber(), $customerData->getBillingAddressData()->getCompanyTaxNumber());
		$this->assertEquals($order->getStreet(), $customerData->getBillingAddressData()->getStreet());
		$this->assertEquals($order->getCity(), $customerData->getBillingAddressData()->getCity());
		$this->assertEquals($order->getPostcode(), $customerData->getBillingAddressData()->getPostcode());
		$this->assertEquals(null, $customerData->getBillingAddressData()->getCountry());
		$this->assertEquals($order->getTelephone(), $customerData->getBillingAddressData()->getTelephone());
	}

}
