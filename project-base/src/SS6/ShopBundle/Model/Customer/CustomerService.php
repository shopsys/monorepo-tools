<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;

class CustomerService {

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
				Condition::ifNull($user->getLastName(), $order->getLastName()),
				null,
				null,
				$user->getPricingGroup()
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
			$billingAddressData->companyCustomer = $order->getCompanyNumber() !== null;
			$billingAddressData->companyName = $order->getCompanyName();
			$billingAddressData->companyNumber = $order->getCompanyNumber();
			$billingAddressData->companyTaxNumber = $order->getCompanyTaxNumber();
			$billingAddressData->street = $order->getStreet();
			$billingAddressData->city = $order->getCity();
			$billingAddressData->postcode = $order->getPostcode();
		}

		if ($billingAddress->getTelephone() === null) {
			$billingAddressData->telephone = $order->getTelephone();
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
			$deliveryAddressData->addressFilled = !$order->isDeliveryAddressSameAsBillingAddress();
			$deliveryAddressData->street = $order->getDeliveryStreet();
			$deliveryAddressData->city = $order->getDeliveryCity();
			$deliveryAddressData->postcode = $order->getDeliveryPostcode();
			$deliveryAddressData->companyName = $order->getDeliveryCompanyName();
			$deliveryAddressData->contactPerson = $order->getDeliveryContactPerson();
			$deliveryAddressData->telephone = $order->getDeliveryTelephone();
		} else {
			$deliveryAddressData->setFromEntity($deliveryAddress);
		}

		if ($deliveryAddress !== null && $deliveryAddress->getTelephone() === null) {
			$deliveryAddressData->telephone = $order->getTelephone();
		}

		return $deliveryAddressData;
	}

}
