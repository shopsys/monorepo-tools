<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
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
		return array(
			new Twig_SimpleFilter(
				'priceWithCurrencyAdmin',
				array($this, 'priceWithCurrencyAdminFilter'),
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFilter('priceWithCurrency', array($this, 'priceWithCurrencyFilter'), array('is_safe' => array('html'))),
			new Twig_SimpleFilter('price', array($this, 'priceFilter'), array('is_safe' => array('html'))),
			new Twig_SimpleFilter('priceText', array($this, 'priceTextFilter'), array('is_safe' => array('html'))),
		);
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction(
				'currencySymbolByDomainId',
				array($this, 'getCurrencySymbolByDomainId'),
				array('is_safe' => array('html'))
			),
		);
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
	 * @param int|null $domainId
	 * @return string
	 */
	public function priceWithCurrencyAdminFilter($price, $domainId = null) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$currencySymbol = $this->getCurrencySymbolByDomainId($domainId);
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
	public function getCurrencySymbolByDomainId($domainId = null) {
		if ($domainId === null) {
			return $this->currencyFacade->getDefaultCurrency()->getSymbol();
		} else {
			$currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
			return $currency->getSymbol();
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'price_extension';
	}
}
