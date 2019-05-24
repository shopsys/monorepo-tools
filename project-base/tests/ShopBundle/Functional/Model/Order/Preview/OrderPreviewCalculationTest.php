<?php

namespace Tests\ShopBundle\Functional\Model\Order\Preview;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewCalculation;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Tests\FrameworkBundle\Test\IsMoneyEqual;
use Tests\ShopBundle\Test\FunctionalTestCase;

class OrderPreviewCalculationTest extends FunctionalTestCase
{
    public function testCalculatePreviewWithTransportAndPayment()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */
        $domain = $this->getContainer()->get(Domain::class);
        $vatData = new VatData();
        $vatData->name = 'vatName';
        $vatData->percent = '20';
        $vat = new Vat($vatData);

        $paymentPrice = new Price(Money::create(100), Money::create(120));
        $transportPrice = new Price(Money::create(10), Money::create(12));
        $unitPrice = new Price(Money::create(1000), Money::create(1200));
        $totalPrice = new Price(Money::create(2000), Money::create(2400));
        $quantifiedItemPrice = new QuantifiedItemPrice($unitPrice, $totalPrice, $vat);
        $quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
        $quantifiedProductsDiscounts = [null, null];
        $currency = new Currency(new CurrencyData());

        $quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
            ->setMethods(['calculatePrices', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
            ->willReturn($quantifiedItemsPrices);

        $quantifiedProductDiscountCalculationMock = $this->getMockBuilder(QuantifiedProductDiscountCalculation::class)
            ->setMethods(['calculateDiscounts', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductDiscountCalculationMock->expects($this->once())->method('calculateDiscounts')
            ->willReturn($quantifiedProductsDiscounts);

        $paymentPriceCalculationMock = $this->getMockBuilder(PaymentPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $paymentPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($paymentPrice);

        $transportPriceCalculationMock = $this->getMockBuilder(TransportPriceCalculation::class)
            ->setMethods(['calculatePrice', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $transportPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($transportPrice);

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
        $this->assertThat($orderPreview->getTotalPrice()->getVatAmount(), new IsMoneyEqual(Money::create(2 + 20 + 400 * 2)));
        $this->assertThat($orderPreview->getTotalPrice()->getPriceWithVat(), new IsMoneyEqual(Money::create(12 + 120 + 2400 * 2)));
        $this->assertThat($orderPreview->getTotalPrice()->getPriceWithoutVat(), new IsMoneyEqual(Money::create(10 + 100 + 2000 * 2)));
        $this->assertSame($transport, $orderPreview->getTransport());
        $this->assertSame($transportPrice, $orderPreview->getTransportPrice());
    }

    public function testCalculatePreviewWithoutTransportAndPayment()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */
        $domain = $this->getContainer()->get(Domain::class);
        $vatData = new VatData();
        $vatData->name = 'vatName';
        $vatData->percent = '20';
        $vat = new Vat($vatData);

        $unitPrice = new Price(Money::create(1000), Money::create(1200));
        $totalPrice = new Price(Money::create(2000), Money::create(2400));
        $quantifiedItemPrice = new QuantifiedItemPrice($unitPrice, $totalPrice, $vat);
        $quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
        $quantifiedProductsDiscounts = [null, null];
        $currency = new Currency(new CurrencyData());

        $quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
            ->setMethods(['calculatePrices', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
            ->willReturn($quantifiedItemsPrices);

        $quantifiedProductDiscountCalculationMock = $this->getMockBuilder(QuantifiedProductDiscountCalculation::class)
            ->setMethods(['calculateDiscounts', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $quantifiedProductDiscountCalculationMock->expects($this->once())->method('calculateDiscounts')
            ->willReturn($quantifiedProductsDiscounts);

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
        $this->assertThat($orderPreview->getTotalPrice()->getVatAmount(), new IsMoneyEqual(Money::create(400 * 2)));
        $this->assertThat($orderPreview->getTotalPrice()->getPriceWithVat(), new IsMoneyEqual(Money::create(2400 * 2)));
        $this->assertThat($orderPreview->getTotalPrice()->getPriceWithoutVat(), new IsMoneyEqual(Money::create(2000 * 2)));
        $this->assertNull($orderPreview->getTransport());
        $this->assertNull($orderPreview->getTransportPrice());
    }
}
