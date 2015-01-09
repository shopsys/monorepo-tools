<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class TransportData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var string
	 */
	public $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public $vat;

	/**
	 * @var string[]
	 */
	public $description;

	/**
	 * @var bool
	 */
	public $hidden;

	/**
	 * @var string
	 */
	public $image;

	/**
	 * @var int[]
	 */
	public $domains;

	/**
	 * @param string[] $names
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param string[] $descriptions
	 * @param boolean $hidden
	 * @param int[] $domains
	 */
	public function __construct(
		array $names = [],
		$price = null,
		Vat $vat = null,
		array $descriptions = [],
		$hidden = false,
		array $domains = []
	) {
		$this->name = $names;
		$this->price = $price;
		$this->vat = $vat;
		$this->description = $descriptions;
		$this->hidden = $hidden;
		$this->domains = $domains;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportDomain[] $transportDomains
	 */
	public function setFromEntity(Transport $transport, array $transportDomains) {
		$translations = $transport->getTranslations();
		$names = [];
		$desctiptions = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
			$desctiptions[$translate->getLocale()] = $translate->getDescription();
		}
		$this->name = $names;
		$this->description = $desctiptions;
		$this->hidden = $transport->isHidden();
		$this->price = $transport->getPrice();
		$this->vat = $transport->getVat();

		$domains = [];
		foreach ($transportDomains as $transportDomain) {
			$domains[] = $transportDomain->getDomainId();
		}
		$this->domains = $domains;
	}
}
