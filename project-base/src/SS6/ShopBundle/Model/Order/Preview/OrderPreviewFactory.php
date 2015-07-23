<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderPreviewFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation
	 */
	private $orderPreviewCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
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
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	public function __construct(
		OrderPreviewCalculation $orderPreviewCalculation,
		Domain $domain,
		CurrencyFacade $currencyFacade,
		CurrentCustomer $currentCustomer,
		Cart $cart,
		PromoCodeFacade $promoCodeFacade
	) {
		$this->orderPreviewCalculation = $orderPreviewCalculation;
		$this->domain = $domain;
		$this->currencyFacade = $currencyFacade;
		$this->currentCustomer = $currentCustomer;
		$this->cart = $cart;
		$this->promoCodeFacade = $promoCodeFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
	 */
	public function create(Transport $transport = null, Payment $payment = null) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId());

		return $this->orderPreviewCalculation->calculatePreview(
			$currency,
			$this->domain->getId(),
			$this->cart->getQuantifiedItems(),
			$transport,
			$payment,
			$this->currentCustomer->findCurrentUser(),
			$this->promoCodeFacade->getEnteredPromoCodePercent()
		);
	}

}
