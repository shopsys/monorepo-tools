<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class ElasticsearchExportProductFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductExporter
     */
    protected $exporter;

    public function __construct(Domain $domain, ElasticsearchProductExporter $exporter)
    {
        $this->domain = $domain;
        $this->exporter = $exporter;
    }

    public function exportAll(): void
    {
        foreach ($this->domain->getAll() as $domain) {
            $id = $domain->getId();
            $locale = $domain->getLocale();
            $this->exporter->export($id, $locale);
        }
    }
}
