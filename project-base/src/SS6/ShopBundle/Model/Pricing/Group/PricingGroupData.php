<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

class PricingGroupData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @var string
	 */
	private $coefficient;

	/**
	 * @param string|null $name
	 */
	public function __construct($name = null, $coefficient = 1) {
		$this->name = $name;
		$this->coefficient = $coefficient;
	}

	/**
	 * @return string
	 */
	public function getCoefficient() {
		return $this->coefficient;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $coefficient
	 */
	public function setCoefficient($coefficient) {
		$this->coefficient = $coefficient;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function setFromEntity(PricingGroup $pricingGroup) {
		$this->setName($pricingGroup->getName());
		$this->setCoefficient($pricingGroup->getCoefficient());
	}
}
