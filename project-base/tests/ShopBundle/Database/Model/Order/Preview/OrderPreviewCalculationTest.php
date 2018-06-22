<?php

namespace Tests\ShopBundle\Database\Model\Order\Preview;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewCalculation;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Tests\ShopBundle\Test\FunctionalTestCase;

class OrderPreviewCalculationTest extends FunctionalTestCase
{
    public function testCalculatePreviewWithTransportAndPayment()
    {
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */
        $vatData = new VatData();
        $vatData->name = 'vatName';
        $vatData->percent = 20;
        $vat = new Vat($vatData);

        $paymentPrice = new Price(100, 120);
        $transportPrice = new Price(10, 12);
        $unitPrice = new Price(1000, 1200);
        $totalPrice = new Price(2000, 2400);
        $quantifiedItemPrice = new QuantifiedItemPrice($unitPrice, $totalPrice, $vat);
        $quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
        $quantifiedProductsDiscounts = [null, null];
        $currency = new Currency(new CurrencyData());

        $quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
            ->setMethods(['calculatePrices', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
            ->will($this->returnValue($quantifiedItemsPrices));

        $quantifiedProductDiscountCalculationMock = $this->getMockBuilder(QuantifiedProductDiscountCalculation::class)
            ->setMethods(['calculateDiscounts', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductDiscountCalculationMock->expects($this->once())->method('calculateDiscounts')
            ->will($this->returnValue($quantifiedProductsDiscounts));

        $paymentPriceCalculationMock = $this->getMockBuilder(PaymentPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $paymentPriceCalculationMock->expects($this->once())->method('calculatePrice')->will($this->returnValue($paymentPrice));

        $transportPriceCalculationMock = $this->getMockBuilder(TransportPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $transportPriceCalculationMock->expects($this->once())->method('calculatePrice')->will($this->returnValue($transportPrice));

        $orderPriceCalculationMock = $this->getMockBuilder(OrderPriceCalculation::class)
            ->setMethods(['calculateOrderRoundingPrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderPriceCalculationMock->expects($this->any())->method('calculateOrderRoundingPrice')->willReturn(null);

        $previewCalculation = new OrderPreviewCalculation(
            $quantifiedProductPriceCalculationMock,
            $quantifiedProductDiscountCalculationMock,
            $transportPriceCalculationMock,
            $paymentPriceCalculationMock,
            $orderPriceCalculationMock
        );

        $quantifiedProductMock = $this->createMock(QuantifiedProduct::class);
        $quantifiedProducts = [
            $quantifiedProductMock,
            $quantifiedProductMock,
        ];

        $transport = $this->createMock(Transport::class);
        $payment = $this->createMock(Payment::class);

        $orderPreview = $previewCalculation->calculatePreview(
            $currency,
            $domain->getId(),
            $quantifiedProducts,
            $transport,
            $payment,
            null
        );

        $this->assertSame($quantifiedProducts, $orderPreview->getQuantifiedProducts());
        $this->assertSame($quantifiedItemsPrices, $orderPreview->getQuantifiedItemsPrices());
        $this->assertSame($payment, $orderPreview->getPayment());
        $this->assertSame($paymentPrice, $orderPreview->getPaymentPrice());
        $this->assertSame(2 + 20 + 400 * 2, $orderPreview->getTotalPrice()->getVatAmount());
        $this->assertSame(12 + 120 + 2400 * 2, $orderPreview->getTotalPrice()->getPriceWithVat());
        $this->assertSame(10 + 100 + 2000 * 2, $orderPreview->getTotalPrice()->getPriceWithoutVat());
        $this->assertSame($transport, $orderPreview->getTransport());
        $this->assertSame($transportPrice, $orderPreview->getTransportPrice());
    }

    public function testCalculatePreviewWithoutTransportAndPayment()
    {
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */
        $vatData = new VatData();
        $vatData->name = 'vatName';
        $vatData->percent = 20;
        $vat = new Vat($vatData);

        $unitPrice = new Price(1000, 1200);
        $totalPrice = new Price(2000, 2400);
        $quantifiedItemPrice = new QuantifiedItemPrice($unitPrice, $totalPrice, $vat);
        $quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
        $quantifiedProductsDiscounts = [null, null];
        $currency = new Currency(new CurrencyData());

        $quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
            ->setMethods(['calculatePrices', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
            ->will($this->returnValue($quantifiedItemsPrices));

        $quantifiedProductDiscountCalculationMock = $this->getMockBuilder(QuantifiedProductDiscountCalculation::class)
            ->setMethods(['calculateDiscounts', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductDiscountCalculationMock->expects($this->once())->method('calculateDiscounts')
            ->will($this->returnValue($quantifiedProductsDiscounts));

        $paymentPriceCalculationMock = $this->getMockBuilder(PaymentPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $paymentPriceCalculationMock->expects($this->never())->method('calculatePrice');

        $transportPriceCalculationMock = $this->getMockBuilder(TransportPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $transportPriceCalculationMock->expects($this->never())->method('calculatePrice');

        $orderPriceCalculationMock = $this->createMock(OrderPriceCalculation::class);

        $previewCalculation = new OrderPreviewCalculation(
            $quantifiedProductPriceCalculationMock,
            $quantifiedProductDiscountCalculationMock,
            $transportPriceCalculationMock,
            $paymentPriceCalculationMock,
            $orderPriceCalculationMock
        );

        $quantifiedProductMock = $this->createMock(QuantifiedProduct::class);
        $quantifiedProducts = [
            $quantifiedProductMock,
            $quantifiedProductMock,
        ];

        $orderPreview = $previewCalculation->calculatePreview(
            $currency,
            $domain->getId(),
            $quantifiedProducts,
            null,
            null,
            null
        );

        $this->assertSame($quantifiedProducts, $orderPreview->getQuantifiedProducts());
        $this->assertSame($quantifiedItemsPrices, $orderPreview->getQuantifiedItemsPrices());
        $this->assertNull($orderPreview->getPayment());
        $this->assertNull($orderPreview->getPaymentPrice());
        $this->assertSame(400 * 2, $orderPreview->getTotalPrice()->getVatAmount());
        $this->assertSame(2400 * 2, $orderPreview->getTotalPrice()->getPriceWithVat());
        $this->assertSame(2000 * 2, $orderPreview->getTotalPrice()->getPriceWithoutVat());
        $this->assertNull($orderPreview->getTransport());
        $this->assertNull($orderPreview->getTransportPrice());
    }
}
