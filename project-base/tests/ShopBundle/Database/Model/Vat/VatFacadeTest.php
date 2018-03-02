<?php

namespace Tests\ShopBundle\Database\Model\Vat;

use Shopsys\FrameworkBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PaymentDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\TransportDataFixture;
use Shopsys\FrameworkBundle\Model\Payment\PaymentEditDataFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportEditDataFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class VatFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $vatFacade = $this->getServiceByType(VatFacade::class);
        /* @var $vatFacade \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade */
        $transportFacade = $this->getServiceByType(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $transportEditDataFactory = $this->getServiceByType(TransportEditDataFactory::class);
        /* @var $transportEditDataFactory \Shopsys\FrameworkBundle\Model\Transport\TransportEditDataFactory */
        $paymentEditDataFactory = $this->getServiceByType(PaymentEditDataFactory::class);
        /* @var $paymentEditDataFactory \Shopsys\FrameworkBundle\Model\Payment\PaymentEditDataFactory */
        $paymentFacade = $this->getServiceByType(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $vatToDelete = $vatFacade->create(new VatData('name', 10));
        $vatToReplaceWith = $this->getReference(VatDataFixture::VAT_HIGH);
        /* @var $vatToReplaceWith \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat */
        $transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        /* @var $transport \Shopsys\FrameworkBundle\Model\Transport\Transport */
        $transportEditData = $transportEditDataFactory->createFromTransport($transport);
        $payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        /* @var $payment \Shopsys\FrameworkBundle\Model\Payment\Payment */
        $paymentEditData = $paymentEditDataFactory->createFromPayment($payment);

        $transportEditData->transportData->vat = $vatToDelete;
        $transportFacade->edit($transport, $transportEditData);

        $paymentEditData->paymentData->vat = $vatToDelete;
        $paymentFacade->edit($payment, $paymentEditData);

        $vatFacade->deleteById($vatToDelete, $vatToReplaceWith);

        $em->refresh($transport);
        $em->refresh($payment);

        $this->assertEquals($vatToReplaceWith, $transport->getVat());
        $this->assertEquals($vatToReplaceWith, $payment->getVat());
    }
}
