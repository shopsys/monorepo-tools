<?php

namespace Tests\FrameworkBundle\Unit\Model\Order;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class OrderPriceCalculationTest extends TestCase
{
    public function testGetOrderTotalPrice()
    {
        $orderItems = [
            $this->createOrderProductMock(),
            $this->createOrderProductMock(),
            $this->createOrderPaymentMock(),
            $this->createOrderTransportMock(),
        ];

        $pricesMap = [
            [$orderItems[0], new Price(Money::fromInteger(150), Money::fromInteger(200))],
            [$orderItems[1], new Price(Money::fromInteger(1000), Money::fromInteger(3000))],
            [$orderItems[2], new Price(Money::fromInteger(15), Money::fromInteger(20))],
            [$orderItems[3], new Price(Money::fromInteger(0), Money::fromInteger(0))],
        ];

        $roundingMock = $this->createMock(Rounding::class);

        $orderItemPriceCalculationMock = $this->getMockBuilder(OrderItemPriceCalculation::class)
            ->setMethods(['__construct', 'calculateTotalPrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemPriceCalculationMock
            ->expects($this->exactly(4))
            ->method('calculateTotalPrice')
            ->willReturnMap($pricesMap);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);

        $orderMock = $this->getMockBuilder(Order::class)
            ->setMethods(['__construct', 'getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock->expects($this->once())->method('getItems')->willReturn($orderItems);

        $orderTotalPrice = $priceCalculation->getOrderTotalPrice($orderMock);

        $this->assertSame(3220, $orderTotalPrice->getPriceWithVat());
        $this->assertSame(1165, $orderTotalPrice->getPriceWithoutVat());
        $this->assertSame(3200, $orderTotalPrice->getProductPriceWithVat());
    }

    public function testCalculateOrderRoundingPriceForOtherCurrency()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_EUR;
        $currencyData->exchangeRate = 1.0;
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::fromInteger(100), Money::fromInteger(120));

        $roundingMock = $this->createMock(Rounding::class);
        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice);

        $this->assertNull($roundingPrice);
    }

    public function testCalculateOrderRoundingPriceForCzkWithoutRounding()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = false;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_CZK;
        $currencyData->exchangeRate = 1.0;
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::fromInteger(100), Money::fromInteger(120));

        $roundingMock = $this->createMock(Rounding::class);
        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice);

        $this->assertNull($roundingPrice);
    }

    public function testCalculateOrderRoundingPriceDown()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_CZK;
        $currencyData->exchangeRate = 1.0;
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::fromInteger(100), Money::fromString('120.3'));

        $roundingMock = $this->getMockBuilder(Rounding::class)
            ->setMethods(['roundPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
            return round($value, 2);
        });

        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertThat($roundingPrice, new IsMoneyEqual(Money::fromString('-0.3')));
    }

    public function testCalculateOrderRoundingPriceUp()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_CZK;
        $currencyData->exchangeRate = 1.0;
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::fromInteger(100), Money::fromString('120.9'));

        $roundingMock = $this->getMockBuilder(Rounding::class)
            ->setMethods(['roundPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
            return round($value, 2);
        });

        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertThat($roundingPrice, new IsMoneyEqual(Money::fromString('0.1')));
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderProductMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypeProduct')->willReturn(true);

        return $orderProductMock;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderPaymentMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypePayment')->willReturn(true);

        return $orderProductMock;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderTransportMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypeTransport')->willReturn(true);

        return $orderProductMock;
    }
}
