<?php

namespace Shopsys\FrameworkBundle\Model\Product\ProductSearchExport;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class ProductSearchExportFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportExporter
     */
    protected $exporter;

    public function __construct(Domain $domain, ProductSearchExportExporter $exporter)
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
