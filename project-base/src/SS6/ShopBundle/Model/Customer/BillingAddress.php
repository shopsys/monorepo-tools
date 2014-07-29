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
	public function __construct(BillingAddressData $billingAddressData) {
		$this->street = $billingAddressData->getStreet();
		$this->city = $billingAddressData->getCity();
		$this->postcode = $billingAddressData->getPostcode();
		$this->country = $billingAddressData->getCountry();
		$this->companyCustomer = $billingAddressData->getCompanyCustomer();
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressData->getCompanyName();
			$this->companyNumber = $billingAddressData->getCompanyNumber();
			$this->companyTaxNumber = $billingAddressData->getCompanyTaxNumber();
		}
		$this->telephone = $billingAddressData->getTelephone();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $billingAddressData
	 */
	public function edit(BillingAddressData $billingAddressData) {
		$this->street = $billingAddressData->getStreet();
		$this->city = $billingAddressData->getCity();
		$this->postcode = $billingAddressData->getPostcode();
		$this->country = $billingAddressData->getCountry();
		$this->companyCustomer = $billingAddressData->getCompanyCustomer();
		if ($this->companyCustomer) {
			$this->companyName = $billingAddressData->getCompanyName();
			$this->companyNumber = $billingAddressData->getCompanyNumber();
			$this->companyTaxNumber = $billingAddressData->getCompanyTaxNumber();
		} else {
			$this->companyName = null;
			$this->companyNumber = null;
			$this->companyTaxNumber = null;
		}
		$this->telephone = $billingAddressData->getTelephone();
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
