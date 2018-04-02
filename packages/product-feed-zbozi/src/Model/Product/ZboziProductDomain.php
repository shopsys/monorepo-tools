<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="zbozi_product_domains"
 * )
 * @ORM\Entity
 */
class ZboziProductDomain
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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $show;

    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
     */
    protected $cpc;

    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
     */
    protected $cpcSearch;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData $zboziProductDomainData
     */
    public function __construct(ZboziProductDomainData $zboziProductDomainData)
    {
        $this->product = $zboziProductDomainData->product;
        $this->show = $zboziProductDomainData->show;
        $this->cpc = $zboziProductDomainData->cpc;
        $this->cpcSearch = $zboziProductDomainData->cpcSearch;
        $this->domainId = $zboziProductDomainData->domainId;
    }

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData $zboziProductDomainData
     */
    public function edit(ZboziProductDomainData $zboziProductDomainData)
    {
        $this->product = $zboziProductDomainData->product;
        $this->show = $zboziProductDomainData->show;
        $this->cpc = $zboziProductDomainData->cpc;
        $this->cpcSearch = $zboziProductDomainData->cpcSearch;
        $this->domainId = $zboziProductDomainData->domainId;
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
     * @return bool
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return null|string
     */
    public function getCpc()
    {
        return $this->cpc;
    }

    /**
     * @return null|string
     */
    public function getCpcSearch()
    {
        return $this->cpcSearch;
    }
}
