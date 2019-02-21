<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Twig;

use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use CommerceGuys\Intl\Formatter\NumberFormatter;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class PriceExtension extends Twig_Extension
{
    const MINIMUM_FRACTION_DIGITS = 2;
    const MAXIMUM_FRACTION_DIGITS = 10;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface $numberFormatRepository
     * @param \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface $intlCurrencyRepository
     */
    public function __construct(
        CurrencyFacade $currencyFacade,
        Domain $domain,
        Localization $localization,
        NumberFormatRepositoryInterface $numberFormatRepository,
        CurrencyRepositoryInterface $intlCurrencyRepository
    ) {
        $this->currencyFacade = $currencyFacade;
        $this->domain = $domain;
        $this->localization = $localization;
        $this->numberFormatRepository = $numberFormatRepository;
        $this->intlCurrencyRepository = $intlCurrencyRepository;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
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
    public function getFunctions(): array
    {
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
            new Twig_SimpleFunction(
                'currencyCode',
                [$this, 'getCurrencyCodeByDomainId']
            ),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @return string
     */
    public function priceFilter(Money $price): string
    {
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($this->domain->getId());

        return $this->formatCurrency($price, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @return string
     */
    public function priceTextFilter(Money $price): string
    {
        if ($price->isZero()) {
            return t('Free');
        } else {
            return $this->priceFilter($price);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @param int $currencyId
     * @param string $locale
     * @return string
     */
    public function priceTextWithCurrencyByCurrencyIdAndLocaleFilter(Money $price, int $currencyId, string $locale): string
    {
        if ($price->isZero()) {
            return t('Free');
        } else {
            $currency = $this->currencyFacade->getById($currencyId);
            return $this->formatCurrency($price, $currency, $locale);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return string
     */
    public function priceWithCurrencyFilter(Money $price, Currency $currency): string
    {
        return $this->formatCurrency($price, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @return string
     */
    public function priceWithCurrencyAdminFilter(Money $price): string
    {
        $currency = $this->currencyFacade->getDefaultCurrency();

        return $this->formatCurrency($price, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @param int $domainId
     * @return string
     */
    public function priceWithCurrencyByDomainIdFilter(Money $price, int $domainId): string
    {
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

        return $this->formatCurrency($price, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @param int $currencyId
     * @return string
     */
    public function priceWithCurrencyByCurrencyIdFilter(Money $price, int $currencyId): string
    {
        $currency = $this->currencyFacade->getById($currencyId);

        return $this->formatCurrency($price, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $price
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param string|null $locale
     * @return string
     */
    private function formatCurrency(Money $price, Currency $currency, string $locale = null): string
    {
        if ($locale === null) {
            $locale = $this->localization->getLocale();
        }

        $numberFormatter = $this->getNumberFormatter($locale);
        $intlCurrency = $this->intlCurrencyRepository->get(
            $currency->getCode(),
            $locale
        );

        return $numberFormatter->formatCurrency($price->toString(), $intlCurrency);
    }

    /**
     * @param string $locale
     * @return \CommerceGuys\Intl\Formatter\NumberFormatter
     */
    private function getNumberFormatter(string $locale): NumberFormatter
    {
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
    public function getCurrencySymbolByDomainId(int $domainId): string
    {
        $locale = $this->localization->getLocale();

        return $this->getCurrencySymbolByDomainIdAndLocale($domainId, $locale);
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getCurrencyCodeByDomainId(int $domainId): string
    {
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

        return $currency->getCode();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return string
     */
    private function getCurrencySymbolByDomainIdAndLocale(int $domainId, string $locale): string
    {
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
        $intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

        return $intlCurrency->getSymbol();
    }

    /**
     * @return string
     */
    public function getDefaultCurrencySymbol(): string
    {
        $locale = $this->localization->getLocale();

        return $this->getDefaultCurrencySymbolByLocale($locale);
    }

    /**
     * @param string $locale
     * @return string
     */
    private function getDefaultCurrencySymbolByLocale(string $locale): string
    {
        $currency = $this->currencyFacade->getDefaultCurrency();
        $intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

        return $intlCurrency->getSymbol();
    }

    /**
     * @param int $currencyId
     * @return string
     */
    public function getCurrencySymbolByCurrencyId(int $currencyId): string
    {
        $locale = $this->localization->getLocale();

        return $this->getCurrencySymbolByCurrencyIdAndLocale($currencyId, $locale);
    }

    /**
     * @param int $currencyId
     * @param string $locale
     * @return string
     */
    private function getCurrencySymbolByCurrencyIdAndLocale(int $currencyId, string $locale): string
    {
        $currency = $this->currencyFacade->getById($currencyId);
        $intlCurrency = $this->intlCurrencyRepository->get($currency->getCode(), $locale);

        return $intlCurrency->getSymbol();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'price_extension';
    }
}
