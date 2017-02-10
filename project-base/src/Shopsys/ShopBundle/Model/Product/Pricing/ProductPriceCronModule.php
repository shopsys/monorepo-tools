<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Symfony\Bridge\Monolog\Logger;

class ProductPriceCronModule implements IteratedCronModuleInterface
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator
     */
    private $productPriceRecalculator;

    public function __construct(ProductPriceRecalculator $productPriceRecalculator) {
        $this->productPriceRecalculator = $productPriceRecalculator;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    public function sleep() {
    }

    public function wakeUp() {
    }

    /**
     * @inheritdoc
     */
    public function iterate() {
        if ($this->productPriceRecalculator->runBatchOfScheduledDelayedRecalculations()) {
            $this->logger->debug('Batch is recalculated.');
            return true;
        } else {
            $this->logger->debug('All prices are recalculated.');
            return false;
        }
    }
}
