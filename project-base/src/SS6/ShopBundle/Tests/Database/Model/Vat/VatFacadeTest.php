<?php

namespace SS6\ShopBundle\Tests\Database\Model\Vat;

use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\PaymentDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\TransportDataFixture;
use SS6\ShopBundle\Model\Payment\PaymentEditDataFactory;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Transport\TransportEditDataFactory;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class VatFacadeTest extends DatabaseTestCase {

	public function testDeleteByIdAndReplace() {
		$em = $this->getEntityManager();
		$vatFacade = $this->getContainer()->get(VatFacade::class);
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */
		$transportEditFacade = $this->getContainer()->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportEditDataFactory = $this->getContainer()->get(TransportEditDataFactory::class);
		/* @var $transportEditDataFactory \SS6\ShopBundle\Model\Transport\TransportEditDataFactory */
		$paymentEditDataFactory = $this->getContainer()->get(PaymentEditDataFactory::class);
		/* @var $paymentEditDataFactory \SS6\ShopBundle\Model\Payment\PaymentEditDataFactory */
		$paymentEditFacade = $this->getContainer()->get(PaymentEditFacade::class);
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$vatToDelete = $vatFacade->create(new VatData('name', 10));
		$vatToReplaceWith = $this->getReference(VatDataFixture::VAT_HIGH);
		/* @var $vatToReplaceWith \SS6\ShopBundle\Model\Pricing\Vat\Vat */
		$transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
		$transportEditData = $transportEditDataFactory->createFromTransport($transport);
		$payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
		$paymentEditData = $paymentEditDataFactory->createFromPayment($payment);

		$transportEditData->transportData->vat = $vatToDelete;
		$transportEditFacade->edit($transport, $transportEditData);

		$paymentEditData->paymentData->vat = $vatToDelete;
		$paymentEditFacade->edit($payment, $paymentEditData);

		$vatFacade->deleteById($vatToDelete, $vatToReplaceWith);

		$em->refresh($transport);
		$em->refresh($payment);

		$this->assertEquals($vatToReplaceWith, $transport->getVat());
		$this->assertEquals($vatToReplaceWith, $payment->getVat());
	}
}
