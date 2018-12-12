<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderProductService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public function getProductsUsingStockFromOrderProducts(array $orderProducts)
    {
        $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
        $productsUsingStock = [];
        foreach ($orderProductsUsingStock as $orderProductUsingStock) {
            $productsUsingStock[] = $orderProductUsingStock->getProduct();
        }

        return $productsUsingStock;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[]
     */
    public function getOrderProductsUsingStockFromOrderProducts(array $orderProducts)
    {
        $orderProductsUsingStock = [];
        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->getProduct();
            if ($product !== null && $product->isUsingStock()) {
                $orderProductsUsingStock[] = $orderProduct;
            }
        }

        return $orderProductsUsingStock;
    }
}
