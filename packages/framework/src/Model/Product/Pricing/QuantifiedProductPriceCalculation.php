<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Product\Product;

class QuantifiedProductPriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Rounding
     */
    protected $rounding;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct
     */
    protected $quantifiedProduct;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     */
    protected $product;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected $productPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation
     */
    protected $priceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Rounding $rounding
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation $priceCalculation
     */
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct $quantifiedProduct
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice
     */
    public function calculatePrice(QuantifiedProduct $quantifiedProduct, $domainId, User $user = null)
    {
        $product = $quantifiedProduct->getProduct();
        if (!$product instanceof Product) {
            $message = 'Object "' . get_class($product) . '" is not valid for QuantifiedProductPriceCalculation.';
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
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
                Money::fromValue($this->getTotalPriceWithoutVat()),
                Money::fromValue($this->getTotalPriceWithVat())
            ),
            $product->getVat()
        );

        return $quantifiedItemPrice;
    }

    /**
     * @return string
     */
    protected function getTotalPriceWithoutVat()
    {
        return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
    }

    /**
     * @return string
     */
    protected function getTotalPriceWithVat()
    {
        return $this->productPrice->getPriceWithVat()->toValue() * $this->quantifiedProduct->getQuantity();
    }

    /**
     * @return string
     */
    protected function getTotalPriceVatAmount()
    {
        $vatPercent = $this->product->getVat()->getPercent();

        return $this->rounding->roundVatAmount(
            $this->getTotalPriceWithVat() * $this->priceCalculation->getVatCoefficientByPercent($vatPercent)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[]
     */
    public function calculatePrices(array $quantifiedProducts, $domainId, User $user = null)
    {
        $quantifiedItemsPrices = [];
        foreach ($quantifiedProducts as $quantifiedItemIndex => $quantifiedProduct) {
            $quantifiedItemsPrices[$quantifiedItemIndex] = $this->calculatePrice($quantifiedProduct, $domainId, $user);
        }

        return $quantifiedItemsPrices;
    }
}
