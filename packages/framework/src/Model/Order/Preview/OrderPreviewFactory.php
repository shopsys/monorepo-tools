<?php

namespace Shopsys\FrameworkBundle\Model\Order\Preview;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

class OrderPreviewFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewCalculation
     */
    private $orderPreviewCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
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
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment|null $payment
     * @return \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview
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
            $this->cartFacade->getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId(),
            $transport,
            $payment,
            $this->currentCustomer->findCurrentUser(),
            $validEnteredPromoCodePercent
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment|null $payment
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @param float|null $promoCodeDiscountPercent
     * @return \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview
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
