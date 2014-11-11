<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class PaymentData {

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	private $vat;

	/**
	 * @var array
	 */
	private $descriptions;

	/**
	 * @var array
	 */
	private $domains;

	/**
	 * @var integer
	 */
	private $hidden;

	/**
	 * @var string
	 */
	private $image;

	/**
	 * @var array
	 */
	private $transports;

	/**
	 * @param array $names
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param array $descriptions
	 * @param boolean $hidden
	 * @param array $domains
	 */
	public function __construct(
		array $names = array(),
		$price = null,
		Vat $vat = null,
		array $descriptions = array(),
		$hidden = false,
		$domains = array()
	) {
		$this->names = $names;
		$this->price = $price;
		$this->vat = $vat;
		$this->descriptions = $descriptions;
		$this->domains = $domains;
		$this->hidden = $hidden;
		$this->transports = array();
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return array
	 */
	public function getDescriptions() {
		return $this->descriptions;
	}

	/**
	 * @return array
	 */
	public function getDomains() {
		return $this->domains;
	}

	/**
	 * @return array
	 */
	public function getTransports() {
		return $this->transports;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @param array $names
	 */
	public function setNames(array $names) {
		$this->names = $names;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function setVat(Vat $vat = null) {
		$this->vat = $vat;
	}

	/**
	 * @param array $descriptions
	 */
	public function setDescriptions($descriptions) {
		$this->descriptions = $descriptions;
	}

	/**
	 * @param array $domains
	 */
	public function setDomains($domains) {
		$this->domains = $domains;
	}

	/**
	 * @param array $transports
	 */
	public function setTransports($transports) {
		$this->transports = $transports;
	}

	/**
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentDomain[] $paymentDomains
	 */
	public function setFromEntity(Payment $payment, array $paymentDomains) {
		$this->setPrice($payment->getPrice());
		$this->setVat($payment->getVat());
		$this->setHidden($payment->isHidden());
		$this->setTransports($payment->getTransports()->toArray());

		$translations = $payment->getTranslations();
		$names = array();
		$desctiptions = array();
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
			$desctiptions[$translate->getLocale()] = $translate->getDescription();
		}
		$this->setNames($names);
		$this->setDescriptions($desctiptions);

		$domains = array();
		foreach ($paymentDomains as $paymentDomain) {
			$domains[] = $paymentDomain->getDomainId();
		}
		$this->setDomains($domains);
	}
}
