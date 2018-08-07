<?php

namespace Tests\ShopBundle\Database\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProductOneToManyBidirectionalEntity
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
     * @var \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedProduct
     *
     * @ORM\ManyToOne(targetEntity="ExtendedProduct", inversedBy="oneToManyBidirectionalEntity")
     * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedProduct
     */
    public function getProduct(): ExtendedProduct
    {
        return $this->product;
    }

    /**
     * @param \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedProduct $product
     */
    public function setProduct(ExtendedProduct $product): void
    {
        $this->product = $product;
    }
}
