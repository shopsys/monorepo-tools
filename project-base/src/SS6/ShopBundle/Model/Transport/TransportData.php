<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class TransportData {

	/**
	 * @var string
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
	 * @var string
	 */
	private $description;

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
	private $domains;

	/**
	 * @param string|null $name
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param string|null $description
	 * @param boolean $hidden
	 * @param array $domains
	 */
	public function __construct(
		$name = null,
		$price = null,
		Vat $vat = null,
		$description = null,
		$hidden = false,
		$domains = array()
	) {
		$this->name = $name;
		$this->price = $price;
		$this->vat = $vat;
		$this->description = $description;
		$this->hidden = $hidden;
		$this->domains = $domains;
	}

	/**
	 * @return string
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
	 * @return string
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
	 * @return array
	 */
	public function getDomains() {
		return $this->domains;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
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
	 * @param string $description
	 */
	public function setDescription($description) {
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
	 * @param array $domains
	 */
	public function setDomains($domains) {
		$this->domains = $domains;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportDomain[] $transportDomains
	 */
	public function setFromEntity(Transport $transport, array $transportDomains) {
		$this->setDescription($transport->getDescription());
		$this->setHidden($transport->isHidden());
		$this->setName($transport->getName());
		$this->setPrice($transport->getPrice());
		$this->setVat($transport->getVat());
		
		$domains = array();
		foreach ($transportDomains as $transportDomain) {
			$domains[] = $transportDomain->getDomainId();
		}
		$this->setDomains($domains);
	}
}
