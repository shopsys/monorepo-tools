<?php

namespace Shopsys\FrameworkBundle\Model\Product\ProductSearchExport;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class ProductSearchExportCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportFacade
     */
    private $productSearchExportFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportFacade $productSearchExportFacade
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
