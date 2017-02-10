<?php

namespace Shopsys\ShopBundle\Model\Localization;

use IntlDateFormatter;
use Shopsys\ShopBundle\Component\Localization\DateTimeFormatPattern;
use Shopsys\ShopBundle\Component\Localization\DateTimeFormatPatternRepository;
use Shopsys\ShopBundle\Component\Localization\DateTimeFormatter;

class CustomDateTimeFormatterFactory
{
    /**
     * @return \Shopsys\ShopBundle\Component\Localization\DateTimeFormatter
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
