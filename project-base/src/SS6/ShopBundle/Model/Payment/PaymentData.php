<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class PaymentData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public $vat;

	/**
	 * @var string[]
	 */
	public $description;

	/**
	 * @var string[]
	 */
	public $instructions;

	/**
	 * @var int[]
	 */
	public $domains;

	/**
	 * @var int
	 */
	public $hidden;

	/**
	 * @var string
	 */
	public $image;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public $transports;

	/**
	 * @param string[] $name
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param string[] $description
	 * @param string[] $instructions
	 * @param bool $hidden
	 * @param int[] $domains
	 */
	public function __construct(
		array $name = [],
		Vat $vat = null,
		array $description = [],
		array $instructions = [],
		$hidden = false,
		array $domains = []
	) {
		$this->name = $name;
		$this->vat = $vat;
		$this->description = $description;
		$this->instructions = $instructions;
		$this->domains = $domains;
		$this->hidden = $hidden;
		$this->transports = [];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentDomain[] $paymentDomains
	 */
	public function setFromEntity(Payment $payment, array $paymentDomains) {
		$this->vat = $payment->getVat();
		$this->hidden = $payment->isHidden();
		$this->transports = $payment->getTransports()->toArray();

		$translations = $payment->getTranslations();
		$names = [];
		$desctiptions = [];
		$instructions = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
			$desctiptions[$translate->getLocale()] = $translate->getDescription();
			$instructions[$translate->getLocale()] = $translate->getInstructions();
		}
		$this->name = $names;
		$this->description = $desctiptions;
		$this->instructions = $instructions;

		$domains = [];
		foreach ($paymentDomains as $paymentDomain) {
			$domains[] = $paymentDomain->getDomainId();
		}
		$this->domains = $domains;
	}
}
