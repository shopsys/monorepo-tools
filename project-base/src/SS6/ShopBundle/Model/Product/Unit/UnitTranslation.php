<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="unit_translations")
 * @ORM\Entity
 */
class UnitTranslation extends AbstractTranslation {

	/**
	 * @Prezent\Translatable(targetEntity="SS6\ShopBundle\Model\Product\Unit\Unit")
	 */
	protected $translatable;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $name;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

}
