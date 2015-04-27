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
				[$this, 'getCurrencySymbolDefault'],
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
	 * @return string
	 */
	private function formatCurrency($price, Currency $currency) {
		if (!is_numeric($price)) {
			return $price;
		}

		$numberFormatter = $this->getNumberFormatter();
		$intlCurrency = $this->intlCurrencyRepository->get(
			$currency->getCode(),
			$this->localization->getLocale()
		);

		return $numberFormatter->formatCurrency($price, $intlCurrency);
	}

	/**
	 * @return \CommerceGuys\Intl\Formatter\NumberFormatter
	 */
	private function getNumberFormatter() {
		$locale = $this->localization->getLocale();

		$numberFormat = $this->numberFormatRepository->get($locale);
		$numberFormatter = new NumberFormatter($numberFormat, NumberFormatter::CURRENCY);
		$numberFormatter->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS);
		$numberFormatter->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

		return $numberFormatter;
	}

	/**
	 * @param int|null $domainId
	 * @return string
	 */
	public function getCurrencySymbolByDomainId($domainId) {
		$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
		return $currency->getSymbol();
	}

	/**
	 * @return string
	 */
	public function getCurrencySymbolDefault() {
		return $this->currencyFacade->getDefaultCurrency()->getSymbol();
	}

	/**
	 * @param int $currencyId
	 * @return string
	 */
	public function getCurrencySymbolByCurrencyId($currencyId) {
		return $this->currencyFacade->getById($currencyId)->getSymbol();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'price_extension';
	}
}
