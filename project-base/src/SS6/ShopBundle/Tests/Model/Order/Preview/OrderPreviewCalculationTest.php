<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Cart\Item\CartItemPrice;
use SS6\ShopBundle\Model\Order\Item\QuantifiedItem;
use SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderPreviewCalculationTest extends FunctionalTestCase {

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testCalculatePreviewWithTransportAndPayment() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$paymentPrice = new Price(100, 120, 20);
		$transportPrice = new Price(10, 12, 2);
		$quantifiedItemPrice = new QuantifiedItemPrice(1000, 1200, 200, 2000, 2400, 400);
		$quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
		$currency = new Currency(new CurrencyData());

		$quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
			->setMethods(['calculatePrices', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
			->will($this->returnValue($quantifiedItemsPrices));

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

		$previewCalculation = new OrderPreviewCalculation(
			$quantifiedProductPriceCalculationMock,
			$transportPriceCalculationMock,
			$paymentPriceCalculationMock
		);

		$quantifiedItemMock = $this->getMock(QuantifiedItem::class, [], [], '', false);
		$quantifiedItems = [
			$quantifiedItemMock,
			$quantifiedItemMock,
		];

		$transport = $this->getMock(Transport::class, [], [], '', false);
		$payment = $this->getMock(Payment::class, [], [], '', false);

		$orderPreview = $previewCalculation->calculatePreview(
			$currency,
			$domain->getId(),
			$quantifiedItems,
			$transport,
			$payment,
			null
		);

		$this->assertSame($quantifiedItems, $orderPreview->getQuantifiedItems());
		$this->assertSame($quantifiedItemsPrices, $orderPreview->getQuantifiedItemsPrices());
		$this->assertSame($payment, $orderPreview->getPayment());
		$this->assertSame($paymentPrice, $orderPreview->getPaymentPrice());
		$this->assertSame(2 + 20 + 400 * 2, $orderPreview->getTotalPrice()->getVatAmount());
		$this->assertSame(12 + 120 + 2400 * 2, $orderPreview->getTotalPrice()->getPriceWithVat());
		$this->assertSame(10 + 100 + 2000 * 2, $orderPreview->getTotalPrice()->getPriceWithoutVat());
		$this->assertSame($transport, $orderPreview->getTransport());
		$this->assertSame($transportPrice, $orderPreview->getTransportPrice());
	}

	public function testCalculatePreviewWithoutTransportAndPayment() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$quantifiedItemPrice = new CartItemPrice(1000, 1200, 200, 2000, 2400, 400);
		$quantifiedItemsPrices = [$quantifiedItemPrice, $quantifiedItemPrice];
		$currency = new Currency(new CurrencyData());

		$quantifiedProductPriceCalculationMock = $this->getMockBuilder(QuantifiedProductPriceCalculation::class)
			->setMethods(['calculatePrices', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$quantifiedProductPriceCalculationMock->expects($this->once())->method('calculatePrices')
			->will($this->returnValue($quantifiedItemsPrices));

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

		$previewCalculation = new OrderPreviewCalculation(
			$quantifiedProductPriceCalculationMock,
			$transportPriceCalculationMock,
			$paymentPriceCalculationMock
		);

		$quantifiedItemMock = $this->getMock(QuantifiedItem::class, [], [], '', false);
		$quantifiedItems = [
			$quantifiedItemMock,
			$quantifiedItemMock,
		];

		$orderPreview = $previewCalculation->calculatePreview(
			$currency,
			$domain->getId(),
			$quantifiedItems,
			null,
			null,
			null
		);

		$this->assertSame($quantifiedItems, $orderPreview->getQuantifiedItems());
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
