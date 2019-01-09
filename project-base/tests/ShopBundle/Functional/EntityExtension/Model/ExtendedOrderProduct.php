<?php

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Product\Product;

/**
 * @ORM\Entity
 *
 * Base of this class is a copy of OrderProduct entity from FrameworkBundle
 * Reason is described in /project-base/docs/wip_glassbox/entity-extension.md
 *
 * @see \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct
 */
class ExtendedOrderProduct extends ExtendedOrderItem
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $productStringField;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param string $unitName
     * @param string|null $catnum
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     */
    public function __construct(
        Order $order,
        $name,
        Price $price,
        $vatPercent,
        $quantity,
        $unitName,
        $catnum,
        Product $product = null
    ) {
        parent::__construct(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            ExtendedOrderItem::TYPE_PRODUCT,
            $unitName,
            $catnum
        );

        $this->setProduct($product);
    }

    /**
     * @return string|null
     */
    public function getProductStringField()
    {
        return $this->productStringField;
    }

    /**
     * @param string|null $productStringField
     */
    public function setProductStringField($productStringField)
    {
        $this->productStringField = $productStringField;
    }
}
