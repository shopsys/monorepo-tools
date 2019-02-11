<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class ProductSearchExportCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportFacade
     */
    protected $productSearchExportFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportFacade $productSearchExportFacade
     */
    public function __construct(ProductSearchExportFacade $productSearchExportFacade)
    {
        $this->productSearchExportFacade = $productSearchExportFacade;
    }

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->productSearchExportFacade->exportAll();
    }
}
