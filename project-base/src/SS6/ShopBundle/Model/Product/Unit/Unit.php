<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Product\Unit\UnitData;
use SS6\ShopBundle\Model\Product\Unit\UnitTranslation;

/**
 * @ORM\Table(name="units")
 * @ORM\Entity
 */
class Unit extends AbstractTranslatableEntity {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\Unit\UnitTranslation")
	 */
	protected $translations;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	public function __construct(UnitData $unitData) {
		$this->translations = new ArrayCollection();
		$this->setTranslations($unitData);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string|null $locale
	 * @return string
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	private function setTranslations(UnitData $unitData) {
		foreach ($unitData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Unit\UnitTranslation
	 */
	protected function createTranslation() {
		return new UnitTranslation();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	public function edit(UnitData $unitData) {
		$this->setTranslations($unitData);
	}

}
