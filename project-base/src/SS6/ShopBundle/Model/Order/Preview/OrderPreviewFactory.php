<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderPreviewFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation
	 */
	private $orderPreviewCalculation;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
	 */
	private $currentPromoCodeFacade;

	public function __construct(
		OrderPreviewCalculation $orderPreviewCalculation,
		Domain $domain,
		CurrencyFacade $currencyFacade,
		CurrentCustomer $currentCustomer,
		Cart $cart,
		CurrentPromoCodeFacade $currentPromoCodeFacade
	) {
		$this->orderPreviewCalculation = $orderPreviewCalculation;
		$this->domain = $domain;
		$this->currencyFacade = $currencyFacade;
		$this->currentCustomer = $currentCustomer;
		$this->cart = $cart;
		$this->currentPromoCodeFacade = $currentPromoCodeFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
	 */
	public function createForCurrentUser(Transport $transport = null, Payment $payment = null) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId());
		$validEnteredPromoCode = $this->currentPromoCodeFacade->getValidEnteredPromoCodeOrNull();
		$validEnteredPromoCodePercent = null;
		if ($validEnteredPromoCode !== null) {
			$validEnteredPromoCodePercent = $validEnteredPromoCode->getPercent();
		}

		return $this->create(
			$currency,
			$this->domain->getId(),
			$this->cart->getQuantifiedProducts(),
			$transport,
			$payment,
			$this->currentCustomer->findCurrentUser(),
			$validEnteredPromoCodePercent
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @param float|null $promoCodeDiscountPercent
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
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
