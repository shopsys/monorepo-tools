<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="brand_translations")
 * @ORM\Entity
 */
class BrandTranslation extends AbstractTranslation
{

    /**
     * @Prezent\Translatable(targetEntity="Shopsys\ShopBundle\Model\Product\Brand\Brand")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }
}
