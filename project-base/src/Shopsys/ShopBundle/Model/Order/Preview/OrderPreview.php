<?php

namespace Shopsys\ShopBundle\Model\Order\Preview;

use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Transport\Transport;

class OrderPreview
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex]
     */
    private $quantifiedProducts;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
     */
    private $quantifiedItemsPrices;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
     */
    private $quantifiedItemsDiscounts;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport|null
     */
    private $transport;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    private $transportPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment|null
     */
    private $payment;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    private $paymentPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $totalPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $productsPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    private $roundingPrice;

    /**
     * @var float|null
     */
    private $promoCodeDiscountPercent;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex] $quantifiedProducts
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex] $quantifiedItemsPrices
     * @param \Shopsys\ShopBundle\Model\Pricing\Price[quantifiedItemIndex] $quantifiedItemsDiscounts
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $totalPrice
     * @param \Shopsys\ShopBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Price|null $transportPrice
     * @param \Shopsys\ShopBundle\Model\Payment\Payment|null $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Price|null $paymentPrice
     * @param \Shopsys\ShopBundle\Model\Pricing\Price|null $roundingPrice
     * @param float|null $promoCodeDiscountPercent
     */
    public function __construct(
        array $quantifiedProducts,
        array $quantifiedItemsPrices,
        array $quantifiedItemsDiscounts,
        Price $productsPrice,
        Price $totalPrice,
        Transport $transport = null,
        Price $transportPrice = null,
        Payment $payment = null,
        Price $paymentPrice = null,
        Price $roundingPrice = null,
        $promoCodeDiscountPercent = null
    ) {
        $this->quantifiedProducts = $quantifiedProducts;
        $this->quantifiedItemsPrices = $quantifiedItemsPrices;
        $this->quantifiedItemsDiscounts = $quantifiedItemsDiscounts;
        $this->productsPrice = $productsPrice;
        $this->totalPrice = $totalPrice;
        $this->transport = $transport;
        $this->transportPrice = $transportPrice;
        $this->payment = $payment;
        $this->paymentPrice = $paymentPrice;
        $this->roundingPrice = $roundingPrice;
        $this->promoCodeDiscountPercent = $promoCodeDiscountPercent;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex]
     */
    public function getQuantifiedProducts()
    {
        return $this->quantifiedProducts;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
     */
    public function getQuantifiedItemsPrices()
    {
        return $this->quantifiedItemsPrices;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
     */
    public function getQuantifiedItemsDiscounts()
    {
        return $this->quantifiedItemsDiscounts;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport|null
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    public function getTransportPrice()
    {
        return $this->transportPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Payment\Payment|null
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    public function getPaymentPrice()
    {
        return $this->paymentPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getProductsPrice()
    {
        return $this->productsPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    public function getRoundingPrice()
    {
        return $this->roundingPrice;
    }

    /**
     * @return float|null
     */
    public function getPromoCodeDiscountPercent()
    {
        return $this->promoCodeDiscountPercent;
    }
}
