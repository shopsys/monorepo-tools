<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class TransportData {

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
		$this->hidden = $hidden;
		$this->domains = $domains;
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
		$this->setDescriptions($transport->getDescriptions());
		$this->setHidden($transport->isHidden());
		$this->setNames($transport->getNames());
		$this->setPrice($transport->getPrice());
		$this->setVat($transport->getVat());

		$domains = array();
		foreach ($transportDomains as $transportDomain) {
			$domains[] = $transportDomain->getDomainId();
		}
		$this->setDomains($domains);
	}
}
