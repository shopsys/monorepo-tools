<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

/**
 * @ORM\Table(name="vats")
 * @ORM\Entity
 */
class Vat {

	const SETTING_DEFAULT_VAT = 'defaultVatId';

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=50)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=4)
	 */
	private $percent;

	public function __construct(VatData $vatData) {
		$this->name = $vatData->name;
		$this->percent = $vatData->percent;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	public function getPercent() {
		return $this->percent;
	}

	/**
	 * @return string
	 */
	public function getCoefficient() {
		$ratio = $this->percent / (100 + $this->percent);
		return round($ratio, 4);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatData $vatData
	 */
	public function edit(VatData $vatData) {
		$this->name = $vatData->name;
		$this->percent = $vatData->percent;
	}

}
