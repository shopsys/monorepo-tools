<?php

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeService
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig $productListOrderingConfig
     * @return string
     */
    public function getOrderingModeIdFromRequest(
        Request $request,
        ProductListOrderingConfig $productListOrderingConfig
    ) {
        $orderingModeId = $request->cookies->get($productListOrderingConfig->getCookieName());

        if (!in_array($orderingModeId, $productListOrderingConfig->getSupportedOrderingModeIds(), true)) {
            $orderingModeId = $productListOrderingConfig->getDefaultOrderingModeId();
        }

        return $orderingModeId;
    }
}
