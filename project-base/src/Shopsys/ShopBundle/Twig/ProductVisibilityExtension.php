<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository;
use Twig_SimpleFunction;

class ProductVisibilityExtension extends \Twig_Extension {

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository
     */
    private $productVisibilityRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        ProductVisibilityRepository $productVisibilityRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        Domain $domain
    ) {
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->domain = $domain;
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction('isVisibleForDefaultPricingGroup', [$this, 'isVisibleForDefaultPricingGroupOnDomain']),
            new Twig_SimpleFunction(
                'isVisibleForDefaultPricingGroupOnEachDomain', [$this, 'isVisibleForDefaultPricingGroupOnEachDomain']
            ),
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'product_visibility';
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return bool
     */
    public function isVisibleForDefaultPricingGroupOnDomain(Product $product, $domainId) {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);
        $productVisibility = $this->productVisibilityRepository->getProductVisibility($product, $pricingGroup, $domainId);

        return $productVisibility->isVisible();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return bool
     */
    public function isVisibleForDefaultPricingGroupOnEachDomain(Product $product) {
        foreach ($this->domain->getAll() as $domainConfig) {
            if (!$this->isVisibleForDefaultPricingGroupOnDomain($product, $domainConfig->getId())) {
                return false;
            }
        }

        return true;
    }
}
