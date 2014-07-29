<?php

namespace SS6\ShopBundle\Model\Customer;

class CustomerData {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserData
	 */
	private $user;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	private $billingAddress;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 */
	private $deliveryAddress;

	public function __construct() {
		$this->user = new UserData();
		$this->billingAddress = new BillingAddressData();
		$this->deliveryAddress = new DeliveryAddressData();
	}

	public function getUser() {
		return $this->user;
	}

	public function getBillingAddress() {
		return $this->billingAddress;
	}

	public function getDeliveryAddress() {
		return $this->deliveryAddress;
	}

	public function setUser(UserData $userData) {
		$this->user = $userData;
	}

	public function setBillingAddress(BillingAddressData $billingAddressData) {
		$this->billingAddress = $billingAddressData;
	}

	public function setDeliveryAddress(DeliveryAddressData $deliveryAddress) {
		$this->deliveryAddress = $deliveryAddress;
	}

	public function setFromEntity(User $user) {
		$this->user->setFromEntity($user);
		$this->billingAddress->setFromEntity($user->getBillingAddress());
		$this->deliveryAddress->setFromEntity($user->getDeliveryAddress());
	}

}
