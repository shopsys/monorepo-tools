<?php

namespace Shopsys\FrameworkBundle\Model\Statistics;

class StatisticsProcessingFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Statistics\StatisticsService
     */
    private $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[] $valueByDateTimeDataPoints
     * @return string[]
     */
    public function getDateTimesFormattedToLocaleFormat(array $valueByDateTimeDataPoints)
    {
        return $this->statisticsService->getDateTimesFormattedToLocaleFormat($valueByDateTimeDataPoints);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[] $valueByDateTimeDataPoints
     * @return int[]
     */
    public function getCounts(array $valueByDateTimeDataPoints)
    {
        return $this->statisticsService->getCounts($valueByDateTimeDataPoints);
    }
}
