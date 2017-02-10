<?php

namespace Shopsys\ShopBundle\Model\Category;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="category_translations")
 * @ORM\Entity
 */
class CategoryTranslation extends AbstractTranslation
{

    /**
     * @Prezent\Translatable(targetEntity="Shopsys\ShopBundle\Model\Category\Category")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
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
