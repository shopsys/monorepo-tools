<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;

class QuantifiedProductPriceCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Rounding
     */
    private $rounding;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct
     */
    private $quantifiedProduct;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product
     */
    private $product;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $productPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PriceCalculation
     */
    private $priceCalculation;

    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        Rounding $rounding,
        PriceCalculation $priceCalculation
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->rounding = $rounding;
        $this->priceCalculation = $priceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct $quantifiedProduct
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Customer\User|null $user
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice
     */
    public function calculatePrice(QuantifiedProduct $quantifiedProduct, $domainId, User $user = null)
    {
        $product = $quantifiedProduct->getProduct();
        if (!$product instanceof Product) {
            $message = 'Object "' . get_class($product) . '" is not valid for QuantifiedProductPriceCalculation.';
            throw new \Shopsys\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
        }

        $this->quantifiedProduct = $quantifiedProduct;
        $this->product = $product;
        $this->productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $domainId,
            $user
        );

        $quantifiedItemPrice = new QuantifiedItemPrice(
            $this->productPrice,
            new Price(
                $this->getTotalPriceWithoutVat(),
                $this->getTotalPriceWithVat()
            ),
            $product->getVat()
        );

        return $quantifiedItemPrice;
    }

    /**
     * @return string
     */
    private function getTotalPriceWithoutVat()
    {
        return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
    }

    /**
     * @return string
     */
    private function getTotalPriceWithVat()
    {
        return $this->productPrice->getPriceWithVat() * $this->quantifiedProduct->getQuantity();
    }

    /**
     * @return string
     */
    private function getTotalPriceVatAmount()
    {
        $vatPercent = $this->product->getVat()->getPercent();

        return $this->rounding->roundVatAmount(
            $this->getTotalPriceWithVat() * $this->priceCalculation->getVatCoefficientByPercent($vatPercent)
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex] $quantifiedProducts
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Customer\User|null $user
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
     */
    public function calculatePrices(array $quantifiedProducts, $domainId, User $user = null)
    {
        $quantifiedItemsPrices = [];
        foreach ($quantifiedProducts as $index => $quantifiedProduct) {
            $quantifiedItemsPrices[$index] = $this->calculatePrice($quantifiedProduct, $domainId, $user);
        }

        return $quantifiedItemsPrices;
    }
}
