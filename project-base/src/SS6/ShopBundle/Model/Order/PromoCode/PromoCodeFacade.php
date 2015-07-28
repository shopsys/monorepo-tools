<?php

namespace SS6\ShopBundle\Model\Order\PromoCode;

use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PromoCodeFacade {

	const PROMO_CODE_SESSION_KEY = 'promoCode';
	const PROMO_CODE_SETTING_KEY = 'promoCode';
	const PROMO_CODE_PERCENT_SETTING_KEY = 'promoCodePercent';

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	public function __construct(SessionInterface $session, Setting $setting) {
		$this->session = $session;
		$this->setting = $setting;
	}

	/**
	 * @return string|null
	 */
	public function getPromoCode() {
		return $this->setting->get(self::PROMO_CODE_SETTING_KEY, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param string|null $code
	 * @param float|null $percent
	 */
	public function editPromoCode($code, $percent) {
		$this->setting->set(self::PROMO_CODE_SETTING_KEY, $code, SettingValue::DOMAIN_ID_COMMON);
		$this->setting->set(self::PROMO_CODE_PERCENT_SETTING_KEY, $percent, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param string $promoCode
	 * @return true
	 */
	private function isPromoCodeValid($promoCode) {
		if ($promoCode === null || $this->getPromoCodePercent() === null) {
			return false;
		}

		return $this->getPromoCode() === $promoCode;
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
	 * @return float|null
	 */
	public function getPromoCodePercent() {
		return $this->setting->get(self::PROMO_CODE_PERCENT_SETTING_KEY, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @return float|null
	 */
	public function getEnteredPromoCodePercent() {
		if ($this->getEnteredPromoCode() === null) {
			return null;
		}

		return $this->getPromoCodePercent();
	}

	public function removeEnteredPromoCode() {
		$this->session->remove(self::PROMO_CODE_SESSION_KEY);
	}
}
