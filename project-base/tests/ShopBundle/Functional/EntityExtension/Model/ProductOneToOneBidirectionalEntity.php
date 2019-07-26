<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProductOneToOneBidirectionalEntity
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
     * @var \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct
     *
     * @ORM\OneToOne(targetEntity="ExtendedProduct", inversedBy="oneToOneBidirectionalEntity")
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
     * @return \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct
     */
    public function getProduct(): ExtendedProduct
    {
        return $this->product;
    }

    /**
     * @param \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct $product
     */
    public function setProduct(ExtendedProduct $product): void
    {
        $this->product = $product;
    }
}
