<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

class OrderProductService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function subtractOrderProductsFromStock(array $orderProducts) {
        $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
        foreach ($orderProductsUsingStock as $orderProductUsingStock) {
            $product = $orderProductUsingStock->getProduct();
            $product->subtractStockQuantity($orderProductUsingStock->getQuantity());
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function returnOrderProductsToStock(array $orderProducts) {
        $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
        foreach ($orderProductsUsingStock as $orderProductUsingStock) {
            $product = $orderProductUsingStock->getProduct();
            $product->addStockQuantity($orderProductUsingStock->getQuantity());
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getProductsUsingStockFromOrderProducts(array $orderProducts) {
        $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
        $productsUsingStock = [];
        foreach ($orderProductsUsingStock as $orderProductUsingStock) {
            $productsUsingStock[] = $orderProductUsingStock->getProduct();
        }

        return $productsUsingStock;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    private function getOrderProductsUsingStockFromOrderProducts(array $orderProducts) {
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
