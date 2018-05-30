<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;

class PaymentDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const PAYMENT_CARD = 'payment_card';
    const PAYMENT_CASH_ON_DELIVERY = 'payment_cash_on_delivery';
    const PAYMENT_CASH = 'payment_cash';

    /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */
    private $paymentFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade
     */
    public function __construct(PaymentFacade $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $paymentData = new PaymentData();
        $paymentData->name = [
            'cs' => 'Kreditní kartou',
            'en' => 'Credit card',
        ];
        $paymentData->czkRounding = false;
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 99.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 2.95,
        ];
        $paymentData->description = [
            'cs' => 'Rychle, levně a spolehlivě!',
            'en' => 'Quick, cheap and reliable!',
        ];
        $paymentData->instructions = [
            'cs' => '<b>Zvolili jste platbu kreditní kartou. Prosím proveďte ji do dvou pracovních dnů.</b>',
            'en' => '<b>You have chosen payment by credit card. Please finish it in two business days.</b>',
        ];
        $paymentData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
        $paymentData->domains = [Domain::FIRST_DOMAIN_ID];
        $paymentData->hidden = false;
        $this->createPayment(self::PAYMENT_CARD, $paymentData, [
            TransportDataFixture::TRANSPORT_PERSONAL,
            TransportDataFixture::TRANSPORT_PPL,
        ]);

        $paymentData->name = [
            'cs' => 'Dobírka',
            'en' => 'Cash on delivery',
        ];
        $paymentData->czkRounding = false;
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 49.90,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 1.95,
        ];
        $paymentData->description = [];
        $paymentData->instructions = [];
        $paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH_ON_DELIVERY, $paymentData, [TransportDataFixture::TRANSPORT_CZECH_POST]);

        $paymentData->name = [
            'cs' => 'Hotově',
            'en' => 'Cash',
        ];
        $paymentData->czkRounding = true;
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 0,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 0,
        ];
        $paymentData->description = [];
        $paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH, $paymentData, [TransportDataFixture::TRANSPORT_PERSONAL]);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     * @param array $transportsReferenceNames
     */
    private function createPayment(
        $referenceName,
        PaymentData $paymentData,
        array $transportsReferenceNames
    ) {
        $paymentData->transports = [];
        foreach ($transportsReferenceNames as $transportReferenceName) {
            $paymentData->transports[] = $this->getReference($transportReferenceName);
        }

        $payment = $this->paymentFacade->create($paymentData);
        $this->addReference($referenceName, $payment);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            TransportDataFixture::class,
            VatDataFixture::class,
            CurrencyDataFixture::class,
        ];
    }
}
