<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;

class PaymentDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    public const PAYMENT_CARD = 'payment_card';
    public const PAYMENT_CASH_ON_DELIVERY = 'payment_cash_on_delivery';
    public const PAYMENT_CASH = 'payment_cash';

    /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade */
    protected $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface
     */
    protected $paymentDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface $paymentDataFactory
     */
    public function __construct(
        PaymentFacade $paymentFacade,
        PaymentDataFactoryInterface $paymentDataFactory
    ) {
        $this->paymentFacade = $paymentFacade;
        $this->paymentDataFactory = $paymentDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $paymentData = $this->paymentDataFactory->create();
        $paymentData->name = [
            'cs' => 'Kreditní kartou',
            'en' => 'Credit card',
        ];
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => Money::create('99.95'),
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => Money::create('2.95'),
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
        $this->createPayment(self::PAYMENT_CARD, $paymentData, [
            TransportDataFixture::TRANSPORT_PERSONAL,
            TransportDataFixture::TRANSPORT_PPL,
        ]);

        $paymentData = $this->paymentDataFactory->create();
        $paymentData->name = [
            'cs' => 'Dobírka',
            'en' => 'Cash on delivery',
        ];
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => Money::create('49.90'),
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => Money::create('1.95'),
        ];
        $paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH_ON_DELIVERY, $paymentData, [TransportDataFixture::TRANSPORT_CZECH_POST]);

        $paymentData = $this->paymentDataFactory->create();
        $paymentData->name = [
            'cs' => 'Hotově',
            'en' => 'Cash',
        ];
        $paymentData->czkRounding = true;
        $paymentData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => Money::zero(),
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => Money::zero(),
        ];
        $paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH, $paymentData, [TransportDataFixture::TRANSPORT_PERSONAL]);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     * @param array $transportsReferenceNames
     */
    protected function createPayment(
        $referenceName,
        PaymentData $paymentData,
        array $transportsReferenceNames
    ) {
        $paymentData->transports = [];
        foreach ($transportsReferenceNames as $transportReferenceName) {
            /** @var \Shopsys\FrameworkBundle\Model\Transport\Transport $transport */
            $transport = $this->getReference($transportReferenceName);
            $paymentData->transports[] = $transport;
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
