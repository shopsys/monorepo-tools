<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;

class CustomerEditService {

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \SS6\ShopBundle\Model\Customer\CustomerData
	 */
	public function getAmendedCustomerDataByOrder(User $user, Order $order) {
		$billingAddress = $user->getBillingAddress();
		$deliveryAddress = $user->getDeliveryAddress();

		return new CustomerData(
			new UserData(
				1,
				Condition::ifNull($user->getFirstName(), $order->getFirstName()),
				Condition::ifNull($user->getLastName(), $order->getLastName())
			),
			$this->getAmendedBillingAddressDataByOrder($order, $billingAddress),
			$this->getAmendedDeliveryAddressDataByOrder($order, $deliveryAddress)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	private function getAmendedBillingAddressDataByOrder(Order $order, BillingAddress $billingAddress) {
		$billingAddressData = new BillingAddressData();
		$billingAddressData->setFromEntity($billingAddress);

		if ($billingAddress->getStreet() === null) {
			$billingAddressData->setCompanyCustomer($order->getCompanyNumber() !== null);
			$billingAddressData->setCompanyName($order->getCompanyName());
			$billingAddressData->setCompanyNumber($order->getCompanyNumber());
			$billingAddressData->setCompanyTaxNumber($order->getCompanyTaxNumber());
			$billingAddressData->setStreet($order->getStreet());
			$billingAddressData->setCity($order->getCity());
			$billingAddressData->setPostcode($order->getPostcode());
		}

		if ($billingAddress->getTelephone() === null) {
			$billingAddressData->setTelephone($order->getTelephone());
		}

		return $billingAddressData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 */
	private function getAmendedDeliveryAddressDataByOrder(Order $order, DeliveryAddress $deliveryAddress = null) {
		$deliveryAddressData = new DeliveryAddressData();

		if ($deliveryAddress === null) {
			$deliveryAddressData->setAddressFilled($order->getDeliveryStreet() !== null);
			$deliveryAddressData->setStreet($order->getDeliveryStreet());
			$deliveryAddressData->setCity($order->getDeliveryCity());
			$deliveryAddressData->setPostcode($order->getDeliveryPostcode());
			$deliveryAddressData->setCompanyName($order->getDeliveryCompanyName());
			$deliveryAddressData->setContactPerson($order->getDeliveryContactPerson());
			$deliveryAddressData->setTelephone($order->getDeliveryTelephone());
		} else {
			$deliveryAddressData->setFromEntity($deliveryAddress);
		}

		if ($deliveryAddress !== null && $deliveryAddress->getTelephone() === null) {
			$deliveryAddressData->setTelephone($order->getTelephone());
		}

		return $deliveryAddressData;
	}

}
