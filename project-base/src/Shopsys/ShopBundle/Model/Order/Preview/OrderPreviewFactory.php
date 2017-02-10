<?php

namespace Shopsys\ShopBundle\Model\Order\Preview;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Model\Transport\Transport;

class OrderPreviewFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewCalculation
     */
    private $orderPreviewCalculation;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    private $currentPromoCodeFacade;

    public function __construct(
        OrderPreviewCalculation $orderPreviewCalculation,
        Domain $domain,
        CurrencyFacade $currencyFacade,
        CurrentCustomer $currentCustomer,
        CartFacade $cartFacade,
        CurrentPromoCodeFacade $currentPromoCodeFacade
    ) {
        $this->orderPreviewCalculation = $orderPreviewCalculation;
        $this->domain = $domain;
        $this->currencyFacade = $currencyFacade;
        $this->currentCustomer = $currentCustomer;
        $this->cartFacade = $cartFacade;
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\ShopBundle\Model\Payment\Payment|null $payment
     * @return \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview
     */
    public function createForCurrentUser(Transport $transport = null, Payment $payment = null)
    {
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId());
        $validEnteredPromoCode = $this->currentPromoCodeFacade->getValidEnteredPromoCodeOrNull();
        $validEnteredPromoCodePercent = null;
        if ($validEnteredPromoCode !== null) {
            $validEnteredPromoCodePercent = $validEnteredPromoCode->getPercent();
        }

        return $this->create(
            $currency,
            $this->domain->getId(),
            $this->cartFacade->getQuantifiedProductsOfCurrentCustomer(),
            $transport,
            $payment,
            $this->currentCustomer->findCurrentUser(),
            $validEnteredPromoCodePercent
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
     * @param \Shopsys\ShopBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\ShopBundle\Model\Payment\Payment|null $payment
     * @param \Shopsys\ShopBundle\Model\Customer\User|null $user
     * @param float|null $promoCodeDiscountPercent
     * @return \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview
     */
    public function create(
        Currency $currency,
        $domainId,
        array $quantifiedProducts,
        Transport $transport = null,
        Payment $payment = null,
        User $user = null,
        $promoCodeDiscountPercent = null
    ) {
        return $this->orderPreviewCalculation->calculatePreview(
            $currency,
            $domainId,
            $quantifiedProducts,
            $transport,
            $payment,
            $user,
            $promoCodeDiscountPercent
        );
    }
}
