<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

use IntlDateFormatter;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatPattern;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatPatternRepository;
use Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter;

class CustomDateTimeFormatterFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Localization\DateTimeFormatter
     */
    public function create(): DateTimeFormatter
    {
        $customDateTimeFormatPatternRepository = new DateTimeFormatPatternRepository();
        $customDateTimeFormatPatternRepository->add(
            new DateTimeFormatPattern('y-MM-dd', 'en', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE)
        );
        $customDateTimeFormatPatternRepository->add(
            new DateTimeFormatPattern('y-MM-dd, h:mm:ss a', 'en', IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM)
        );

        $classData = $this->entityNameResolver->resolve(DateTimeFormatter::class);
        $dateTimeFormatter = new $classData($customDateTimeFormatPatternRepository);

        return $dateTimeFormatter;
    }
}
