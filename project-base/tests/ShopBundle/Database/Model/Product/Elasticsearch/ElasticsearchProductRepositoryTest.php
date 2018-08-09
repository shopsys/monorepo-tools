<?php

namespace Tests\ShopBundle\Database\Model\Product\Elasticsearch;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ElasticsearchProductRepositoryTest extends DatabaseTestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductRepository
     */
    private $repository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->getContainer()->get(ElasticsearchProductRepository::class);
        $this->domain = $this->getContainer()->get(Domain::class);
    }

    public function testProductDataHaveExpectedStructure(): void
    {
        $data = $this->repository->getProductsData($this->domain->getId(), $this->domain->getLocale(), 0, 10);
        $this->assertCount(10, $data);

        $structure = array_keys(reset($data));
        sort($structure);

        $expectedStructure = [
            'id',
            'name',
            'catnum',
            'partno',
            'ean',
            'description',
            'shortDescription',
        ];
        sort($expectedStructure);

        $this->assertSame($expectedStructure, $structure);
    }
}
