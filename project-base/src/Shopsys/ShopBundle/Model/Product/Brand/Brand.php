<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Product\Brand\BrandData;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $name;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\Brand\BrandTranslation")
	 */
	protected $translations;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	public function __construct(BrandData $brandData) {
		$this->name = $brandData->name;
		$this->translations = new ArrayCollection();
		$this->setTranslations($brandData);
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
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	public function edit(BrandData $brandData) {
		$this->name = $brandData->name;
		$this->setTranslations($brandData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	private function setTranslations(BrandData $brandData) {
		foreach ($brandData->descriptions as $locale => $description) {
			$brandTranslation = $this->translation($locale);
			/* @var $brandTranslation \SS6\ShopBundle\Model\Product\Brand\BrandTranslation */
			$brandTranslation->setDescription($description);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Brand\BrandTranslation
	 */
	protected function createTranslation() {
		return new BrandTranslation();
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	public function getDescription($locale = null) {
		return $this->translation($locale)->getDescription();
	}

}
