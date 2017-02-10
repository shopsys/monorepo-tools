<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity
{

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
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation")
     */
    protected $translations;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
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
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     */
    public function edit(BrandData $brandData) {
        $this->name = $brandData->name;
        $this->setTranslations($brandData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     */
    private function setTranslations(BrandData $brandData) {
        foreach ($brandData->descriptions as $locale => $description) {
            $brandTranslation = $this->translation($locale);
            /* @var $brandTranslation \Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation */
            $brandTranslation->setDescription($description);
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation
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
