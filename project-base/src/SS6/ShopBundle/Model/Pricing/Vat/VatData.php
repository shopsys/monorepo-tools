<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class VatData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $percent;

	/**
	 * @param string|null $name
	 * @param string|null $percent
	 */
	public function __construct($name = null, $percent = null) {
		$this->name = $name;
		$this->percent = $percent;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getPercent() {
		return $this->percent;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $percent
	 */
	public function setPercent($percent) {
		$this->percent = $percent;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function setFromEntity(Vat $vat) {
		$this->name = $vat->getName();
		$this->percent = $vat->getPercent();
	}

}
