<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="billing_addresses")
 * @ORM\Entity
 */
class BillingAddress {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var boolean
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
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $telephone
	 */
	public function __construct(BillingAddressFormData $billingAddressFormData) {
		$this->street = $billingAddressFormData->getStreet();
		$this->city = $billingAddressFormData->getCity();
		$this->postcode = $billingAddressFormData->getPostcode();
		$this->country = $billingAddressFormData->getCountry();
		$this->companyCustomer = $billingAddressFormData->getCompanyCustomer();
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressFormData->getCompanyName();
			$this->companyNumber = $billingAddressFormData->getCompanyNumber();
			$this->companyTaxNumber = $billingAddressFormData->getCompanyTaxNumber();
		}
		$this->telephone = $billingAddressFormData->getTelephone();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData $billingAddressFormData
	 */
	public function edit(BillingAddressFormData $billingAddressFormData) {
		$this->street = $billingAddressFormData->getStreet();
		$this->city = $billingAddressFormData->getCity();
		$this->postcode = $billingAddressFormData->getPostcode();
		$this->country = $billingAddressFormData->getCountry();
		$this->companyCustomer = $billingAddressFormData->getCompanyCustomer();
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressFormData->getCompanyName();
			$this->companyNumber = $billingAddressFormData->getCompanyNumber();
			$this->companyTaxNumber = $billingAddressFormData->getCompanyTaxNumber();
		} else {
			$this->companyName = null;
			$this->companyNumber = null;
			$this->companyTaxNumber = null;
		}
		$this->telephone = $billingAddressFormData->getTelephone();
	}

	/**
	 * @return boolean
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
