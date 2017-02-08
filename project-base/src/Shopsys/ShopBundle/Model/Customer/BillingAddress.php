<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Country\Country;

/**
 * @ORM\Table(name="billing_addresses")
 * @ORM\Entity
 */
class BillingAddress {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private $companyCustomer;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $companyName;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $companyNumber;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $companyTaxNumber;

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
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Country\Country")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
	 */
	private $country;

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddressData $billingAddressData
	 */
	public function __construct(BillingAddressData $billingAddressData) {
		$this->street = $billingAddressData->street;
		$this->city = $billingAddressData->city;
		$this->postcode = $billingAddressData->postcode;
		$this->companyCustomer = $billingAddressData->companyCustomer;
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressData->companyName;
			$this->companyNumber = $billingAddressData->companyNumber;
			$this->companyTaxNumber = $billingAddressData->companyTaxNumber;
		}
		$this->telephone = $billingAddressData->telephone;
		$this->country = $billingAddressData->country;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddressData $billingAddressData
	 */
	public function edit(BillingAddressData $billingAddressData) {
		$this->street = $billingAddressData->street;
		$this->city = $billingAddressData->city;
		$this->postcode = $billingAddressData->postcode;
		$this->companyCustomer = $billingAddressData->companyCustomer;
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressData->companyName;
			$this->companyNumber = $billingAddressData->companyNumber;
			$this->companyTaxNumber = $billingAddressData->companyTaxNumber;
		} else {
			$this->companyName = null;
			$this->companyNumber = null;
			$this->companyTaxNumber = null;
		}
		$this->telephone = $billingAddressData->telephone;
		$this->country = $billingAddressData->country;
	}

	/**
	 * @return bool
	 */
	public function isCompanyCustomer() {
		return $this->companyCustomer;
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
	public function getCompanyNumber() {
		return $this->companyNumber;
	}

	/**
	 * @return string|null
	 */
	public function getCompanyTaxNumber() {
		return $this->companyTaxNumber;
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
