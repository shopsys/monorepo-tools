<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Product\Flag\FlagData;
use SS6\ShopBundle\Model\Product\Flag\FlagTranslation;

/**
 * @ORM\Table(name="flags")
 * @ORM\Entity
 */
class Flag extends AbstractTranslatableEntity {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\Flag\FlagTranslation")
	 */
	protected $translations;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=7)
	 */
	private $rgbColor;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 */
	public function __construct(FlagData $flagData) {
		$this->translations = new ArrayCollection();
		$this->setTranslations($flagData);
		$this->rgbColor = $flagData->rgbColor;
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
	 * @return string
	 */
	public function getRgbColor() {
		return $this->rgbColor;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 */
	private function setTranslations(FlagData $flagData) {
		foreach ($flagData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Flag\FlagTranslation
	 */
	protected function createTranslation() {
		return new FlagTranslation();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 */
	public function edit(FlagData $flagData) {
		$this->setTranslations($flagData);
		$this->rgbColor = $flagData->rgbColor;
	}

}
