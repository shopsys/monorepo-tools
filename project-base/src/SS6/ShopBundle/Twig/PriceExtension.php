<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class PriceExtension extends Twig_Extension {

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

	public function __construct(TranslatorInterface $translator, CurrencyFacade $currencyFacade, Domain $domain) {
		$this->translator = $translator;
		$this->currencyFacade = $currencyFacade;
		$this->domain = $domain;
	}

	/**
	 * @return array
	 */
	public function getFilters() {
		return [
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
			new Twig_SimpleFilter('priceWithCurrency', [$this, 'priceWithCurrencyFilter'], ['is_safe' => ['html']]),
			new Twig_SimpleFilter('price', [$this, 'priceFilter'], ['is_safe' => ['html']]),
			new Twig_SimpleFilter('priceText', [$this, 'priceTextFilter'], ['is_safe' => ['html']]),
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
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId())->getSymbol();
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;' . $currencySymbol;

		return $price;
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceWithCurrencyAdminFilter($price) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $this->getCurrencySymbolDefault();
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;' . $currencySymbol;

		return $price;
	}

	/**
	 * @param string $price
	 * @param int $domainId
	 * @return string
	 */
	public function priceWithCurrencyByDomainIdFilter($price, $domainId) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $this->getCurrencySymbolByDomainId($domainId);
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;' . $currencySymbol;

		return $price;
	}

	public function priceWithCurrencyByCurrencyIdFilter($price, $currencyId) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $this->currencyFacade->getById($currencyId)->getSymbol();
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;' . $currencySymbol;

		return $price;
	}

	/**
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return string
	 */
	public function priceWithCurrencyFilter($price, Currency $currency) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $currency->getSymbol();
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;' . $currencySymbol;

		return $price;
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
