<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class PaymentData {

	/**
	 * @var string[]
	 */
	private $name;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	private $vat;

	/**
	 * @var string[]
	 */
	private $description;

	/**
	 * @var int[]
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
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @param string[] $name
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param string[] $description
	 * @param boolean $hidden
	 * @param int[] $domains
	 */
	public function __construct(
		array $name = [],
		$price = null,
		Vat $vat = null,
		array $description = [],
		$hidden = false,
		array $domains = []
	) {
		$this->name = $name;
		$this->price = $price;
		$this->vat = $vat;
		$this->description = $description;
		$this->domains = $domains;
		$this->hidden = $hidden;
		$this->transports = [];
	}

	/**
	 * @return string[]
	 */
	public function getName() {
		return $this->name;
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
	 * @return string[]
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return int[]
	 */
	public function getDomains() {
		return $this->domains;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
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
	 * @param string[] $name
	 */
	public function setName(array $name) {
		$this->name = $name;
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
	 * @param string[] $description
	 */
	public function setDescription(array $description) {
		$this->description = $description;
	}

	/**
	 * @param int[] $domains
	 */
	public function setDomains(array $domains) {
		$this->domains = $domains;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 */
	public function setTransports(array $transports) {
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
		$names = [];
		$desctiptions = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
			$desctiptions[$translate->getLocale()] = $translate->getDescription();
		}
		$this->setName($names);
		$this->setDescription($desctiptions);

		$domains = [];
		foreach ($paymentDomains as $paymentDomain) {
			$domains[] = $paymentDomain->getDomainId();
		}
		$this->setDomains($domains);
	}
}
