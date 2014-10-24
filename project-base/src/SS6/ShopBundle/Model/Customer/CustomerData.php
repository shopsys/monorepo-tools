<?php

namespace SS6\ShopBundle\Model\Customer;

class CustomerData {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserData
	 */
	private $userData;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	private $billingAddressData;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 */
	private $deliveryAddressData;

	/**
	 * @var bool
	 */
	private $sendRegistrationMail;

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
	 * @return \SS6\ShopBundle\Model\Customer\UserData
	 */
	public function getUserData() {
		return $this->userData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	public function getBillingAddressData() {
		return $this->billingAddressData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddressData|null
	 */
	public function getDeliveryAddressData() {
		return $this->deliveryAddressData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 */
	public function setUserData(UserData $userData) {
		$this->userData = $userData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddressData $billingAddressData
	 */
	public function setBillingAddress(BillingAddressData $billingAddressData) {
		$this->billingAddressData = $billingAddressData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData|null $deliveryAddress
	 */
	public function setDeliveryAddress(DeliveryAddressData $deliveryAddress = null) {
		$this->deliveryAddressData = $deliveryAddress;
	}

	/**
	 * @return bool
	 */
	public function getSendRegistrationMail() {
		return $this->sendRegistrationMail;
	}

	/**
	 * @param bool $sendRegistrationMail
	 */
	public function setSendRegistrationMail($sendRegistrationMail) {
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
