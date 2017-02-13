<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat;

use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Symfony\Bridge\Monolog\Logger;

class VatDeletionCronModule implements IteratedCronModuleInterface
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @var ProductInputPriceFacade
     */
    private $productInputPriceFacade;

    public function __construct(VatFacade $vatFacade, ProductInputPriceFacade $productInputPriceFacade)
    {
        $this->vatFacade = $vatFacade;
        $this->productInputPriceFacade = $productInputPriceFacade;
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
        $deletedVatsCount = $this->vatFacade->deleteAllReplacedVats();
        $this->logger->addInfo('Deleted ' . $deletedVatsCount . ' vats');
    }

    public function wakeUp()
    {
    }

    /**
     * @inheritdoc
     */
    public function iterate()
    {
        $batchResult = $this->productInputPriceFacade->replaceBatchVatAndRecalculateInputPrices();

        if ($batchResult) {
            $this->logger->debug('Batch is done');
        } else {
            $deletedVatsCount = $this->vatFacade->deleteAllReplacedVats();
            $this->logger->debug('All vats are replaced');
            $this->logger->addInfo('Deleted ' . $deletedVatsCount . ' vats');
        }

        return $batchResult;
    }
}
