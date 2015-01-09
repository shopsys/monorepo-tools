<?php

namespace SS6\ShopBundle\Model\Customer;

class DeliveryAddressData {

	/**
	 * @var boolean
	 */
	public $addressFilled;

	/**
	 * @var string|null
	 */
	public $companyName;

	/**
	 * @var string|null
	 */
	public $contactPerson;

	/**
	 * @var string|null
	 */
	public $telephone;

	/**
	 * @var string|null
	 */
	public $street;

	/**
	 * @var string|null
	 */
	public $city;

	/**
	 * @var string|null
	 */
	public $postcode;

	/**
	 * @param boolean $addressFilled
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $companyName
	 * @param string|null $contactPerson
	 * @param string|null $telephone
	 */
	public function __construct(
		$addressFilled = false,
		$street = null,
		$city = null,
		$postcode = null,
		$companyName = null,
		$contactPerson = null,
		$telephone = null
	) {
		$this->addressFilled = $addressFilled;
		$this->street = $street;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->companyName = $companyName;
		$this->contactPerson = $contactPerson;
		$this->telephone = $telephone;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 */
	public function setFromEntity(DeliveryAddress $deliveryAddress = null) {
		if ($deliveryAddress !== null) {
			$this->addressFilled = true;
			$this->companyName = $deliveryAddress->getCompanyName();
			$this->contactPerson = $deliveryAddress->getContactPerson();
			$this->telephone = $deliveryAddress->getTelephone();
			$this->street = $deliveryAddress->getStreet();
			$this->city = $deliveryAddress->getCity();
			$this->postcode = $deliveryAddress->getPostcode();
		} else {
			$this->addressFilled = false;
		}
	}

}
