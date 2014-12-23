<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class TransportData {

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
	 * @var bool
	 */
	private $hidden;

	/**
	 * @var string
	 */
	private $image;

	/**
	 * @var int[]
	 */
	private $domains;

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
	 * @return int[]
	 */
	public function getDomains() {
		return $this->domains;
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
	 * @param int[] $domains
	 */
	public function setDomains(array $domains) {
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
		$this->setName($names);
		$this->setDescription($desctiptions);
		$this->setHidden($transport->isHidden());
		$this->setPrice($transport->getPrice());
		$this->setVat($transport->getVat());

		$domains = [];
		foreach ($transportDomains as $transportDomain) {
			$domains[] = $transportDomain->getDomainId();
		}
		$this->setDomains($domains);
	}
}
