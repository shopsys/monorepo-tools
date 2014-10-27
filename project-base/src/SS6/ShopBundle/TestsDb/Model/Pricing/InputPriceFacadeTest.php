<?php

namespace SS6\ShopBundle\TestsDb\Model\Pricing;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\InputPriceRepository;
use SS6\ShopBundle\Model\Pricing\InputPriceFacade;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Transport\TransportData;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class InputPriceFacadeTest extends DatabaseTestCase {

	public function testOnKernelResponseNoAction() {
		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */

		$inputPriceRepositoryMock = $this->getMockBuilder(InputPriceRepository::class)
			->setMethods(array('__construct', 'recalculateToInputPricesWithoutVat', 'recalculateToInputPricesWithVat'))
			->disableOriginalConstructor()
			->getMock();
		$inputPriceRepositoryMock->expects($this->never())->method('recalculateToInputPricesWithoutVat');
		$inputPriceRepositoryMock->expects($this->never())->method('recalculateToInputPricesWithVat');

		$filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
			->disableOriginalConstructor()
			->getMock();

		$inputPriceFacade = new InputPriceFacade($inputPriceRepositoryMock, $setting);

		$inputPriceFacade->onKernelResponse($filterResponseEventMock);
	}

	public function inputPricesTestDataProvider() {
		return array(
			array('inputPriceWithoutVat' => '100', 'inputPriceWithVat' => '121', 'vatPercent' => '21'),
			array('inputPriceWithoutVat' => '17261.983471', 'inputPriceWithVat' => '20887', 'vatPercent' => '21'),
		);
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
		/* @var $setting SS6\ShopBundle\Model\Setting\Setting */
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

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('name');
		$productData->setPrice($inputPriceWithVat);
		$productData->setVat($vat);
		$product = $productEditFacade->create($productData);
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$paymentData = new PaymentData();
		$paymentData->setName('name');
		$paymentData->setPrice($inputPriceWithVat);
		$paymentData->setVat($vat);
		$payment = $paymentEditFacade->create($paymentData);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$transportData = new TransportData();
		$transportData->setNames(array('cs' => 'name'));
		$transportData->setDescriptions(array('cs' => 'desc'));
		$transportData->setPrice($inputPriceWithVat);
		$transportData->setVat($vat);
		$transport = $transportEditFacade->create($transportData);
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
		$this->assertEquals(round($inputPriceWithoutVat, 6), round($payment2->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithoutVat, 6), round($transport2->getPrice(), 6));
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
		/* @var $setting SS6\ShopBundle\Model\Setting\Setting */
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

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('name');
		$productData->setPrice($inputPriceWithoutVat);
		$productData->setVat($vat);
		$product = $productEditFacade->create($productData);
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$paymentData = new PaymentData();
		$paymentData->setName('name');
		$paymentData->setPrice($inputPriceWithoutVat);
		$paymentData->setVat($vat);
		$payment = $paymentEditFacade->create($paymentData);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$transportData = new TransportData();
		$transportData->setNames(array('cs' => 'name'));
		$transportData->setPrice($inputPriceWithoutVat);
		$transportData->setVat($vat);
		$transport = $transportEditFacade->create($transportData);
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
		$this->assertEquals(round($inputPriceWithVat, 6), round($payment2->getPrice(), 6));
		$this->assertEquals(round($inputPriceWithVat, 6), round($transport2->getPrice(), 6));
	}

}
