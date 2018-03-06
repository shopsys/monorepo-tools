<?php

namespace Shopsys\FrameworkBundle\Model\Order\Preview;

use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

class OrderPreview
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    private $quantifiedProductsByIndex;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[]
     */
    private $quantifiedItemsPricesByIndex;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    private $quantifiedItemsDiscountsByIndex;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     */
    private $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    private $transportPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    private $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    private $paymentPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private $totalPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private $productsPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    private $roundingPrice;

    /**
     * @var float|null
     */
    private $promoCodeDiscountPercent;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProductsByIndex
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPricesByIndex
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price[] $quantifiedItemsDiscountsByIndex
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $totalPrice
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $transportPrice
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment|null $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $paymentPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $roundingPrice
     * @param float|null $promoCodeDiscountPercent
     */
    public function __construct(
        array $quantifiedProductsByIndex,
        array $quantifiedItemsPricesByIndex,
        array $quantifiedItemsDiscountsByIndex,
        Price $productsPrice,
        Price $totalPrice,
        Transport $transport = null,
        Price $transportPrice = null,
        Payment $payment = null,
        Price $paymentPrice = null,
        Price $roundingPrice = null,
        $promoCodeDiscountPercent = null
    ) {
        $this->quantifiedProductsByIndex = $quantifiedProductsByIndex;
        $this->quantifiedItemsPricesByIndex = $quantifiedItemsPricesByIndex;
        $this->quantifiedItemsDiscountsByIndex = $quantifiedItemsDiscountsByIndex;
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
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProducts()
    {
        return $this->quantifiedProductsByIndex;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[]
     */
    public function getQuantifiedItemsPrices()
    {
        return $this->quantifiedItemsPricesByIndex;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getQuantifiedItemsDiscounts()
    {
        return $this->quantifiedItemsDiscountsByIndex;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    public function getTransportPrice()
    {
        return $this->transportPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    public function getPaymentPrice()
    {
        return $this->paymentPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getProductsPrice()
    {
        return $this->productsPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
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
