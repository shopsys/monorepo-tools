<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Statistics;

use DateInterval;
use DateTime;
use DateTimeImmutable as DateTimeImmutable;

class StatisticsFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Statistics\StatisticsRepository
     */
    protected $statisticsRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPointFormatter
     */
    protected $valueByDateTimeDataPointFormatter;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Statistics\StatisticsRepository $statisticsRepository
     * @param \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPointFormatter $valueByDateTimeDataPointFormatter
     */
    public function __construct(
        StatisticsRepository $statisticsRepository,
        ValueByDateTimeDataPointFormatter $valueByDateTimeDataPointFormatter
    ) {
        $this->statisticsRepository = $statisticsRepository;
        $this->valueByDateTimeDataPointFormatter = $valueByDateTimeDataPointFormatter;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[]
     */
    public function getCustomersRegistrationsCountByDayInLastTwoWeeks(): array
    {
        $startDataTime = new DateTime('- 2 weeks midnight');
        $tomorrowDateTime = new DateTime('tomorrow');

        $valueByDateTimeDataPoints = $this->statisticsRepository->getCustomersRegistrationsCountByDayBetweenTwoDateTimes(
            $startDataTime,
            $tomorrowDateTime
        );

        $valueByDateTimeDataPoints = $this->valueByDateTimeDataPointFormatter->normalizeDataPointsByDateTimeIntervals(
            $valueByDateTimeDataPoints,
            $startDataTime,
            $tomorrowDateTime,
            DateInterval::createFromDateString('+ 1 day')
        );

        return $valueByDateTimeDataPoints;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[]
     */
    public function getNewOrdersCountByDayInLastTwoWeeks(): array
    {
        $startDataTime = new DateTime('- 2 weeks midnight');
        $tomorrowDateTime = new DateTime('tomorrow');

        $valueByDateTimeDataPoints = $this->statisticsRepository->getNewOrdersCountByDayBetweenTwoDateTimes(
            $startDataTime,
            $tomorrowDateTime
        );

        $valueByDateTimeDataPoints = $this->valueByDateTimeDataPointFormatter->normalizeDataPointsByDateTimeIntervals(
            $valueByDateTimeDataPoints,
            $startDataTime,
            $tomorrowDateTime,
            DateInterval::createFromDateString('+ 1 day')
        );

        return $valueByDateTimeDataPoints;
    }

    /**
     * @param int $fromDays
     * @param int|null $toDays
     * @return int
     */
    public function getNewCustomersCount(int $fromDays, ?int $toDays = null): int
    {
        $startDateTime = new DateTimeImmutable('- ' . $fromDays . ' days');
        $endDateTime = $this->getToDateTime($toDays);

        return $this->statisticsRepository->getNewCustomersCountBetweenDates($startDateTime, $endDateTime);
    }

    /**
     * @param int $fromDays
     * @param int|null $toDays
     * @return int
     */
    public function getOrdersCount(int $fromDays, ?int $toDays = null): int
    {
        $startDateTime = new DateTimeImmutable('- ' . $fromDays . ' days');
        $endDateTime = $this->getToDateTime($toDays);

        return $this->statisticsRepository->getOrdersCountBetweenDates($startDateTime, $endDateTime);
    }

    /**
     * @param int $fromDays
     * @param int|null $toDays
     * @return int
     */
    public function getOrdersValue(int $fromDays, ?int $toDays = null): int
    {
        $startDateTime = new DateTimeImmutable('- ' . $fromDays . ' days');
        $endDateTime = $this->getToDateTime($toDays);

        return $this->statisticsRepository->getOrdersValueBetweenDates($startDateTime, $endDateTime);
    }

    /**
     * @param int|null $toDays
     * @return \DateTimeImmutable
     */
    protected function getToDateTime(?int $toDays = null): DateTimeImmutable
    {
        if ($toDays === null) {
            $endDateTime = new DateTimeImmutable('now');
        } else {
            $endDateTime = new DateTimeImmutable('- ' . $toDays . ' days');
        }

        return $endDateTime;
    }
}
