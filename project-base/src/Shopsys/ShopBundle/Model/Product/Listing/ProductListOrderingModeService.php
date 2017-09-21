<?php

namespace Shopsys\ShopBundle\Model\Product\Listing;

use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeService
{
    const ORDER_BY_RELEVANCE = 'relevance';
    const ORDER_BY_NAME_ASC = 'name_asc';
    const ORDER_BY_NAME_DESC = 'name_desc';
    const ORDER_BY_PRICE_ASC = 'price_asc';
    const ORDER_BY_PRICE_DESC = 'price_desc';
    const ORDER_BY_PRIORITY = 'priority';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingConfig $productListOrderingConfig
     * @return string
     */
    public function getOrderingModeIdFromRequest(
        Request $request,
        ProductListOrderingConfig $productListOrderingConfig
    ) {
        $orderingModeId = $request->cookies->get($productListOrderingConfig->getCookieName());

        if (!in_array($orderingModeId, $this->getSupportedOrderingModeIds($productListOrderingConfig), true)) {
            $orderingModeId = $productListOrderingConfig->getDefaultOrderingModeId();
        }

        return $orderingModeId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingConfig $productListOrderingConfig
     * @return string[]
     */
    private function getSupportedOrderingModeIds(ProductListOrderingConfig $productListOrderingConfig)
    {
        return array_keys($productListOrderingConfig->getSupportedOrderingModesNamesIndexedById());
    }
}
