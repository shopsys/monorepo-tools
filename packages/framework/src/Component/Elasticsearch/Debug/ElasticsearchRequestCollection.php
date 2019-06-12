<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Elasticsearch\Debug;

class ElasticsearchRequestCollection
{
    /**
     * @var array
     */
    protected $collectedData = [];

    /**
     * @return array
     */
    public function getCollectedData(): array
    {
        return $this->collectedData;
    }

    /**
     * @return int
     */
    public function getCollectedDataCount(): int
    {
        return count($this->collectedData);
    }

    /**
     * @return float
     */
    public function getTotalTime(): float
    {
        $totalRequestsTime = 0;
        $collectedData = $this->getCollectedData();
        foreach ($collectedData as $requestData) {
            $totalRequestsTime += $requestData['duration'];
        }

        return $totalRequestsTime;
    }

    /**
     * @param string $requestCurl
     * @param string|null $requestJson
     * @param mixed $requestData
     * @param string $method
     * @param string $uri
     * @param int|null $statusCode
     * @param mixed $response
     * @param float $duration
     */
    public function addRequest(
        string $requestCurl,
        ?string $requestJson,
        $requestData,
        string $method,
        string $uri,
        ?int $statusCode,
        $response,
        float $duration
    ): void {
        $this->collectedData[] = [
            'requestCurl' => $requestCurl,
            'requestJson' => $requestJson,
            'requestData' => $requestData,
            'method' => $method,
            'uri' => $uri,
            'statusCode' => $statusCode,
            'response' => $response,
            'duration' => $duration,
        ];
    }
}
