<?php

namespace Shopsys\ShopBundle\Twig;

use CommerceGuys\Intl\Formatter\NumberFormatter;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Twig_Extension;

class NumberFormatterExtension extends Twig_Extension
{
    const MINIMUM_FRACTION_DIGITS = 0;
    const MAXIMUM_FRACTION_DIGITS = 10;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface
     */
    private $numberFormatRepository;

    public function __construct(
        Localization $localization,
        NumberFormatRepositoryInterface $numberFormatRepository
    ) {
        $this->localization = $localization;
        $this->numberFormatRepository = $numberFormatRepository;
    }

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'formatNumber',
                [$this, 'formatNumber']
            ),
            new \Twig_SimpleFilter(
                'formatDecimalNumber',
                [$this, 'formatDecimalNumber']
            ),
            new \Twig_SimpleFilter(
                'formatPercent',
                [$this, 'formatPercent']
            ),
        ];
    }

    /**
     * @param mixed $number
     * @param string|null $locale
     * @return string
     */
    public function formatNumber($number, $locale = null)
    {
        $numberFormat = $this->numberFormatRepository->get($this->getLocale($locale));
        $numberFormatter = new NumberFormatter($numberFormat, NumberFormatter::DECIMAL);
        $numberFormatter->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS);
        $numberFormatter->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

        return $numberFormatter->format($number);
    }

    /**
     * @param mixed $number
     * @param int $minimumFractionDigits
     * @param string|null $locale
     * @return string
     */
    public function formatDecimalNumber($number, $minimumFractionDigits, $locale = null)
    {
        $numberFormat = $this->numberFormatRepository->get($this->getLocale($locale));
        $numberFormatter = new NumberFormatter($numberFormat, NumberFormatter::DECIMAL);
        $numberFormatter->setMinimumFractionDigits($minimumFractionDigits);
        $numberFormatter->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

        return $numberFormatter->format($number);
    }

    /**
     * @param mixed $number
     * @param string|null $locale
     * @return string
     */
    public function formatPercent($number, $locale = null)
    {
        $numberFormat = $this->numberFormatRepository->get($this->getLocale($locale));
        $numberFormatter = new NumberFormatter($numberFormat, NumberFormatter::PERCENT);
        $numberFormatter->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS);
        $numberFormatter->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

        return $numberFormatter->format($number);
    }

    /**
     * @param string|null $locale
     * @return string
     */
    private function getLocale($locale = null)
    {
        if ($locale === null) {
            $locale = $this->localization->getLocale();
        }

        return $locale;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'number_formatter_extension';
    }
}
