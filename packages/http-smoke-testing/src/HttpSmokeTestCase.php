<?php

namespace Shopsys\HttpSmokeTesting;

use Shopsys\HttpSmokeTesting\RouterAdapter\SymfonyRouterAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class HttpSmokeTestCase extends KernelTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before data provider is executed and before each test.
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);
    }

    /**
     * The main test method for smoke testing of all routes in your application.
     *
     * You must configure the provided RequestDataSets by implementing customizeRouteConfigs method.
     * If you need custom behavior for creating or handling requests in your application you should override the
     * createRequest or handleRequest method.
     *
     * @param \Shopsys\HttpSmokeTesting\RequestDataSet $requestDataSet
     * @dataProvider httpResponseTestDataProvider
     */
    final public function testHttpResponse(RequestDataSet $requestDataSet)
    {
        $requestDataSet->executeCallsDuringTestExecution(static::$kernel->getContainer());

        if ($requestDataSet->isSkipped()) {
            $message = sprintf('Test for route "%s" was skipped.', $requestDataSet->getRouteName());
            $this->markTestSkipped($this->getMessageWithDebugNotes($requestDataSet, $message));
        }

        $request = $this->createRequest($requestDataSet);

        $response = $this->handleRequest($request);

        $this->assertResponse($response, $requestDataSet);
    }

    /**
     * Data provider for the testHttpResponse method.
     *
     * This method gets all RouteInfo objects provided by RouterAdapter. It then passes them into
     * customizeRouteConfigs() method for customization and returns the resulting RequestDataSet objects.
     *
     * @return \Shopsys\HttpSmokeTesting\RequestDataSet[][]
     */
    final public function httpResponseTestDataProvider()
    {
        $this->setUp();

        $requestDataSetGenerators = [];
        /* @var $requestDataSetGenerators \Shopsys\HttpSmokeTesting\RequestDataSetGenerator[] */

        $allRouteInfo = $this->getRouterAdapter()->getAllRouteInfo();
        foreach ($allRouteInfo as $routeInfo) {
            $requestDataSetGenerators[] = new RequestDataSetGenerator($routeInfo);
        }

        $routeConfigCustomizer = new RouteConfigCustomizer($requestDataSetGenerators);

        $this->customizeRouteConfigs($routeConfigCustomizer);

        $requestDataSets = [];
        foreach ($requestDataSetGenerators as $requestDataSetGenerator) {
            $requestDataSets = array_merge($requestDataSets, $requestDataSetGenerator->generateRequestDataSets());
        }

        return array_map(
            function (RequestDataSet $requestDataSet) {
                return [$requestDataSet];
            },
            $requestDataSets
        );
    }

    /**
     * @return \Shopsys\HttpSmokeTesting\RouterAdapter\RouterAdapterInterface
     */
    protected function getRouterAdapter()
    {
        $router = static::$kernel->getContainer()->get('router');

        return new SymfonyRouterAdapter($router);
    }

    /**
     * This method must be implemented to customize and configure the test cases for individual routes
     *
     * @param \Shopsys\HttpSmokeTesting\RouteConfigCustomizer $routeConfigCustomizer
     */
    abstract protected function customizeRouteConfigs(RouteConfigCustomizer $routeConfigCustomizer);

    /**
     * @param \Shopsys\HttpSmokeTesting\RequestDataSet $requestDataSet
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(RequestDataSet $requestDataSet)
    {
        $uri = $this->getRouterAdapter()->generateUri($requestDataSet);

        $request = Request::create($uri);

        $requestDataSet->getAuth()
            ->authenticateRequest($request);

        return $request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest(Request $request)
    {
        return static::$kernel->handle($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Shopsys\HttpSmokeTesting\RequestDataSet $requestDataSet
     */
    protected function assertResponse(Response $response, RequestDataSet $requestDataSet)
    {
        $failMessage = sprintf(
            'Failed asserting that status code %d for route "%s" is identical to expected %d',
            $response->getStatusCode(),
            $requestDataSet->getRouteName(),
            $requestDataSet->getExpectedStatusCode()
        );
        $this->assertSame(
            $requestDataSet->getExpectedStatusCode(),
            $response->getStatusCode(),
            $this->getMessageWithDebugNotes($requestDataSet, $failMessage)
        );
    }

    /**
     * @param \Shopsys\HttpSmokeTesting\RequestDataSet $requestDataSet
     * @param string $message
     * @return string
     */
    protected function getMessageWithDebugNotes(RequestDataSet $requestDataSet, $message)
    {
        if (count($requestDataSet->getDebugNotes()) > 0) {
            $indentedDebugNotes = array_map(function ($debugNote) {
                return "\n" . '  - ' . $debugNote;
            }, $requestDataSet->getDebugNotes());
            $message .= "\n" . 'Notes for this data set:' . implode($indentedDebugNotes);
        }

        return $message;
    }
}
