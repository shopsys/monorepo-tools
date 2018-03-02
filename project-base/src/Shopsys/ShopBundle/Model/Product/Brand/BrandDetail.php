<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

class BrandDetail
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public $brand;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDetailFactory
     */
    public $brandDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain
     */
    private $brandDomainsIndexedByDomainId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDetailFactory $brandDetailFactory
     * @param array|null $brandDomainsIndexedByDomainId
     */
    public function __construct(
        Brand $brand,
        BrandDetailFactory $brandDetailFactory,
        array $brandDomainsIndexedByDomainId = null
    ) {
        $this->brand = $brand;
        $this->brandDetailFactory = $brandDetailFactory;
        $this->brandDomainsIndexedByDomainId = $brandDomainsIndexedByDomainId;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsIndexedByDomainId()
    {
        if ($this->brandDomainsIndexedByDomainId === null) {
            $this->brandDomainsIndexedByDomainId = $this->brandDetailFactory->getBrandDomainsIndexedByDomainId($this->brand);
        }

        return $this->brandDomainsIndexedByDomainId;
    }
}
