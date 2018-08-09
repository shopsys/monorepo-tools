<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class ElasticsearchExportCronModule implements SimpleCronModuleInterface
{

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchExportProductFacade
     */
    private $elasticsearchExportProductFacade;

    public function __construct(ElasticsearchExportProductFacade $elasticsearchExportProductFacade)
    {
        $this->elasticsearchExportProductFacade = $elasticsearchExportProductFacade;
    }

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->elasticsearchExportProductFacade->exportAll();
    }
}
