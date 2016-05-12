<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Country\Country;

/**
 * @ORM\Table(name="delivery_addresses")
 * @ORM\Entity
 */
class DeliveryAddress {

	/**
	 * @var int
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
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	private $telephone;

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country|null
	 * @ORM\ManyToOne(targetEntity="\SS6\ShopBundle\Model\Country\Country")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
	 */
	private $country;

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
	 */
	public function __construct(DeliveryAddressData $deliveryAddressData) {
		$this->street = $deliveryAddressData->street;
		$this->city = $deliveryAddressData->city;
		$this->postcode = $deliveryAddressData->postcode;
		$this->companyName = $deliveryAddressData->companyName;
		$this->contactPerson = $deliveryAddressData->contactPerson;
		$this->telephone = $deliveryAddressData->telephone;
		$this->country = $deliveryAddressData->country;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
	 */
	public function edit(DeliveryAddressData $deliveryAddressData) {
		$this->street = $deliveryAddressData->street;
		$this->city = $deliveryAddressData->city;
		$this->postcode = $deliveryAddressData->postcode;
		$this->companyName = $deliveryAddressData->companyName;
		$this->contactPerson = $deliveryAddressData->contactPerson;
		$this->telephone = $deliveryAddressData->telephone;
		$this->country = $deliveryAddressData->country;
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
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Country\Country|null
	 */
	public function getCountry() {
		return $this->country;
	}

}
