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
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @dataProvider httpResponseTestDataProvider
     */
    final public function testHttpResponse(TestCaseConfig $config)
    {
        $config->executeAllCustomizationsDelayedUntilTestExecution();

        if ($config->isSkipped()) {
            $message = sprintf('Test for route "%s" was skipped.', $config->getRouteName());
            $this->markTestSkipped($this->getMessageWithDebugNotes($config, $message));
        }

        $request = $this->createRequest($config);

        $response = $this->handleRequest($request);

        $this->assertResponse($response, $config);
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig[][]
     */
    final public function httpResponseTestDataProvider()
    {
        static::setUp();

        $routeConfigs = $this->getRouterAdapter()->getRouteConfigs();

        $routeConfigsBuilder = new RouteConfigCustomizer($routeConfigs);

        $this->customizeRouteConfigs($routeConfigsBuilder);

        $testCaseConfigs = [];
        foreach ($routeConfigs as $routeConfig) {
            $testCaseConfigs = array_merge($testCaseConfigs, $routeConfig->generateTestCaseConfigs());
        }

        return array_map(
            function (TestCaseConfig $config) {
                return [$config];
            },
            $testCaseConfigs
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
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(TestCaseConfig $config)
    {
        $uri = $this->getRouterAdapter()->generateUri($config);

        $request = Request::create($uri);

        if ($config->getUsername() !== null) {
            $request->server->set('PHP_AUTH_USER', $config->getUsername());
            $request->headers->set('PHP_AUTH_USER', $config->getUsername());

            if ($config->getPassword() !== null) {
                $request->server->set('PHP_AUTH_PW', $config->getPassword());
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
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     */
    protected function assertResponse(Response $response, TestCaseConfig $config)
    {
        $failMessage = sprintf(
            'Failed asserting that status code %d for route "%s" is identical to expected %d',
            $response->getStatusCode(),
            $config->getRouteName(),
            $config->getExpectedStatusCode()
        );
        $this->assertSame(
            $config->getExpectedStatusCode(),
            $response->getStatusCode(),
            $this->getMessageWithDebugNotes($config, $failMessage)
        );
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @param string $message
     * @return string
     */
    protected function getMessageWithDebugNotes(TestCaseConfig $config, $message)
    {
        if (count($config->getDebugNotes()) > 0) {
            $indentedDebugNotes = array_map(function ($debugNote) {
                return "\n" . '  - ' . $debugNote;
            }, $config->getDebugNotes());
            $message .= "\n" . 'Notes for this data set:' . implode($indentedDebugNotes);
        }

        return $message;
    }
}
