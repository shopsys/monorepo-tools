<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\Model\Payment\PaymentEditData;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;

class PaymentDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const PAYMENT_CARD = 'payment_card';
    const PAYMENT_CASH_ON_DELIVERY = 'payment_cash_on_delivery';
    const PAYMENT_CASH = 'payment_cash';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $paymentEditData = new PaymentEditData();
        $paymentEditData->paymentData->name = [
            'cs' => 'Kreditní kartou',
            'en' => 'Credit card',
        ];
        $paymentEditData->paymentData->czkRounding = false;
        $paymentEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 99.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 2.95,
        ];
        $paymentEditData->paymentData->description = [
            'cs' => 'Rychle, levně a spolehlivě!',
            'en' => 'Quick, cheap and reliable!',
        ];
        $paymentEditData->paymentData->instructions = [
            'cs' => '<b>Zvolili jste platbu kreditní kartou. Prosím proveďte ji do dvou pracovních dnů.</b>',
            'en' => '<b>You have chosen payment by credit card. Please finish it in two business days.</b>',
        ];
        $paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
        $paymentEditData->paymentData->domains = [Domain::FIRST_DOMAIN_ID];
        $paymentEditData->paymentData->hidden = false;
        $this->createPayment(self::PAYMENT_CARD, $paymentEditData, [
            TransportDataFixture::TRANSPORT_PERSONAL,
            TransportDataFixture::TRANSPORT_PPL,
        ]);

        $paymentEditData->paymentData->name = [
            'cs' => 'Dobírka',
            'en' => 'Personal collection',
        ];
        $paymentEditData->paymentData->czkRounding = false;
        $paymentEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 49.90,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 1.95,
        ];
        $paymentEditData->paymentData->description = [];
        $paymentEditData->paymentData->instructions = [];
        $paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH_ON_DELIVERY, $paymentEditData, [TransportDataFixture::TRANSPORT_CZECH_POST]);

        $paymentEditData->paymentData->name = [
            'cs' => 'Hotově',
            'en' => 'Cash',
        ];
        $paymentEditData->paymentData->czkRounding = true;
        $paymentEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 0,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 0,
        ];
        $paymentEditData->paymentData->description = [];
        $paymentEditData->paymentData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $this->createPayment(self::PAYMENT_CASH, $paymentEditData, [TransportDataFixture::TRANSPORT_PERSONAL]);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentEditData $paymentEditData
     * @param array $transportsReferenceNames
     */
    private function createPayment(
        $referenceName,
        PaymentEditData $paymentEditData,
        array $transportsReferenceNames
    ) {
        $paymentFacade = $this->get('shopsys.shop.payment.payment_facade');
        /* @var $paymentFacade \Shopsys\ShopBundle\Model\Payment\PaymentFacade */

        $paymentEditData->paymentData->transports = [];
        foreach ($transportsReferenceNames as $transportReferenceName) {
            $paymentEditData->paymentData->transports[] = $this->getReference($transportReferenceName);
        }

        $payment = $paymentFacade->create($paymentEditData);
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
