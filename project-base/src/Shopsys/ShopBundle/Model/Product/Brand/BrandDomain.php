<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;

/**
 * @ORM\Table(name="brand_domains")
 * @ORM\Entity
 */
class BrandDomain
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\Brand
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Brand\Brand")
     * @ORM\JoinColumn(nullable=false, name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $brand;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoMetaDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoH1;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @param int $domainId
     */
    public function __construct(Brand $brand, $domainId)
    {
        $this->brand = $brand;
        $this->domainId = $domainId;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param int $domainId
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
    }

    /**
     * @return null|string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param null|string $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @return null|string
     */
    public function getSeoMetaDescription()
    {
        return $this->seoMetaDescription;
    }

    /**
     * @param null|string $seoMetaDescription
     */
    public function setSeoMetaDescription($seoMetaDescription)
    {
        $this->seoMetaDescription = $seoMetaDescription;
    }

    /**
     * @return null|string
     */
    public function getSeoH1()
    {
        return $this->seoH1;
    }

    /**
     * @param null|string $seoH1
     */
    public function setSeoH1($seoH1)
    {
        $this->seoH1 = $seoH1;
    }
}
