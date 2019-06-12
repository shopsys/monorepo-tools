<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Elasticsearch\Debug;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ElasticsearchCollector extends DataCollector
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\Debug\ElasticsearchRequestCollection
     */
    protected $elasticsearchRequestCollection;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\Debug\ElasticsearchRequestCollection $elasticsearchRequestCollection
     */
    public function __construct(ElasticsearchRequestCollection $elasticsearchRequestCollection)
    {
        $this->elasticsearchRequestCollection = $elasticsearchRequestCollection;
    }

    /**
     * @inheritdoc
     */
    public function collect(Request $request, Response $response, Exception $exception = null): void
    {
        $this->data = [
            'requests' => $this->elasticsearchRequestCollection->getCollectedData(),
            'requestsCount' => $this->elasticsearchRequestCollection->getCollectedDataCount(),
            'totalRequestsTime' => $this->elasticsearchRequestCollection->getTotalTime() * 1000,
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'shopsys.elasticsearch_collector';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
