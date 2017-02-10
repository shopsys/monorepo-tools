<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitTranslation;

/**
 * @ORM\Table(name="units")
 * @ORM\Entity
 */
class Unit extends AbstractTranslatableEntity
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
     * @var \Shopsys\ShopBundle\Model\Product\Unit\UnitTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\ShopBundle\Model\Product\Unit\UnitTranslation")
     */
    protected $translations;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
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
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     */
    private function setTranslations(UnitData $unitData) {
        foreach ($unitData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Unit\UnitTranslation
     */
    protected function createTranslation() {
        return new UnitTranslation();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     */
    public function edit(UnitData $unitData) {
        $this->setTranslations($unitData);
    }
}
