<?php

namespace Tests\FrameworkBundle\Unit\Model\Order\Item;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatDataFactory;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFactory;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class OrderItemPriceCalculationTest extends TestCase
{
    public function testCalculatePriceWithoutVat()
    {
        $priceCalculationMock = $this->getMockBuilder(PriceCalculation::class)
            ->setMethods(['getVatAmountByPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(Money::fromInteger(100));

        $orderItemData = new OrderItemData();
        $orderItemData->priceWithVat = Money::fromInteger(1000);
        $orderItemData->vatPercent = 10;

        $orderItemPriceCalculation = new OrderItemPriceCalculation($priceCalculationMock, new VatFactory(new EntityNameResolver([])), new VatDataFactory());
        $priceWithoutVat = $orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);

        $this->assertThat($priceWithoutVat, new IsMoneyEqual(Money::fromInteger(900)));
    }

    public function testCalculateTotalPrice()
    {
        $priceCalculationMock = $this->getMockBuilder(PriceCalculation::class)
            ->setMethods(['getVatAmountByPriceWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(Money::fromInteger(10));

        $orderItemPriceCalculation = new OrderItemPriceCalculation($priceCalculationMock, new VatFactory(new EntityNameResolver([])), new VatDataFactory());

        $orderItem = $this->getMockForAbstractClass(
            OrderItem::class,
            [],
            '',
            false,
            true,
            true,
            ['getPriceWithVat', 'getQuantity', 'getVatPercent']
        );
        $orderItem->expects($this->once())->method('getPriceWithVat')->willReturn(Money::fromInteger(100));
        $orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $orderItem->expects($this->once())->method('getVatPercent')->willReturn(1);

        $totalPrice = $orderItemPriceCalculation->calculateTotalPrice($orderItem);

        $this->assertThat($totalPrice->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(200)));
        $this->assertThat($totalPrice->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(190)));
        $this->assertThat($totalPrice->getVatAmount(), new IsMoneyEqual(Money::fromInteger(10)));
    }
}
