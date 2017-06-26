<?php

namespace Shopsys\ShopBundle\Model\Statistics;

use DateInterval;
use DateTime;

class StatisticsFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Statistics\StatisticsRepository
     */
    private $statisticsRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Statistics\StatisticsService
     */
    private $statisticsService;

    /**
     * @param \Shopsys\ShopBundle\Model\Statistics\StatisticsRepository $statisticsRepository
     * @param \Shopsys\ShopBundle\Model\Statistics\StatisticsService $statisticsService
     */
    public function __construct(
        StatisticsRepository $statisticsRepository,
        StatisticsService $statisticsService
    ) {
        $this->statisticsRepository = $statisticsRepository;
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Statistics\ValueByDateTimeDataPoint[]
     */
    public function getCustomersRegistrationsCountByDayInLastTwoWeeks()
    {
        $startDataTime = new DateTime('- 2 weeks midnight');
        $tomorrowDateTime = new DateTime('tomorrow');

        $valueByDateTimeDataPoints = $this->statisticsRepository->getCustomersRegistrationsCountByDayBetweenTwoDateTimes(
            $startDataTime,
            $tomorrowDateTime
        );

        $valueByDateTimeDataPoints = $this->statisticsService->normalizeDataPointsByDateTimeIntervals(
            $valueByDateTimeDataPoints,
            $startDataTime,
            $tomorrowDateTime,
            DateInterval::createFromDateString('+ 1 day')
        );

        return $valueByDateTimeDataPoints;
    }
}
