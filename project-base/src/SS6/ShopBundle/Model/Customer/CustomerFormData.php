<?php

namespace SS6\ShopBundle\Model\Customer;

class CustomerFormData {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserFormData
	 */
	private $user;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddressFormData
	 */
	private $billingAddress;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddressFormData
	 */
	private $deliveryAddress;

	public function __construct() {
		$this->user = new UserFormData();
		$this->billingAddress = new BillingAddressFormData();
		$this->deliveryAddress = new DeliveryAddressFormData();
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

	public function setUser(UserFormData $userFormData) {
		$this->user = $userFormData;
	}

	public function setBillingAddress(BillingAddressFormData $billingAddressFormData) {
		$this->billingAddress = $billingAddressFormData;
	}

	public function setDeliveryAddress(DeliveryAddressFormData $deliveryAddress) {
		$this->deliveryAddress = $deliveryAddress;
	}

	public function setFromEntity(User $user) {
		$this->user->setFromEntity($user);
		$this->billingAddress->setFromEntity($user->getBillingAddress());
		$this->deliveryAddress->setFromEntity($user->getDeliveryAddress());
	}

}
