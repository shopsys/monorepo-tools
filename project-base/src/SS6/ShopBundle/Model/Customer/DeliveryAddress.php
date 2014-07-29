<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="delivery_addresses")
 * @ORM\Entity
 */
class DeliveryAddress {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $companyName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	private $contactPerson;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $street;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $city;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $postcode;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $country;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $telephone;

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressFormData $deliveryAddressFormData
	 */
	public function __construct(DeliveryAddressFormData $deliveryAddressFormData) {
		$this->street = $deliveryAddressFormData->getStreet();
		$this->city = $deliveryAddressFormData->getCity();
		$this->postcode = $deliveryAddressFormData->getPostcode();
		$this->country = $deliveryAddressFormData->getCountry();
		$this->companyName = $deliveryAddressFormData->getCompanyName();
		$this->contactPerson = $deliveryAddressFormData->getContactPerson();
		$this->telephone = $deliveryAddressFormData->getTelephone();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressFormData $deliveryAddressFormData
	 */
	public function edit(DeliveryAddressFormData $deliveryAddressFormData) {
		$this->street = $deliveryAddressFormData->getStreet();
		$this->city = $deliveryAddressFormData->getCity();
		$this->postcode = $deliveryAddressFormData->getPostcode();
		$this->country = $deliveryAddressFormData->getCountry();
		$this->companyName = $deliveryAddressFormData->getCompanyName();
		$this->contactPerson = $deliveryAddressFormData->getContactPerson();
		$this->telephone = $deliveryAddressFormData->getTelephone();
	}

	/**
	 * @return string|null
	 */
	public function getCompanyName() {
		return $this->companyName;
	}

	/**
	 * @return string|null
	 */
	public function getContactPerson() {
		return $this->contactPerson;
	}

	/**
	 * @return string|null
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * @return string|null
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string|null
	 */
	public function getPostcode() {
		return $this->postcode;
	}

	/**
	 * @return string|null
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @return string|null
	 */
	public function getTelephone() {
		return $this->telephone;
	}

}
