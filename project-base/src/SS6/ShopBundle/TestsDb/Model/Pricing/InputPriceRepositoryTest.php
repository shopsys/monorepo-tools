<?php

namespace SS6\ShopBundle\TestsDb\Model\Pricing;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class InputPriceRepositoryTest extends DatabaseTestCase {

	public function testRecalculateToInputPricesWithoutVat() {
		$em = $this->getEntityManager();

		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting SS6\ShopBundle\Model\Setting\Setting3 */
		$inputPriceRepository = $this->getContainer()->get('ss6.shop.pricing.input_price_repository');
		/* @var $inputPriceRepository \SS6\ShopBundle\Model\Pricing\InputPriceRepository */
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

		$vat = new Vat('vat', 21);
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('name');
		$productData->setPrice(121);
		$productData->setVat($vat);
		$product = $productEditFacade->create($productData);

		$paymentData = new PaymentData();
		$paymentData->setName('name');
		$paymentData->setPrice(121);
		$paymentData->setVat($vat);
		$payment = new Payment($paymentData);
		$paymentEditFacade->create($payment);

		$transportData = new TransportData();
		$transportData->setName('name');
		$transportData->setPrice(121);
		$transportData->setVat($vat);
		$transport = new Transport($transportData);
		$transportEditFacade->create($transport);

		$em->flush();

		$inputPriceRepository->recalculateToInputPricesWithoutVat();

		$product2 = $productRepository->getById($product->getId());
		$payment2 = $paymentRepository->getById($payment->getId());
		$transport2 = $transportRepository->getById($transport->getId());

		$this->assertEquals(round(99.99, 6), round($product2->getPrice(), 6));
		$this->assertEquals(round(99.99, 6), round($payment2->getPrice(), 6));
		$this->assertEquals(round(99.99, 6), round($transport2->getPrice(), 6));
	}

	public function testRecalculateToInputPricesWithVat() {
		$em = $this->getEntityManager();

		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting SS6\ShopBundle\Model\Setting\Setting3 */
		$inputPriceRepository = $this->getContainer()->get('ss6.shop.pricing.input_price_repository');
		/* @var $inputPriceRepository \SS6\ShopBundle\Model\Pricing\InputPriceRepository */
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

		$vat = new Vat('vat', 21);
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('name');
		$productData->setPrice(100);
		$productData->setVat($vat);
		$product = $productEditFacade->create($productData);

		$paymentData = new PaymentData();
		$paymentData->setName('name');
		$paymentData->setPrice(100);
		$paymentData->setVat($vat);
		$payment = new Payment($paymentData);
		$paymentEditFacade->create($payment);

		$transportData = new TransportData();
		$transportData->setName('name');
		$transportData->setPrice(100);
		$transportData->setVat($vat);
		$transport = new Transport($transportData);
		$transportEditFacade->create($transport);

		$em->flush();

		$inputPriceRepository->recalculateToInputPricesWithVat();

		$product2 = $productRepository->getById($product->getId());
		$payment2 = $paymentRepository->getById($payment->getId());
		$transport2 = $transportRepository->getById($transport->getId());

		$this->assertEquals(round(121, 6), round($product2->getPrice(), 6));
		$this->assertEquals(round(121, 6), round($payment2->getPrice(), 6));
		$this->assertEquals(round(121, 6), round($transport2->getPrice(), 6));
	}

}
