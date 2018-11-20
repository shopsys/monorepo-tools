<?php

namespace Tests\ShopBundle\Functional\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CurrencyDataFixture;
use Shopsys\FrameworkBundle\Model\Payment\PaymentDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceRecalculator;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class InputPriceRecalculationSchedulerTest extends TransactionFunctionalTestCase
{
    public function testOnKernelResponseNoAction()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);

        $inputPriceRecalculatorMock = $this->getMockBuilder(InputPriceRecalculator::class)
            ->setMethods(['__construct', 'recalculateToInputPricesWithoutVat', 'recalculateToInputPricesWithVat'])
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceRecalculatorMock->expects($this->never())->method('recalculateToInputPricesWithoutVat');
        $inputPriceRecalculatorMock->expects($this->never())->method('recalculateToInputPricesWithVat');

        $filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest'])
            ->getMock();
        $filterResponseEventMock->expects($this->any())->method('isMasterRequest')
            ->willReturn(true);

        $inputPriceRecalculationScheduler = new InputPriceRecalculationScheduler($inputPriceRecalculatorMock, $setting);

        $inputPriceRecalculationScheduler->onKernelResponse($filterResponseEventMock);
    }

    public function inputPricesTestDataProvider()
    {
        return [
            ['inputPriceWithoutVat' => '100', 'inputPriceWithVat' => '121', 'vatPercent' => '21'],
            ['inputPriceWithoutVat' => '17261.983471', 'inputPriceWithVat' => '20887', 'vatPercent' => '21'],
        ];
    }

    /**
     * @dataProvider inputPricesTestDataProvider
     * @param mixed $inputPriceWithoutVat
     * @param mixed $inputPriceWithVat
     * @param mixed $vatPercent
     */
    public function testOnKernelResponseRecalculateInputPricesWithoutVat(
        $inputPriceWithoutVat,
        $inputPriceWithVat,
        $vatPercent
    ) {
        $em = $this->getEntityManager();

        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceRecalculationScheduler $inputPriceRecalculationScheduler */
        $inputPriceRecalculationScheduler = $this->getContainer()->get(InputPriceRecalculationScheduler::class);
        /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade $transportFacade */
        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Payment\PaymentDataFactory $paymentDataFactory */
        $paymentDataFactory = $this->getContainer()->get(PaymentDataFactoryInterface::class);
        /** @var \Shopsys\ShopBundle\Model\Transport\TransportDataFactory $transportDataFactory */
        $transportDataFactory = $this->getContainer()->get(TransportDataFactoryInterface::class);

        $setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $availabilityData = new AvailabilityData();
        $availabilityData->dispatchTime = 0;
        $availability = new Availability($availabilityData);
        $em->persist($vat);
        $em->persist($availability);

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency1 */
        $currency1 = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency2 */
        $currency2 = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);

        $paymentData = $paymentDataFactory->create();
        $paymentData->name = ['cs' => 'name'];
        $paymentData->pricesByCurrencyId = [$currency1->getId() => $inputPriceWithVat, $currency2->getId() => $inputPriceWithVat];
        $paymentData->vat = $vat;
        /** @var \Shopsys\ShopBundle\Model\Payment\Payment $payment */
        $payment = $paymentFacade->create($paymentData);

        $transportData = $transportDataFactory->create();
        $transportData->name = ['cs' => 'name'];
        $transportData->description = ['cs' => 'desc'];
        $transportData->pricesByCurrencyId = [$currency1->getId() => $inputPriceWithVat, $currency2->getId() => $inputPriceWithVat];
        $transportData->vat = $vat;
        /** @var \Shopsys\ShopBundle\Model\Transport\Transport $transport */
        $transport = $transportFacade->create($transportData);
        $em->flush();

        $filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest'])
            ->getMock();
        $filterResponseEventMock->expects($this->any())->method('isMasterRequest')
            ->willReturn(true);

        $inputPriceRecalculationScheduler->scheduleSetInputPricesWithoutVat();
        $inputPriceRecalculationScheduler->onKernelResponse($filterResponseEventMock);

        $em->refresh($payment);
        $em->refresh($transport);

        $this->assertSame(round($inputPriceWithoutVat, 6), round($payment->getPrice($currency1)->getPrice(), 6));
        $this->assertSame(round($inputPriceWithoutVat, 6), round($transport->getPrice($currency1)->getPrice(), 6));
    }

    /**
     * @dataProvider inputPricesTestDataProvider
     * @param mixed $inputPriceWithoutVat
     * @param mixed $inputPriceWithVat
     * @param mixed $vatPercent
     */
    public function testOnKernelResponseRecalculateInputPricesWithVat(
        $inputPriceWithoutVat,
        $inputPriceWithVat,
        $vatPercent
    ) {
        $em = $this->getEntityManager();

        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceRecalculationScheduler $inputPriceRecalculationScheduler */
        $inputPriceRecalculationScheduler = $this->getContainer()->get(InputPriceRecalculationScheduler::class);
        /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade */
        $paymentFacade = $this->getContainer()->get(PaymentFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade $transportFacade */
        $transportFacade = $this->getContainer()->get(TransportFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Payment\PaymentDataFactory $paymentDataFactory */
        $paymentDataFactory = $this->getContainer()->get(PaymentDataFactoryInterface::class);
        /** @var \Shopsys\ShopBundle\Model\Transport\TransportDataFactory $transportDataFactory */
        $transportDataFactory = $this->getContainer()->get(TransportDataFactoryInterface::class);

        $setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency1 */
        $currency1 = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency2 */
        $currency2 = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $availabilityData = new AvailabilityData();
        $availabilityData->dispatchTime = 0;
        $availability = new Availability($availabilityData);
        $em->persist($vat);
        $em->persist($availability);

        $paymentData = $paymentDataFactory->create();
        $paymentData->name = ['cs' => 'name'];
        $paymentData->pricesByCurrencyId = [$currency1->getId() => $inputPriceWithoutVat, $currency2->getId() => $inputPriceWithoutVat];
        $paymentData->vat = $vat;
        /** @var \Shopsys\ShopBundle\Model\Payment\Payment $payment */
        $payment = $paymentFacade->create($paymentData);

        $transportData = $transportDataFactory->create();
        $transportData->name = ['cs' => 'name'];
        $transportData->pricesByCurrencyId = [$currency1->getId() => $inputPriceWithoutVat, $currency2->getId() => $inputPriceWithoutVat];
        $transportData->vat = $vat;
        /** @var \Shopsys\ShopBundle\Model\Transport\Transport $transport */
        $transport = $transportFacade->create($transportData);

        $em->flush();

        $filterResponseEventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest'])
            ->getMock();
        $filterResponseEventMock->expects($this->any())->method('isMasterRequest')
            ->willReturn(true);

        $inputPriceRecalculationScheduler->scheduleSetInputPricesWithVat();
        $inputPriceRecalculationScheduler->onKernelResponse($filterResponseEventMock);

        $em->refresh($payment);
        $em->refresh($transport);

        $this->assertSame(round($inputPriceWithVat, 6), round($payment->getPrice($currency1)->getPrice(), 6));
        $this->assertSame(round($inputPriceWithVat, 6), round($transport->getPrice($currency1)->getPrice(), 6));
    }
}
