<?php

namespace SS6\ShopBundle\Twig;

use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use CommerceGuys\Intl\Formatter\NumberFormatter;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class PriceExtension extends Twig_Extension {

	const MINIMUM_FRACTION_DIGITS = 2;
	const MAXIMUM_FRACTION_DIGITS = 10;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @var \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface
	 */
	private $numberFormatRepository;

	/**
	 * @var \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface
	 */
	private $intlCurrencyRepository;

	public function __construct(
		Translator $translator,
		CurrencyFacade $currencyFacade,
		Domain $domain,
		Localization $localization,
		NumberFormatRepositoryInterface $numberFormatRepository,
		CurrencyRepositoryInterface $intlCurrencyRepository
	) {
		$this->translator = $translator;
		$this->currencyFacade = $currencyFacade;
		$this->domain = $domain;
		$this->localization = $localization;
		$this->numberFormatRepository = $numberFormatRepository;
		$this->intlCurrencyRepository = $intlCurrencyRepository;
	}

	/**
	 * @return array
	 */
	public function getFilters() {
		return [
			new Twig_SimpleFilter(
				'price',
				[$this, 'priceFilter']
			),
			new Twig_SimpleFilter(
				'priceText',
				[$this, 'priceTextFilter'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFilter(
				'priceTextWithCurrencyByCurrencyIdAndLocale',
				[$this, 'priceTextWithCurrencyByCurrencyIdAndLocaleFilter'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFilter(
				'priceWithCurrency',
				[$this, 'priceWithCurrencyFilter'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFilter(
				'priceWithCurrencyAdmin',
				[$this, 'priceWithCurrencyAdminFilter'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFilter(
				'priceWithCurrencyByDomainId',
				[$this, 'priceWithCurrencyByDomainIdFilter'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFilter(
				'priceWithCurrencyByCurrencyId',
				[$this, 'priceWithCurrencyByCurrencyIdFilter'],
				['is_safe' => ['html']]
			),
		];
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction(
				'currencySymbolByDomainId',
				[$this, 'getCurrencySymbolByDomainId'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFunction(
				'currencySymbolDefault',
				[$this, 'getDefaultCurrencySymbol'],
				['is_safe' => ['html']]
			),
			new Twig_SimpleFunction(
				'currencySymbolByCurrencyId',
				[$this, 'getCurrencySymbolByCurrencyId'],
				['is_safe' => ['html']]
			),
		];
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceFilter($price) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId());

		return $this->formatCurrency($price, $currency);
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceTextFilter($price) {
		if ($price == 0) {
			return $this->translator->trans('Zdarma');
		} else {
			return $this->priceFilter($price);
		}
	}

	/**
	 * @param string $price
	 * @param int $currencyId
	 * @param string $locale
	 * @return string
	 */
	public function priceTextWithCurrencyByCurrencyIdAndLocaleFilter($price, $currencyId, $locale) {
		if ($price == 0) {
			return $this->translator->trans('Zdarma');
		} else {
			$currency = $this->currencyFacade->getById($currencyId);
			return $this->formatCurrency($price, $currency, $locale);
		}
	}

	/**
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return string
	 */
	public function priceWithCurrencyFilter($price, Currency $currency) {
		return $this->formatCurrency($price, $currency);
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceWithCurrencyAdminFilter($price) {
		$currency = $this->currencyFacade->getDefaultCurrency();

		return $this->formatCurrency($price, $currency);
	}

	/**
	 * @param string $price
	 * @param int $domainId
	 * @return string
	 */
	public function priceWithCurrencyByDomainIdFilter($price, $domainId) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

		return $this->formatCurrency($price, $currency);
	}

	public function priceWithCurrencyByCurrencyIdFilter($price, $currencyId) {
		$currency = $this->currencyFacade->getById($currencyId);

		return $this->formatCurrency($price, $currency);
	}

	/**
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param string|null $locale
	 * @return string
	 */
	private function formatCurrency($price, Currency $currency, $locale = null) {
		if (!is_numeric($price)) {
			return $price;
		}
		if ($locale === null) {
			$locale = $this->localization->getLocale();
		}

		$numberFormatter = $this->getNumberFormatter($locale);
		$intlCurrency = $this->intlCurrencyRepository->get(
			$currency->getCode(),
			$locale
		);

		return $numberFormatter->formatCurrency($price, $intlCurrency);
	}

	/**
	 * @return \CommerceGuys\Intl\Formatter\NumberFormatter
	 * @param string $locale
	 */
	private function getNumberFormatter($locale) {
		$numberFormat = $this->numberFormatRepository->get($locale);
		$numberFormatter = new NumberFormatter($numberFormat, NumberFormatter::CURRENCY);
		$numberFormatter->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS);
		$numberFormatter->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

		return $numberFormatter;
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getCurrencySymbolByDomainId($domainId) {
		$locale = $this->localization->getLocale();

		return $this->getCurrencySymbolByDomainIdAndLocale($domainId, $locale);
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @return string
	 */
	private function getCurrencySymbolByDomainIdAndLocale($domainId, $locale) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
		$intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

		return $intlCurrency->getSymbol();
	}

	/**
	 * @return string
	 */
	public function getDefaultCurrencySymbol() {
		$locale = $this->localization->getLocale();

		return $this->getDefaultCurrencySymbolByLocale($locale);
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	private function getDefaultCurrencySymbolByLocale($locale) {
		$currency = $this->currencyFacade->getDefaultCurrency();
		$intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

		return $intlCurrency->getSymbol();
	}

	/**
	 * @param int $currencyId
	 * @return string
	 */
	public function getCurrencySymbolByCurrencyId($currencyId) {
		$locale = $this->localization->getLocale();

		return $this->getCurrencySymbolByCurrencyIdAndLocale($currencyId, $locale);
	}

	/**
	 * @param int $currencyId
	 * @param string $locale
	 * @return string
	 */
	private function getCurrencySymbolByCurrencyIdAndLocale($currencyId, $locale) {
		$currency = $this->currencyFacade->getById($currencyId);
		$intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

		return $intlCurrency->getSymbol();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'price_extension';
	}
}
