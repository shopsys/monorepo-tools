<?php

namespace Shopsys\FrameworkBundle\Model\Statistics;

use DateInterval;
use DateTime;

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
    public function getCustomersRegistrationsCountByDayInLastTwoWeeks()
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
    public function getNewOrdersCountByDayInLastTwoWeeks()
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
}
