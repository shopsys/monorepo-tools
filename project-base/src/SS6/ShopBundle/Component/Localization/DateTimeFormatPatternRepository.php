<?php

namespace SS6\ShopBundle\Component\Localization;

class DateTimeFormatPatternRepository {

	/**
	 * @var \SS6\ShopBundle\Component\Localization\DateTimeFormatPattern[]
	 */
	private $dateTimeFormatPatterns;

	public function __construct() {
		$this->dateTimeFormatPatterns = [];
	}

	public function add(DateTimeFormatPattern $dateTimePattern) {
		$this->dateTimeFormatPatterns[] = $dateTimePattern;
	}

	/**
	 * @param string $locale
	 * @param int $dateType @link http://php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
	 * @param int $timeType @link http://php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
	 * @return \SS6\ShopBundle\Component\Localization\DateTimeFormatPattern|null
	 */
	public function findDateTimePattern($locale, $dateType, $timeType) {
		foreach ($this->dateTimeFormatPatterns as $dateTimePattern) {
			if ($this->isMatching($dateTimePattern, $locale, $dateType, $timeType)) {
				return $dateTimePattern;
			}
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Localization\DateTimeFormatPattern $dateTimePattern
	 * @param string $locale
	 * @param int|null $dateType
	 * @param int|null $timeType
	 * @return bool
	 */
	private function isMatching(DateTimeFormatPattern $dateTimePattern, $locale, $dateType, $timeType) {
		if ($dateTimePattern->getLocale() !== $locale) {
			return false;
		}

		if ($dateTimePattern->getDateType() !== null && $dateTimePattern->getDateType() !== $dateType) {
			return false;
		}

		if ($dateTimePattern->getTimeType() !== null && $dateTimePattern->getTimeType() !== $timeType) {
			return false;
		}

		return true;
	}

}
