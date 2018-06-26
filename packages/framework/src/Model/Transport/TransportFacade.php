<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Model\Payment\PaymentRepository;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;

class TransportFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportRepository
     */
    protected $transportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportVisibilityCalculation
     */
    protected $transportVisibilityCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    protected $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    protected $transportPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFactoryInterface
     */
    protected $transportFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceFactoryInterface
     */
    protected $transportPriceFactory;

    public function __construct(
        EntityManagerInterface $em,
        TransportRepository $transportRepository,
        PaymentRepository $paymentRepository,
        TransportVisibilityCalculation $transportVisibilityCalculation,
        Domain $domain,
        ImageFacade $imageFacade,
        CurrencyFacade $currencyFacade,
        TransportPriceCalculation $transportPriceCalculation,
        TransportFactoryInterface $transportFactory,
        TransportPriceFactoryInterface $transportPriceFactory
    ) {
        $this->em = $em;
        $this->transportRepository = $transportRepository;
        $this->paymentRepository = $paymentRepository;
        $this->transportVisibilityCalculation = $transportVisibilityCalculation;
        $this->domain = $domain;
        $this->imageFacade = $imageFacade;
        $this->currencyFacade = $currencyFacade;
        $this->transportPriceCalculation = $transportPriceCalculation;
        $this->transportFactory = $transportFactory;
        $this->transportPriceFactory = $transportPriceFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function create(TransportData $transportData)
    {
        $transport = $this->transportFactory->create($transportData);
        $this->em->persist($transport);
        $this->em->flush();
        $this->updateTransportPrices($transport, $transportData->pricesByCurrencyId);
        $this->imageFacade->uploadImage($transport, $transportData->image->uploadedFiles, null);
        $transport->setPayments($transportData->payments);
        $this->em->flush();

        return $transport;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportData $transportData
     */
    public function edit(Transport $transport, TransportData $transportData)
    {
        $transport->edit($transportData);
        $this->updateTransportPrices($transport, $transportData->pricesByCurrencyId);
        $this->imageFacade->uploadImage($transport, $transportData->image->uploadedFiles, null);
        $transport->setPayments($transportData->payments);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getById($id)
    {
        return $this->transportRepository->getById($id);
    }

    /**
     * @param int $id
     */
    public function deleteById($id)
    {
        $transport = $this->getById($id);
        $transport->markAsDeleted();
        $paymentsByTransport = $this->paymentRepository->getAllByTransport($transport);
        foreach ($paymentsByTransport as $payment) {
            $payment->getTransports()->removeElement($transport);
        }
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $visiblePayments
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public function getVisibleOnCurrentDomain(array $visiblePayments)
    {
        return $this->getVisibleByDomainId($this->domain->getId(), $visiblePayments);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $visiblePaymentsOnDomain
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public function getVisibleByDomainId($domainId, $visiblePaymentsOnDomain)
    {
        $transports = $this->transportRepository->getAllByDomainId($domainId);

        return $this->transportVisibilityCalculation->filterVisible($transports, $visiblePaymentsOnDomain, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param string[] $pricesByCurrencyId
     */
    protected function updateTransportPrices(Transport $transport, $pricesByCurrencyId)
    {
        foreach ($this->currencyFacade->getAll() as $currency) {
            $price = $pricesByCurrencyId[$currency->getId()];
            $transport->setPrice($this->transportPriceFactory, $currency, $price);
        }
    }

    public function getAll()
    {
        return $this->transportRepository->getAll();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public function getAllIncludingDeleted()
    {
        return $this->transportRepository->getAllIncludingDeleted();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return string[]
     */
    public function getTransportPricesWithVatIndexedByTransportId(Currency $currency)
    {
        $transportPricesWithVatByTransportId = [];
        $transports = $this->getAllIncludingDeleted();
        foreach ($transports as $transport) {
            $transportPrice = $this->transportPriceCalculation->calculateIndependentPrice($transport, $currency);
            $transportPricesWithVatByTransportId[$transport->getId()] = $transportPrice->getPriceWithVat();
        }

        return $transportPricesWithVatByTransportId;
    }

    /**
     * @return string[]
     */
    public function getTransportVatPercentsIndexedByTransportId()
    {
        $transportVatPercentsByTransportId = [];
        $transports = $this->getAllIncludingDeleted();
        foreach ($transports as $transport) {
            $transportVatPercentsByTransportId[$transport->getId()] = $transport->getVat()->getPercent();
        }

        return $transportVatPercentsByTransportId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getIndependentBasePricesIndexedByCurrencyId(Transport $transport)
    {
        $prices = [];
        foreach ($transport->getPrices() as $transportInputPrice) {
            $currency = $transportInputPrice->getCurrency();
            $prices[$currency->getId()] = $this->transportPriceCalculation->calculateIndependentPrice($transport, $currency);
        }

        return $prices;
    }
}
