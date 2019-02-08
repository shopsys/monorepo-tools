<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\Plugin\Cron\IteratedCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class ProductAvailabilityCronModule implements IteratedCronModuleInterface
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator
     */
    protected $productAvailabilityRecalculator;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator $productAvailabilityRecalculator
     */
    public function __construct(ProductAvailabilityRecalculator $productAvailabilityRecalculator)
    {
        $this->productAvailabilityRecalculator = $productAvailabilityRecalculator;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function sleep()
    {
    }

    public function wakeUp()
    {
    }

    /**
     * @inheritdoc
     */
    public function iterate()
    {
        if ($this->productAvailabilityRecalculator->runBatchOfScheduledDelayedRecalculations()) {
            $this->logger->debug('Batch is recalculated.');
            return true;
        } else {
            $this->logger->debug('All availabilities are recalculated.');
            return false;
        }
    }
}
