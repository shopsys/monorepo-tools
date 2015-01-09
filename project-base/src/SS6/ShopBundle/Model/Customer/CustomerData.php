<?php

namespace SS6\ShopBundle\Model\Customer;

class CustomerData {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserData
	 */
	public $userData;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	public $billingAddressData;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 */
	public $deliveryAddressData;

	/**
	 * @var bool
	 */
	public $sendRegistrationMail;

	public function __construct(
		UserData $userData = null,
		BillingAddressData $billingAddressData = null,
		DeliveryAddressData $deliveryAddressData = null,
		$sendRegistrationMail = false
	) {
		if ($userData !== null) {
			$this->userData = $userData;
		} else {
			$this->userData = new UserData();
		}
		if ($billingAddressData !== null) {
			$this->billingAddressData = $billingAddressData;
		} else {
			$this->billingAddressData = new BillingAddressData();
		}
		if ($deliveryAddressData !== null) {
			$this->deliveryAddressData = $deliveryAddressData;
		} else {
			$this->deliveryAddressData = new DeliveryAddressData();
		}
		$this->sendRegistrationMail = $sendRegistrationMail;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function setFromEntity(User $user) {
		$this->userData->setFromEntity($user);
		$this->billingAddressData->setFromEntity($user->getBillingAddress());
		$this->deliveryAddressData->setFromEntity($user->getDeliveryAddress());
	}

}
