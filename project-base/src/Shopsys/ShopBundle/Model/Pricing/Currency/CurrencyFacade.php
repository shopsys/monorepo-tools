<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Order\OrderRepository;
use Shopsys\ShopBundle\Model\Payment\PaymentPrice;
use Shopsys\ShopBundle\Model\Payment\PaymentRepository;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Transport\TransportPrice;
use Shopsys\ShopBundle\Model\Transport\TransportRepository;

class CurrencyFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyService
     */
    private $currencyService;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    public function __construct(
        EntityManager $em,
        CurrencyRepository $currencyRepository,
        CurrencyService $currencyService,
        PricingSetting $pricingSetting,
        OrderRepository $orderRepository,
        Domain $domain,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        PaymentRepository $paymentRepository,
        TransportRepository $transportRepository
    ) {
        $this->em = $em;
        $this->currencyRepository = $currencyRepository;
        $this->currencyService = $currencyService;
        $this->pricingSetting = $pricingSetting;
        $this->orderRepository = $orderRepository;
        $this->domain = $domain;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->paymentRepository = $paymentRepository;
        $this->transportRepository = $transportRepository;
    }

    /**
     * @param int $currencyId
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function getById($currencyId)
    {
        return $this->currencyRepository->getById($currencyId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function create(CurrencyData $currencyData)
    {
        $currency = $this->currencyService->create($currencyData);
        $this->em->persist($currency);
        $this->em->flush($currency);
        $this->createTransportAndPaymentPrices($currency);

        return $currency;
    }

    /**
     * @param int $currencyId
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function edit($currencyId, CurrencyData $currencyData)
    {
        $currency = $this->currencyRepository->getById($currencyId);
        $this->currencyService->edit($currency, $currencyData, $this->isDefaultCurrency($currency));
        $this->em->flush();
        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

        return $currency;
    }

    /**
     * @param int $currencyId
     */
    public function deleteById($currencyId)
    {
        $currency = $this->currencyRepository->getById($currencyId);

        if (in_array($currency->getId(), $this->getNotAllowedToDeleteCurrencyIds(), true)) {
            throw new \Shopsys\ShopBundle\Model\Pricing\Currency\Exception\DeletingNotAllowedToDeleteCurrencyException();
        }
        $this->em->remove($currency);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    public function getAll()
    {
        return $this->currencyRepository->getAll();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function getDefaultCurrency()
    {
        return $this->getById($this->pricingSetting->getDefaultCurrencyId());
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function getDomainDefaultCurrencyByDomainId($domainId)
    {
        return $this->getById($this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainId));
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     */
    public function setDefaultCurrency(Currency $currency)
    {
        $this->pricingSetting->setDefaultCurrency($currency);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param int $domainId
     */
    public function setDomainDefaultCurrency(Currency $currency, $domainId)
    {
        $this->pricingSetting->setDomainDefaultCurrency($currency, $domainId);
        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();
    }

    /**
     * @return int[]
     */
    public function getNotAllowedToDeleteCurrencyIds()
    {
        return $this->currencyService->getNotAllowedToDeleteCurrencyIds(
            $this->getDefaultCurrency()->getId(),
            $this->getCurrenciesUsedInOrders(),
            $this->pricingSetting,
            $this->domain
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @return bool
     */
    public function isDefaultCurrency(Currency $currency)
    {
        return $currency === $this->getDefaultCurrency();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    public function getCurrenciesUsedInOrders()
    {
        return $this->orderRepository->getCurrenciesUsedInOrders();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    public function getAllIndexedById()
    {
        foreach ($this->getAll() as $currency) {
            $currenciesIndexedById[$currency->getId()] = $currency;
        }

        return $currenciesIndexedById;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     */
    private function createTransportAndPaymentPrices(Currency $currency)
    {
        $toFlush = [];
        foreach ($this->paymentRepository->getAll() as $payment) {
            $paymentPrice = new PaymentPrice($payment, $currency, 0);
            $this->em->persist($paymentPrice);
            $toFlush[] = $paymentPrice;
        }
        foreach ($this->transportRepository->getAll() as $transport) {
            $transportPrice = new TransportPrice($transport, $currency, 0);
            $this->em->persist($transportPrice);
            $toFlush[] = $transportPrice;
        }

        $this->em->flush($toFlush);
    }
}
