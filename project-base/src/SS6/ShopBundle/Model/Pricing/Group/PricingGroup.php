<?php

namespace SS6\ShopBundle\Model\Pricing\Group;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pricing_groups")
 * @ORM\Entity
 */
class PricingGroup {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=4)
	 */
	private $coefficient;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 * @param int $domainId
	 */
	public function __construct($pricingGroupData, $domainId) {
		$this->coefficient = $pricingGroupData->getCoefficient();
		$this->name = $pricingGroupData->getName();
		$this->domainId = $domainId;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return string
	 */
	public function getCoefficient() {
		return $this->coefficient;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 */
	public function edit(PricingGroupData $pricingGroupData) {
		$this->name = $pricingGroupData->getName();
		$this->coefficient = $pricingGroupData->getCoefficient();
	}

}
