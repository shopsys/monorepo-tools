<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

use IntlDateFormatter;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatPattern;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatPatternRepository;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter;

class CustomDateTimeFormatterFactory
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter
     */
    public function create()
    {
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
