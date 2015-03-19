<?php

namespace SS6\ShopBundle\TestsDb\Model\Pricing;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\Model\Payment\PaymentEditData;
use SS6\ShopBundle\Model\Pricing\InputPriceFacade;
use SS6\ShopBundle\Model\Pricing\InputPriceRecalculator;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Setting\SettingValue;
use SS6\ShopBundle\Model\Transport\TransportEditData;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class InputPriceFacadeTest extends DatabaseTestCase {

	public function testOnKernelResponseNoAction() {
		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */

		$inputPriceRecalculatorMock = $this->getMockBuilder(InputPriceRecalculator::class)
			->setMethods(['__construct', 'recalculateToInputPricesWithoutVat', 'recalculateToInputPricesWithVat'])
			->disableOriginalConstructor()
			->getMock();
		$inputPriceRecalculatorMock->expects($this->never())->method('recalculateToInputPricesWithoutVat');
		$inputPriceRecalculatorMock->expects($this->never())->method('recalculateToInputPricesWithVat');

		$filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
			->disableOriginalConstructor()
			->getMock();

		$inputPriceFacade = new InputPriceFacade($inputPriceRecalculatorMock, $setting);

		$inputPriceFacade->onKernelResponse($filterResponseEventMock);
	}

	public function inputPricesTestDataProvider() {
		return [
			['inputPriceWithoutVat' => '100', 'inputPriceWithVat' => '121', 'vatPercent' => '21'],
			['inputPriceWithoutVat' => '17261.983471', 'inputPriceWithVat' => '20887', 'vatPercent' => '21'],
		];
	}

	/**
	 * @dataProvider inputPricesTestDataProvider
	 */
	public function testOnKernelResponseRecalculateInputPricesWithoutVat(
		$inputPriceWithoutVat,
		$inputPriceWithVat,
		$vatPercent
	) {
		$em = $this->getEntityManager();

		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */
		$inputPriceFacade = $this->getContainer()->get('ss6.shop.pricing.input_price_facade');
		/* @var $inputPriceFacade \SS6\ShopBundle\Model\Pricing\InputPriceFacade */
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$paymentEditFacade = $this->getContainer()->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$paymentRepository = $this->getContainer()->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$transportEditFacade = $this->getContainer()->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportRepository = $this->getContainer()->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT, SettingValue::DOMAIN_ID_COMMON);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$em->persist($vat);

		$currency1 = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$currency2 = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'name'];
		$productEditData->productData->price = $inputPriceWithVat;
		$productEditData->productData->vat = $vat;
		$product = $productEditFacade->create($productEditData);
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$paymentEditData = new PaymentEditData();
		$paymentEditData->paymentData->name = ['cs' => 'name'];
		$paymentEditData->prices = [$currency1->getId() => $inputPriceWithVat, $currency2->getId() => $inputPriceWithVat];
		$paymentEditData->paymentData->vat = $vat;
		$payment = $paymentEditFacade->create($paymentEditData);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$transportEditData = new \SS6\ShopBundle\Model\Transport\TransportEditData();
		$transportEditData->transportData->name = ['cs' => 'name'];
		$transportEditData->transportData->description = ['cs' => 'desc'];
		$transportEditData->prices = [$currency1->getId() => $inputPriceWithVat, $currency2->getId() => $inputPriceWithVat];
		$transportEditData->transportData->vat = $vat;
		$transport = $transportEditFacade->create($transportEditData);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
		$em->flush();

		$filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
			->disableOriginalConstructor()
			->getMock();

		$inputPriceFacade->scheduleSetInputPricesWithoutVat();
		$inputPriceFacade->onKernelResponse($filterResponseEventMock);

		$product2 = $productRepository->getById($product->getId());
		$payment2 = $paymentRepository->getById($payment->getId());
		$transport2 = $transportRepository->getById($transport->getId());

		$this->assertEquals(round($inputPriceWithoutVat, 6), round($product2->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithoutVat, 6), round($payment2->getPrice($currency1)->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithoutVat, 6), round($transport2->getPrice($currency1)->getPrice(), 6));
	}

	/**
	 * @dataProvider inputPricesTestDataProvider
	 */
	public function testOnKernelResponseRecalculateInputPricesWithVat(
		$inputPriceWithoutVat,
		$inputPriceWithVat,
		$vatPercent
	) {
		$em = $this->getEntityManager();

		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */
		$inputPriceFacade = $this->getContainer()->get('ss6.shop.pricing.input_price_facade');
		/* @var $inputPriceFacade \SS6\ShopBundle\Model\Pricing\InputPriceFacade */
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productRepository = $this->getContainer()->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$paymentEditFacade = $this->getContainer()->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$paymentRepository = $this->getContainer()->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$transportEditFacade = $this->getContainer()->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportRepository = $this->getContainer()->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT, SettingValue::DOMAIN_ID_COMMON);

		$currency1 = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$currency2 = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$em->persist($vat);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'name'];
		$productEditData->productData->price = $inputPriceWithoutVat;
		$productEditData->productData->vat = $vat;
		$product = $productEditFacade->create($productEditData);
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$paymentEditData = new PaymentEditData();
		$paymentEditData->paymentData->name = ['cs' => 'name'];
		$paymentEditData->prices = [$currency1->getId() => $inputPriceWithoutVat, $currency2->getId() => $inputPriceWithoutVat];
		$paymentEditData->paymentData->vat = $vat;
		$payment = $paymentEditFacade->create($paymentEditData);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$transportEditData = new TransportEditData();
		$transportEditData->transportData->name = ['cs' => 'name'];
		$transportEditData->prices = [$currency1->getId() => $inputPriceWithoutVat, $currency2->getId() => $inputPriceWithoutVat];
		$transportEditData->transportData->vat = $vat;
		$transport = $transportEditFacade->create($transportEditData);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */

		$em->flush();

		$filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
			->disableOriginalConstructor()
			->getMock();

		$inputPriceFacade->scheduleSetInputPricesWithVat();
		$inputPriceFacade->onKernelResponse($filterResponseEventMock);

		$product2 = $productRepository->getById($product->getId());
		$payment2 = $paymentRepository->getById($payment->getId());
		$transport2 = $transportRepository->getById($transport->getId());

		$this->assertEquals(round($inputPriceWithVat, 6), round($product2->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithVat, 6), round($payment2->getPrice($currency1)->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithVat, 6), round($transport2->getPrice($currency1)->getPrice(), 6));
	}

}
