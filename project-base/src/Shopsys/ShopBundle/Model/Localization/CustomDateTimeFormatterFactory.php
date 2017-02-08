<?php

namespace SS6\ShopBundle\Model\Localization;

use IntlDateFormatter;
use SS6\ShopBundle\Component\Localization\DateTimeFormatPattern;
use SS6\ShopBundle\Component\Localization\DateTimeFormatPatternRepository;
use SS6\ShopBundle\Component\Localization\DateTimeFormatter;

class CustomDateTimeFormatterFactory {

	/**
	 * @return \SS6\ShopBundle\Component\Localization\DateTimeFormatter
	 */
	public function create() {
		$customDateTimeFormatPatternRepository = new DateTimeFormatPatternRepository();
		$customDateTimeFormatPatternRepository->add(
			new DateTimeFormatPattern('y-MM-dd', 'en', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE)
		);
		$customDateTimeFormatPatternRepository->add(
			new DateTimeFormatPattern('y-MM-dd, h:mm:ss a', 'en', IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM)
		);

		$dateTimeFormatter = new DateTimeFormatter($customDateTimeFormatPatternRepository);

		return $dateTimeFormatter;
	}

}
