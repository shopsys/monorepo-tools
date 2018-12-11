<?php

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeForBrandFacade
{
    const COOKIE_NAME = 'productListOrderingModeForBrand';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Listing\RequestToOrderingModeIdConverter
     */
    protected $requestToOrderingModeIdConverter;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Listing\RequestToOrderingModeIdConverter $requestToOrderingModeIdConverter
     */
    public function __construct(RequestToOrderingModeIdConverter $requestToOrderingModeIdConverter)
    {
        $this->requestToOrderingModeIdConverter = $requestToOrderingModeIdConverter;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig
     */
    public function getProductListOrderingConfig()
    {
        return new ProductListOrderingConfig(
            [
                ProductListOrderingConfig::ORDER_BY_PRIORITY => t('TOP'),
                ProductListOrderingConfig::ORDER_BY_NAME_ASC => t('alphabetically A -> Z'),
                ProductListOrderingConfig::ORDER_BY_NAME_DESC => t('alphabetically Z -> A'),
                ProductListOrderingConfig::ORDER_BY_PRICE_ASC => t('from the cheapest'),
                ProductListOrderingConfig::ORDER_BY_PRICE_DESC => t('from most expensive'),
            ],
            ProductListOrderingConfig::ORDER_BY_PRIORITY,
            self::COOKIE_NAME
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    public function getOrderingModeIdFromRequest(Request $request)
    {
        return $this->requestToOrderingModeIdConverter->getOrderingModeIdFromRequest(
            $request,
            $this->getProductListOrderingConfig()
        );
    }
}
