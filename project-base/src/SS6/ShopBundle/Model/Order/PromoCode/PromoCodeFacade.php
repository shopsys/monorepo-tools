<?php

namespace SS6\ShopBundle\Model\Order\PromoCode;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PromoCodeFacade {

	const PROMO_CODE_SESSION_KEY = 'promoCode';

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}

	/**
	 * @param string $promoCode
	 * @return true
	 */
	public function isPromoCodeValid($promoCode) {
		if ($promoCode === null) {
			return false;
		}

		return 'secretCode' === $promoCode;
	}

	/**
	 * @return string|null
	 */
	public function getEnteredPromoCode() {
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
	 * @return int
	 */
	public function getPromoCodePercent() {
		return 10;
	}

	public function removeEnteredPromoCode() {
		$this->session->remove(self::PROMO_CODE_SESSION_KEY);
	}
}
