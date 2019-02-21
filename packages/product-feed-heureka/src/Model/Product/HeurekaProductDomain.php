<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Money\Money;

/**
 * @ORM\Table(
 *     name="heureka_product_domains"
 * )
 * @ORM\Entity
 */
class HeurekaProductDomain
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
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     *
     * @ORM\Column(type="money", precision=20, scale=6, nullable=true)
     */
    protected $cpc;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData $heurekaProductDomainData
     */
    public function __construct(HeurekaProductDomainData $heurekaProductDomainData)
    {
        $this->product = $heurekaProductDomainData->product;
        $this->cpc = $heurekaProductDomainData->cpc;
        $this->domainId = $heurekaProductDomainData->domainId;
    }

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData $heurekaProductDomainData
     */
    public function edit(HeurekaProductDomainData $heurekaProductDomainData)
    {
        $this->product = $heurekaProductDomainData->product;
        $this->cpc = $heurekaProductDomainData->cpc;
        $this->domainId = $heurekaProductDomainData->domainId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public function getCpc(): ?Money
    {
        return $this->cpc;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }
}
