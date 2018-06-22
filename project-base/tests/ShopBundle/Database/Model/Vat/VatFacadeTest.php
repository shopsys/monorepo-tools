<?php

namespace Tests\ShopBundle\Database\Model\Vat;

use Shopsys\FrameworkBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PaymentDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\TransportDataFixture;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class VatFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $vatFacade = $this->getContainer()->get(VatFacade::class);
        /* @var $vatFacade \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade */
        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */
        $transportDataFactory = $this->getContainer()->get(TransportDataFactory::class);
        /* @var $transportDataFactory \Shopsys\FrameworkBundle\Model\Transport\TransportDataFactory */
        $paymentDataFactory = $this->getContainer()->get(PaymentDataFactory::class);
        /* @var $paymentDataFactory \Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactory */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /* @var $paymentFacade \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */

        $vatData = new VatData();
        $vatData->name = 'name';
        $vatData->percent = 10;
        $vatToDelete = $vatFacade->create($vatData);
        $vatToReplaceWith = $this->getReference(VatDataFixture::VAT_HIGH);
        /* @var $vatToReplaceWith \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat */
        $transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        /* @var $transport \Shopsys\FrameworkBundle\Model\Transport\Transport */
        $transportData = $transportDataFactory->createFromTransport($transport);
        $payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        /* @var $payment \Shopsys\FrameworkBundle\Model\Payment\Payment */
        $paymentData = $paymentDataFactory->createFromPayment($payment);

        $transportData->vat = $vatToDelete;
        $transportFacade->edit($transport, $transportData);

        $paymentData->vat = $vatToDelete;
        $paymentFacade->edit($payment, $paymentData);

        $vatFacade->deleteById($vatToDelete, $vatToReplaceWith);

        $em->refresh($transport);
        $em->refresh($payment);

        $this->assertEquals($vatToReplaceWith, $transport->getVat());
        $this->assertEquals($vatToReplaceWith, $payment->getVat());
    }
}
