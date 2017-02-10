<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Order;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\ShopBundle\Model\Order\Item\OrderPayment;
use Shopsys\ShopBundle\Model\Order\Item\OrderProduct;
use Shopsys\ShopBundle\Model\Order\Item\OrderTransport;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\OrderPriceCalculation;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\Rounding;

class OrderPriceCalculationTest extends PHPUnit_Framework_TestCase
{

    public function testGetOrderTotalPrice() {
        $orderItems = [
            $this->getMock(OrderProduct::class, [], [], '', false),
            $this->getMock(OrderProduct::class, [], [], '', false),
            $this->getMock(OrderPayment::class, [], [], '', false),
            $this->getMock(OrderTransport::class, [], [], '', false),
        ];

        $pricesMap = [
            [$orderItems[0], new Price(150, 200)],
            [$orderItems[1], new Price(1000, 3000)],
            [$orderItems[2], new Price(15, 20)],
            [$orderItems[3], new Price(0, 0)],
        ];

        $roundingMock = $this->getMock(Rounding::class, [], [], '', false);

        $orderItemPriceCalculationMock = $this->getMockBuilder(OrderItemPriceCalculation::class)
            ->setMethods(['__construct', 'calculateTotalPrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemPriceCalculationMock
            ->expects($this->exactly(4))
            ->method('calculateTotalPrice')
            ->willReturnMap($pricesMap);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);

        $orderMock = $this->getMock(Order::class, ['__construct', 'getItems'], [], '', false);
        $orderMock->expects($this->once())->method('getItems')->willReturn($orderItems);

        $orderTotalPrice = $priceCalculation->getOrderTotalPrice($orderMock);

        $this->assertSame(3220, $orderTotalPrice->getPriceWithVat());
        $this->assertSame(1165, $orderTotalPrice->getPriceWithoutVat());
        $this->assertSame(3200, $orderTotalPrice->getProductPriceWithVat());
    }

    public function testCalculateOrderRoundingPriceForOtherCurrency() {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currency = new Currency(new CurrencyData('currencyName', Currency::CODE_EUR, 1.0));
        $orderTotalPrice = new Price(100, 120);

        $roundingMock = $this->getMock(Rounding::class, [], [], '', false);
        $orderItemPriceCalculationMock = $this->getMock(OrderItemPriceCalculation::class, [], [], '', false);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice);

        $this->assertNull($roundingPrice);
    }

    public function testCalculateOrderRoundingPriceForCzkWithoutRounding() {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = false;
        $payment = new Payment($paymentData);

        $currency = new Currency(new CurrencyData('currencyName', Currency::CODE_CZK, 1.0));
        $orderTotalPrice = new Price(100, 120);

        $roundingMock = $this->getMock(Rounding::class, [], [], '', false);
        $orderItemPriceCalculationMock = $this->getMock(OrderItemPriceCalculation::class, [], [], '', false);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice);

        $this->assertNull($roundingPrice);
    }

    public function testCalculateOrderRoundingPriceDown() {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currency = new Currency(new CurrencyData('currencyName', Currency::CODE_CZK, 1.0));
        $orderTotalPrice = new Price(100, 120.3);

        $roundingMock = $this->getMock(Rounding::class, ['roundPriceWithVat'], [], '', false);
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
            return round($value, 2);
        });

        $orderItemPriceCalculationMock = $this->getMock(OrderItemPriceCalculation::class, [], [], '', false);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertSame('-0.3', (string)$roundingPrice);
    }

    public function testCalculateOrderRoundingPriceUp() {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currency = new Currency(new CurrencyData('currencyName', Currency::CODE_CZK, 1.0));
        $orderTotalPrice = new Price(100, 120.9);

        $roundingMock = $this->getMock(Rounding::class, ['roundPriceWithVat'], [], '', false);
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
            return round($value, 2);
        });

        $orderItemPriceCalculationMock = $this->getMock(OrderItemPriceCalculation::class, [], [], '', false);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertSame('0.1', (string)$roundingPrice);
    }
}
