<?php

namespace Shopsys\FrameworkBundle\Twig;

use CommerceGuys\Intl\Formatter\NumberFormatter;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Twig_Extension;

class NumberFormatterExtension extends Twig_Extension
{
    protected const MINIMUM_FRACTION_DIGITS = 0;
    protected const MAXIMUM_FRACTION_DIGITS = 10;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    protected $localization;

    /**
     * @var \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface
     */
    protected $numberFormatRepository;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface $numberFormatRepository
     */
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
        $numberFormatter = new NumberFormatter($this->numberFormatRepository, [
            'locale' => $this->getLocale($locale),
            'style' => 'decimal',
            'minimum_fraction_digits' => static::MINIMUM_FRACTION_DIGITS,
            'maximum_fraction_digits' => static::MAXIMUM_FRACTION_DIGITS,
        ]);

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
        $numberFormatter = new NumberFormatter($this->numberFormatRepository, [
            'locale' => $this->getLocale($locale),
            'style' => 'decimal',
            'minimum_fraction_digits' => $minimumFractionDigits,
            'maximum_fraction_digits' => static::MAXIMUM_FRACTION_DIGITS,
        ]);

        return $numberFormatter->format($number);
    }

    /**
     * @param mixed $number
     * @param string|null $locale
     * @return string
     */
    public function formatPercent($number, $locale = null)
    {
        $numberFormatter = new NumberFormatter($this->numberFormatRepository, [
            'locale' => $this->getLocale($locale),
            'style' => 'percent',
            'minimum_fraction_digits' => static::MINIMUM_FRACTION_DIGITS,
            'maximum_fraction_digits' => static::MAXIMUM_FRACTION_DIGITS,
        ]);

        return $numberFormatter->format($number / 100);
    }

    /**
     * @param string|null $locale
     * @return string
     */
    protected function getLocale($locale = null)
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
