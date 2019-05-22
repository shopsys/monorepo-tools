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
            [$orderItems[0], new Price(Money::create(150), Money::create(200))],
            [$orderItems[1], new Price(Money::create(1000), Money::create(3000))],
            [$orderItems[2], new Price(Money::create(15), Money::create(20))],
            [$orderItems[3], new Price(Money::create(0), Money::create(0))],
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

        $this->assertThat($orderTotalPrice->getPriceWithVat(), new IsMoneyEqual(Money::create(3220)));
        $this->assertThat($orderTotalPrice->getPriceWithoutVat(), new IsMoneyEqual(Money::create(1165)));
        $this->assertThat($orderTotalPrice->getProductPriceWithVat(), new IsMoneyEqual(Money::create(3200)));
    }

    public function testCalculateOrderRoundingPriceForOtherCurrency()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_EUR;
        $currencyData->exchangeRate = '1.0';
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::create(100), Money::create(120));

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
        $currencyData->exchangeRate = '1.0';
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::create(100), Money::create(120));

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
        $currencyData->exchangeRate = '1.0';
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::create(100), Money::create('120.3'));

        $roundingMock = $this->getMockBuilder(Rounding::class)
            ->setMethods(['roundPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function (Money $value) {
            return $value->round(2);
        });

        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertThat($roundingPrice, new IsMoneyEqual(Money::create('-0.3')));
    }

    public function testCalculateOrderRoundingPriceUp()
    {
        $paymentData = new PaymentData();
        $paymentData->czkRounding = true;
        $payment = new Payment($paymentData);

        $currencyData = new CurrencyData();
        $currencyData->name = 'currencyName';
        $currencyData->code = Currency::CODE_CZK;
        $currencyData->exchangeRate = '1.0';
        $currency = new Currency($currencyData);
        $orderTotalPrice = new Price(Money::create(100), Money::create('120.9'));

        $roundingMock = $this->getMockBuilder(Rounding::class)
            ->setMethods(['roundPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $roundingMock->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function (Money $value) {
            return $value->round(2);
        });

        $orderItemPriceCalculationMock = $this->createMock(OrderItemPriceCalculation::class);

        $priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock, $roundingMock);
        $roundingPrice = $priceCalculation->calculateOrderRoundingPrice($payment, $currency, $orderTotalPrice)->getPriceWithVat();

        $this->assertThat($roundingPrice, new IsMoneyEqual(Money::create('0.1')));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderProductMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypeProduct')->willReturn(true);

        return $orderProductMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderPaymentMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypePayment')->willReturn(true);

        return $orderProductMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOrderTransportMock(): MockObject
    {
        $orderProductMock = $this->createMock(OrderItem::class);

        $orderProductMock->method('isTypeTransport')->willReturn(true);

        return $orderProductMock;
    }
}
