<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Model\Order\Item;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\WrongItemTypeException;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class OrderItemTest extends TestCase
{
    public function testTransportCannotBeSetForWrongType(): void
    {
        $orderItem = $this->createOrderPayment();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->setTransport($this->createTransportMock());
    }

    public function testTransportCannotBeGottenFromWrongType(): void
    {
        $orderItem = $this->createOrderPayment();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->getTransport();
    }

    public function testEditTransportTypeEditsTransport(): void
    {
        $orderItem = $this->createOrderTransport();

        $orderItemData = new OrderItemData();
        $orderItemData->priceWithVat = Money::zero();
        $orderItemData->priceWithoutVat = Money::zero();
        $transport = $this->createTransportMock();
        $orderItemData->transport = $transport;
        $orderItem->edit($orderItemData);

        $this->assertSame($transport, $orderItem->getTransport());
    }

    public function testPaymentCannotBeSetForWrongType(): void
    {
        $orderItem = $this->createOrderProduct();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->setPayment($this->createPaymentMock());
    }

    public function testPaymentCannotBeGottenFromWrongType(): void
    {
        $orderItem = $this->createOrderProduct();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->getPayment();
    }

    public function testEditPaymentTypeEditsPayment(): void
    {
        $orderItem = $this->createOrderPayment();

        $orderItemData = new OrderItemData();
        $orderItemData->priceWithVat = Money::zero();
        $orderItemData->priceWithoutVat = Money::zero();
        $payment = $this->createPaymentMock();
        $orderItemData->payment = $payment;
        $orderItem->edit($orderItemData);

        $this->assertSame($payment, $orderItem->getPayment());
    }

    public function testProductCannotBeSetForWrongType(): void
    {
        $orderItem = $this->createOrderTransport();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->setProduct($this->createProductMock());
    }

    public function testProductCannotBeGottenFromWrongType(): void
    {
        $orderItem = $this->createOrderTransport();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->getProduct();
    }

    public function testProductCannotHaveProduct(): void
    {
        $orderItem = $this->createOrderTransport();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->hasProduct();
    }

    public function testEditProductTypeWithProduct()
    {
        $orderItemData = new OrderItemData();
        $orderItemData->name = 'newName';
        $orderItemData->priceWithVat = Money::fromInteger(20);
        $orderItemData->priceWithoutVat = Money::fromInteger(30);
        $orderItemData->quantity = 2;
        $orderItemData->vatPercent = 10;

        $orderItem = $this->createOrderProduct($this->createProductMock());
        $orderItem->edit($orderItemData);

        $this->assertSame('newName', $orderItem->getName());
        $this->assertThat($orderItem->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(20)));
        $this->assertThat($orderItem->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(30)));
        $this->assertSame(2, $orderItem->getQuantity());
        $this->assertSame(10, $orderItem->getvatPercent());
    }

    public function testEditProductTypeWithoutProduct()
    {
        $orderItemData = new OrderItemData();
        $orderItemData->name = 'newName';
        $orderItemData->priceWithVat = Money::fromInteger(20);
        $orderItemData->priceWithoutVat = Money::fromInteger(30);
        $orderItemData->quantity = 2;
        $orderItemData->vatPercent = 10;

        $orderItem = $this->createOrderProduct();
        $orderItem->edit($orderItemData);

        $this->assertSame('newName', $orderItem->getName());
        $this->assertThat($orderItem->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(20)));
        $this->assertThat($orderItem->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(30)));
        $this->assertSame(2, $orderItem->getQuantity());
        $this->assertSame(10, $orderItem->getvatPercent());
    }

    public function testConstructWithMainVariantThrowsException()
    {
        $variant = Product::create(new ProductData(), new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant(new ProductData(), new ProductCategoryDomainFactory(), [$variant]);

        $this->expectException(MainVariantCannotBeOrderedException::class);

        $this->createOrderProduct($mainVariant);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderPayment(): OrderItem
    {
        $orderPayment = new OrderItem(
            $this->createOrderMock(),
            '',
            new Price(Money::fromInteger(10), Money::fromInteger(12)),
            0.2,
            1,
            OrderItem::TYPE_PAYMENT,
            null,
            null
        );
        $orderPayment->setPayment($this->createPaymentMock());
        return $orderPayment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderTransport(): OrderItem
    {
        $orderTransport = new OrderItem(
            $this->createOrderMock(),
            '',
            new Price(Money::fromInteger(10), Money::fromInteger(12)),
            0.2,
            1,
            OrderItem::TYPE_TRANSPORT,
            null,
            null
        );
        $orderTransport->setTransport($this->createTransportMock());
        return $orderTransport;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderProduct(Product $product = null): OrderItem
    {
        $orderProduct = new OrderItem(
            $this->createOrderMock(),
            '',
            new Price(Money::fromInteger(10), Money::fromInteger(12)),
            0.2,
            1,
            OrderItem::TYPE_PRODUCT,
            null,
            null
        );
        $orderProduct->setProduct($product);
        return $orderProduct;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createOrderMock(): MockObject
    {
        return $this->createMock(Order::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createTransportMock(): MockObject
    {
        return $this->createMock(Transport::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createPaymentMock(): MockObject
    {
        return $this->createMock(Payment::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductMock(): MockObject
    {
        return $this->createMock(Product::class);
    }
}
