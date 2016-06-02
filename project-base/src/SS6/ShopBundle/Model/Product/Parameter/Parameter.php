<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterTranslation;

/**
 * @ORM\Table(name="parameter_titles")
 * @ORM\Entity
 */
class Parameter extends AbstractTranslatableEntity {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\Parameter\ParameterTranslation")
	 */
	protected $translations;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	public function __construct(ParameterData $parameterData) {
		$this->translations = new ArrayCollection();
		$this->setTranslations($parameterData);
		$this->visible = $parameterData->visible;
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
	 * @return bool
	 */
	public function isVisible() {
		return $this->visible;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	private function setTranslations(ParameterData $parameterData) {
		foreach ($parameterData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterTranslation
	 */
	protected function createTranslation() {
		return new ParameterTranslation();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	public function edit(ParameterData $parameterData) {
		$this->setTranslations($parameterData);
		$this->visible = $parameterData->visible;
	}

}
