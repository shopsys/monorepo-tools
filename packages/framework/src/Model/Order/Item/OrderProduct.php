<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;

/**
 * @ORM\Entity
 */
class OrderProduct extends OrderItem
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=true, name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $product;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param string $unitName
     * @param string|null $catnum
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
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
            $unitName,
            $catnum
        );

        if ($product !== null && $product->isMainVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException();
        }

        $this->product = $product;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return bool
     */
    public function hasProduct()
    {
        return $this->product !== null;
    }
}
