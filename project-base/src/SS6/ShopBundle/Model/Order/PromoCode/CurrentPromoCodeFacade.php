<?php

namespace SS6\ShopBundle\Model\Order\PromoCode;

use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrentPromoCodeFacade {

	const PROMO_CODE_SESSION_KEY = 'promoCode';

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	public function __construct(PromoCodeFacade $promoCodeFacade, SessionInterface $session) {
		$this->promoCodeFacade = $promoCodeFacade;
		$this->session = $session;
	}

	/**
	 * @param string $promoCode
	 * @return bool
	 */
	private function isPromoCodeValid($promoCode) {
		if ($promoCode === null || $this->promoCodeFacade->getPromoCodePercent() === null) {
			return false;
		}

		return $this->promoCodeFacade->getPromoCode() === $promoCode;
	}

	/**
	 * @return string|null
	 */
	public function getValidEnteredPromoCode() {
		$enteredPromoCode = $this->session->get(self::PROMO_CODE_SESSION_KEY);

		if (!$this->isPromoCodeValid($enteredPromoCode)) {
			$enteredPromoCode = null;
		}

		return $enteredPromoCode;
	}

	/**
	 * @param string $enteredPromoCode
	 */
	public function setEnteredPromoCode($enteredPromoCode) {
		if (!$this->isPromoCodeValid($enteredPromoCode)) {
			throw new \SS6\ShopBundle\Model\Order\PromoCode\Exception\InvalidPromoCodeException($enteredPromoCode);
		} else {
			$this->session->set(self::PROMO_CODE_SESSION_KEY, $enteredPromoCode);
		}
	}

	/**
	 * @return float|null
	 */
	public function getValidEnteredPromoCodePercent() {
		if ($this->getValidEnteredPromoCode() === null) {
			return null;
		}

		return $this->promoCodeFacade->getPromoCodePercent();
	}

	public function removeEnteredPromoCode() {
		$this->session->remove(self::PROMO_CODE_SESSION_KEY);
	}
}
