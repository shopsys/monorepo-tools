<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Product\ProductTranslation;

/**
 * @ORM\Table(name="product_translations")
 * @ORM\Entity
 */
class ExtendedProductTranslation extends ProductTranslation
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $productDetailName;

    /**
     * @return string
     */
    public function getProductDetailName(): string
    {
        return $this->productDetailName;
    }

    /**
     * @param string $productDetailName
     */
    public function setProductDetailName(string $productDetailName): void
    {
        $this->productDetailName = $productDetailName;
    }
}
