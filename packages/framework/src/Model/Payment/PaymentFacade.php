<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportRepository;

class PaymentFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentVisibilityCalculation
     */
    private $paymentVisibilityCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    public function __construct(
        EntityManager $em,
        PaymentRepository $paymentRepository,
        TransportRepository $transportRepository,
        PaymentVisibilityCalculation $paymentVisibilityCalculation,
        Domain $domain,
        ImageFacade $imageFacade,
        CurrencyFacade $currencyFacade,
        PaymentPriceCalculation $paymentPriceCalculation
    ) {
        $this->em = $em;
        $this->paymentRepository = $paymentRepository;
        $this->transportRepository = $transportRepository;
        $this->paymentVisibilityCalculation = $paymentVisibilityCalculation;
        $this->domain = $domain;
        $this->imageFacade = $imageFacade;
        $this->currencyFacade = $currencyFacade;
        $this->paymentPriceCalculation = $paymentPriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentEditData $paymentEditData
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function create(PaymentEditData $paymentEditData)
    {
        $payment = new Payment($paymentEditData->paymentData);
        $this->em->persist($payment);
        $this->em->flush();
        $this->updatePaymentPrices($payment, $paymentEditData->pricesByCurrencyId);
        $this->createPaymentDomains($payment, $paymentEditData->paymentData->domains);
        $this->setAdditionalDataAndFlush($payment, $paymentEditData->paymentData);

        return $payment;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentEditData $paymentEditData
     */
    public function edit(Payment $payment, PaymentEditData $paymentEditData)
    {
        $payment->edit($paymentEditData->paymentData);
        $this->updatePaymentPrices($payment, $paymentEditData->pricesByCurrencyId);
        $this->deletePaymentDomainsByPayment($payment);
        $this->createPaymentDomains($payment, $paymentEditData->paymentData->domains);
        $this->setAdditionalDataAndFlush($payment, $paymentEditData->paymentData);
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getById($id)
    {
        return $this->paymentRepository->getById($id);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentDomain[]
     */
    public function getPaymentDomainsByPayment(Payment $payment)
    {
        return $this->paymentRepository->getPaymentDomainsByPayment($payment);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $payment = $this->getById($id);
        $payment->markAsDeleted();
        $this->deletePaymentDomainsByPayment($payment);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    private function setAdditionalDataAndFlush(Payment $payment, PaymentData $paymentData)
    {
        $transports = $this->transportRepository->getAllByIds($paymentData->transports);
        $payment->setTransports($transports);
        $this->imageFacade->uploadImage($payment, $paymentData->image, null);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getVisibleOnCurrentDomain()
    {
        return $this->getVisibleByDomainId($this->domain->getId());
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getVisibleByDomainId($domainId)
    {
        $allPayments = $this->paymentRepository->getAll();

        return $this->paymentVisibilityCalculation->filterVisible($allPayments, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param array $domainIds
     */
    private function createPaymentDomains(Payment $payment, array $domainIds)
    {
        foreach ($domainIds as $domainId) {
            $paymentDomain = new PaymentDomain($payment, $domainId);
            $this->em->persist($paymentDomain);
        }
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    private function deletePaymentDomainsByPayment(Payment $payment)
    {
        $paymentDomains = $this->getPaymentDomainsByPayment($payment);
        foreach ($paymentDomains as $paymentDomain) {
            $this->em->remove($paymentDomain);
        }
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param string[] $pricesByCurrencyId
     */
    private function updatePaymentPrices(Payment $payment, $pricesByCurrencyId)
    {
        foreach ($this->currencyFacade->getAll() as $currency) {
            $price = $pricesByCurrencyId[$currency->getId()];
            $payment->setPrice($currency, $price);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public function getAllIncludingDeleted()
    {
        return $this->paymentRepository->getAllIncludingDeleted();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return string[]
     */
    public function getPaymentPricesWithVatIndexedByPaymentId(Currency $currency)
    {
        $paymentPricesWithVatByPaymentId = [];
        $payments = $this->getAllIncludingDeleted();
        foreach ($payments as $payment) {
            $paymentPrice = $this->paymentPriceCalculation->calculateIndependentPrice($payment, $currency);
            $paymentPricesWithVatByPaymentId[$payment->getId()] = $paymentPrice->getPriceWithVat();
        }

        return $paymentPricesWithVatByPaymentId;
    }

    /**
     * @return string[]
     */
    public function getPaymentVatPercentsIndexedByPaymentId()
    {
        $paymentVatPercentsByPaymentId = [];
        $payments = $this->getAllIncludingDeleted();
        foreach ($payments as $payment) {
            $paymentVatPercentsByPaymentId[$payment->getId()] = $payment->getVat()->getPercent();
        }

        return $paymentVatPercentsByPaymentId;
    }
}
