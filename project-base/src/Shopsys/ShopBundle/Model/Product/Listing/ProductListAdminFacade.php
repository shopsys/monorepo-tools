<?php

namespace Shopsys\ShopBundle\Model\Product\Listing;

use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class ProductListAdminFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Listing\ProductListAdminRepository
     */
    private $productListAdminRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
     */
    public function __construct(
        ProductListAdminRepository $productListAdminRepository,
        PricingGroupSettingFacade $pricingGroupSettingFacade
    ) {
        $this->productListAdminRepository = $productListAdminRepository;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductListQueryBuilder()
    {
        /**
         * temporary solution -
         * when product price type calculation is set to manual, price for first domain is shown in admin product list
         */
        $defaultPricingGroupId = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId(1)->getId();

        return $this->productListAdminRepository->getProductListQueryBuilder($defaultPricingGroupId);
    }

    /**
     * @param \Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByQuickSearchData(QuickSearchFormData $quickSearchData)
    {
        $queryBuilder = $this->getProductListQueryBuilder();
        $this->productListAdminRepository->extendQueryBuilderByQuickSearchData($queryBuilder, $quickSearchData);

        return $queryBuilder;
    }
}
