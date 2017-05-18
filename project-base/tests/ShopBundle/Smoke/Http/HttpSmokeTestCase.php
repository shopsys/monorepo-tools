<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\ShopBundle\Smoke\Http\SymfonyRouterAdapter;

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
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
     * @dataProvider httpResponseTestDataProvider
     */
    final public function testHttpResponse(RequestDataSet $requestDataSet)
    {
        $requestDataSet->executeAllCustomizationsDelayedUntilTestExecution();

        if ($requestDataSet->isSkipped()) {
            $message = sprintf('Test for route "%s" was skipped.', $requestDataSet->getRouteName());
            $this->markTestSkipped($this->getMessageWithDebugNotes($requestDataSet, $message));
        }

        $request = $this->createRequest($requestDataSet);

        $response = $this->handleRequest($request);

        $this->assertResponse($response, $requestDataSet);
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\RequestDataSet[][]
     */
    final public function httpResponseTestDataProvider()
    {
        static::setUp();

        $routeConfigs = $this->getRouterAdapter()->getRouteConfigs();

        $routeConfigsBuilder = new RouteConfigCustomizer($routeConfigs);

        $this->customizeRouteConfigs($routeConfigsBuilder);

        $requestDataSets = [];
        foreach ($routeConfigs as $routeConfig) {
            $requestDataSets = array_merge($requestDataSets, $routeConfig->generateRequestDataSets());
        }

        return array_map(
            function (RequestDataSet $requestDataSet) {
                return [$requestDataSet];
            },
            $requestDataSets
        );
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\RouterAdapterInterface
     */
    protected function getRouterAdapter()
    {
        $router = static::$kernel->getContainer()->get('router');

        return new SymfonyRouterAdapter($router);
    }

    /**
     * This method must be implemented to customize and configure the test cases for individual routes
     *
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    abstract protected function customizeRouteConfigs(RouteConfigCustomizer $routeConfigCustomizer);

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(RequestDataSet $requestDataSet)
    {
        $uri = $this->getRouterAdapter()->generateUri($requestDataSet);

        $request = Request::create($uri);

        if ($requestDataSet->getUsername() !== null) {
            $request->server->set('PHP_AUTH_USER', $requestDataSet->getUsername());
            $request->headers->set('PHP_AUTH_USER', $requestDataSet->getUsername());

            if ($requestDataSet->getPassword() !== null) {
                $request->server->set('PHP_AUTH_PW', $requestDataSet->getPassword());
            }
            $request->headers->add($request->server->getHeaders());
        }

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
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
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
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
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
