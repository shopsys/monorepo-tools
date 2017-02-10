<?php

namespace Shopsys\ShopBundle\Twig;

use DateTime;
use IntlDateFormatter;
use Shopsys\ShopBundle\Component\Localization\DateTimeFormatter;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Twig_Extension;

class DateTimeFormatterExtension extends Twig_Extension
{
    /**
     * @var \Shopsys\ShopBundle\Component\Localization\DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        Localization $localization
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->localization = $localization;
    }

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters() {
        return [
            new \Twig_SimpleFilter(
                'formatDate',
                [$this, 'formatDate']
            ),
            new \Twig_SimpleFilter(
                'formatTime',
                [$this, 'formatTime']
            ),
            new \Twig_SimpleFilter(
                'formatDateTime',
                [$this, 'formatDateTime']
            ),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction(
                'dateOfCreation',
                [$this, 'dateOfCreation']
            ),
        ];
    }

    /**
     * @param mixed $dateTime
     * @param string|null $locale
     * @return string
     */
    public function formatDate($dateTime, $locale = null) {
        return $this->dateTimeFormatter->format(
            $this->convertToDateTime($dateTime),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::NONE,
            $this->getLocale($locale)
        );
    }

    /**
     * @param mixed $dateTime
     * @param string|null $locale
     * @return string
     */
    public function formatTime($dateTime, $locale = null) {
        return $this->dateTimeFormatter->format(
            $this->convertToDateTime($dateTime),
            IntlDateFormatter::NONE,
            IntlDateFormatter::MEDIUM,
            $this->getLocale($locale)
        );
    }

    /**
     * @param mixed $dateTime
     * @param string|null $locale
     * @return string
     */
    public function formatDateTime($dateTime, $locale = null) {
        return $this->dateTimeFormatter->format(
            $this->convertToDateTime($dateTime),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::MEDIUM,
            $this->getLocale($locale)
        );
    }

    /**
     * @param string|null $locale
     */
    private function getLocale($locale = null) {
        if ($locale === null) {
            $locale = $this->localization->getLocale();
        }

        return $locale;
    }

    /**
     * @param mixed $value
     * @return \DateTime
     */
    private function convertToDateTime($value) {
        if ($value instanceof DateTime) {
            return $value;
        } else {
            return new DateTime($value);
        }
    }

    /**
     * @param int $date
     * @return string
     */
    public function dateOfCreation($date) {
        $startDate = date('Y', strtotime('1-1-' . $date));
        $endDate = date('Y');

        if ($startDate === $endDate) {
            return $startDate;
        }

        return $startDate . ' - ' . $endDate;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'date_formatter_extension';
    }
}
