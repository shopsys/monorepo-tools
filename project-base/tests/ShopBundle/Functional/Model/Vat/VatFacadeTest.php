<?php

namespace Tests\ShopBundle\Functional\Model\Vat;

use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\PaymentDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\VatDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class VatFacadeTest extends TransactionFunctionalTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade $vatFacade */
        $vatFacade = $this->getContainer()->get(VatFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade $transportFacade */
        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Transport\TransportDataFactory $transportDataFactory */
        $transportDataFactory = $this->getContainer()->get(TransportDataFactoryInterface::class);
        /** @var \Shopsys\ShopBundle\Model\Payment\PaymentDataFactory $paymentDataFactory */
        $paymentDataFactory = $this->getContainer()->get(PaymentDataFactoryInterface::class);
        /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);

        $vatData = new VatData();
        $vatData->name = 'name';
        $vatData->percent = '10';
        $vatToDelete = $vatFacade->create($vatData);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vatToReplaceWith */
        $vatToReplaceWith = $this->getReference(VatDataFixture::VAT_HIGH);
        /** @var \Shopsys\ShopBundle\Model\Transport\Transport $transport */
        $transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $transportData = $transportDataFactory->createFromTransport($transport);
        /** @var \Shopsys\ShopBundle\Model\Payment\Payment $payment */
        $payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
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
