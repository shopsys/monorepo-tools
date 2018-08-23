<?php

namespace Tests\FrameworkBundle\Unit\Component\Microservice\ProductSearchExport;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient;
use Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\Exception\ProductSearchExportStructureException;
use Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSearchExportStructureFacadeTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain|\PHPUnit\Framework\MockObject\MockObject
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $microserviceClient;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade
     */
    private $productSearchExportStructureFacade;

    protected function setUp()
    {
        $this->microserviceClient = $this->createMock(MicroserviceClient::class);
        $this->domain = $this->createMock(Domain::class);
        $this->productSearchExportStructureFacade = new ProductSearchExportStructureFacade($this->microserviceClient, $this->domain);
    }

    public function testCreateSuccessIndex(): void
    {
        $this->domain->method('getAllIds')->willReturn([1, 2]);
        $this->microserviceClient->method('post')->withConsecutive([1], [2]);

        $output = $this->createOutput(4);
        $this->productSearchExportStructureFacade->createIndexes($output);
    }

    public function testCreateThatReturnsErrorWithMessage(): void
    {
        $this->domain->method('getAllIds')->willReturn([0]);

        $body = json_encode(['message' => 'Index 1 already exists']);
        $exception = $this->createServerException($body);
        $this->microserviceClient->method('post')->willThrowException($exception);

        $this->expectException(ProductSearchExportStructureException::class);

        $output = $this->createOutput(1);
        $this->productSearchExportStructureFacade->createIndexes($output);
    }

    public function testCreateThatReturnsErrorWithoutMessage(): void
    {
        $this->domain->method('getAllIds')->willReturn([0]);

        $exception = $this->createServerException();
        $this->microserviceClient->method('post')->willThrowException($exception);

        $this->expectException(ProductSearchExportStructureException::class);

        $output = $this->createOutput(1);
        $this->productSearchExportStructureFacade->createIndexes($output);
    }

    public function testDeleteSuccess(): void
    {
        $this->domain->method('getAllIds')->willReturn([1, 2]);
        $this->microserviceClient->method('delete')->withConsecutive([1], [2]);

        $output = $this->createOutput(2);
        $this->productSearchExportStructureFacade->deleteIndexes($output);
    }

    /**
     * @param int $writelnCalledAtLeast
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOutput(int $writelnCalledAtLeast): MockObject
    {
        $output = $this->createMock(OutputInterface::class);
        $output->expects($this->atLeast($writelnCalledAtLeast))->method('writeln');
        return $output;
    }

    /**
     * @param string|null $body
     * @return ServerException
     */
    protected function createServerException(string $body = null): ServerException
    {
        $request = new Request('', '');
        $response = new Response(500, [], $body);
        $exception = new ServerException('', $request, $response);
        return $exception;
    }
}
