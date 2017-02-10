<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Payment\PaymentDomain;
use Shopsys\ShopBundle\Model\Payment\PaymentEditData;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\ShopBundle\Model\Payment\PaymentRepository;
use Shopsys\ShopBundle\Model\Payment\PaymentVisibilityCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Model\Transport\TransportRepository;

class PaymentFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentVisibilityCalculation
     */
    private $paymentVisibilityCalculation;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation
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
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentEditData $paymentEditData
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public function create(PaymentEditData $paymentEditData) {
        $payment = new Payment($paymentEditData->paymentData);
        $this->em->persist($payment);
        $this->em->flush();
        $this->updatePaymentPrices($payment, $paymentEditData->prices);
        $this->createPaymentDomains($payment, $paymentEditData->paymentData->domains);
        $this->setAddionalDataAndFlush($payment, $paymentEditData->paymentData);

        return $payment;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentEditData $paymentEditData
     */
    public function edit(Payment $payment, PaymentEditData $paymentEditData) {
        $payment->edit($paymentEditData->paymentData);
        $this->updatePaymentPrices($payment, $paymentEditData->prices);
        $this->deletePaymentDomainsByPayment($payment);
        $this->createPaymentDomains($payment, $paymentEditData->paymentData->domains);
        $this->setAddionalDataAndFlush($payment, $paymentEditData->paymentData);
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public function getById($id) {
        return $this->paymentRepository->getById($id);
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public function getByIdWithTransports($id) {
        return $this->paymentRepository->getByIdWithTransports($id);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @return \Shopsys\ShopBundle\Model\Payment\PaymentDomain[]
     */
    public function getPaymentDomainsByPayment(Payment $payment) {
        return $this->paymentRepository->getPaymentDomainsByPayment($payment);
    }

    /**
     * @param int $id
     */
    public function deleteById($id) {
        $payment = $this->getById($id);
        $payment->markAsDeleted();
        $this->deletePaymentDomainsByPayment($payment);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
     */
    private function setAddionalDataAndFlush(Payment $payment, PaymentData $paymentData) {
        $transports = $this->transportRepository->getAllByIds($paymentData->transports);
        $payment->setTransports($transports);
        $this->imageFacade->uploadImage($payment, $paymentData->image, null);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    public function getVisibleOnCurrentDomain() {
        return $this->getVisibleByDomainId($this->domain->getId());
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    public function getVisibleByDomainId($domainId) {
        $allPayments = $this->paymentRepository->getAllWithTransports();

        return $this->paymentVisibilityCalculation->filterVisible($allPayments, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param array $domainIds
     */
    private function createPaymentDomains(Payment $payment, array $domainIds) {
        foreach ($domainIds as $domainId) {
            $paymentDomain = new PaymentDomain($payment, $domainId);
            $this->em->persist($paymentDomain);
        }
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     */
    private function deletePaymentDomainsByPayment(Payment $payment) {
        $paymentDomains = $this->getPaymentDomainsByPayment($payment);
        foreach ($paymentDomains as $paymentDomain) {
            $this->em->remove($paymentDomain);
        }
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param string[currencyId] $prices
     */
    private function updatePaymentPrices(Payment $payment, $prices) {
        foreach ($this->currencyFacade->getAll() as $currency) {
            $price = $prices[$currency->getId()];
            $payment->setPrice($currency, $price);
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    public function getAllIncludingDeleted() {
        return $this->paymentRepository->getAllIncludingDeleted();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @return string [paymentId]
     */
    public function getPaymentPricesWithVatIndexedByPaymentId(Currency $currency) {
        $paymentPricesWithVatByPaymentId = [];
        $payments = $this->getAllIncludingDeleted();
        foreach ($payments as $payment) {
            $paymentPrice = $this->paymentPriceCalculation->calculateIndependentPrice($payment, $currency);
            $paymentPricesWithVatByPaymentId[$payment->getId()] = $paymentPrice->getPriceWithVat();
        }

        return $paymentPricesWithVatByPaymentId;
    }

    /**
     * @return string[paymentId]
     */
    public function getPaymentVatPercentsIndexedByPaymentId() {
        $paymentVatPercentsByPaymentId = [];
        $payments = $this->getAllIncludingDeleted();
        foreach ($payments as $payment) {
            $paymentVatPercentsByPaymentId[$payment->getId()] = $payment->getVat()->getPercent();
        }

        return $paymentVatPercentsByPaymentId;
    }

}
