<?php

namespace Shopsys\ShopBundle\Model\Statistics;

class StatisticsProcessingFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Statistics\StatisticsService
     */
    private $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Statistics\ValueByDateTimeDataPoint[] $valueByDateTimeDataPoints
     * @return string[]
     */
    public function getDateTimesFormattedToLocaleFormat(array $valueByDateTimeDataPoints)
    {
        return $this->statisticsService->getDateTimesFormattedToLocaleFormat($valueByDateTimeDataPoints);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Statistics\ValueByDateTimeDataPoint[] $valueByDateTimeDataPoints
     * @return int[]
     */
    public function getCounts(array $valueByDateTimeDataPoints)
    {
        return $this->statisticsService->getCounts($valueByDateTimeDataPoints);
    }
}
