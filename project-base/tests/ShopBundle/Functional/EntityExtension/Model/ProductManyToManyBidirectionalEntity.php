<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProductManyToManyBidirectionalEntity
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
     * @var \Doctrine\Common\Collections\Collection|\Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct[]
     *
     * @ORM\ManyToMany(targetEntity="ExtendedProduct", mappedBy="manyToManyBidirectionalEntities")
     */
    protected $products;

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
        $this->products = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct[]
     */
    public function getProducts(): array
    {
        return $this->products->getValues();
    }

    /**
     * @param \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct $product
     */
    public function addProduct(ExtendedProduct $product): void
    {
        $this->products->add($product);
    }
}
