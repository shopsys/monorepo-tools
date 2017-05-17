<?php

namespace Tests\ShopBundle\Smoke\Http;

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
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @dataProvider httpResponseTestDataProvider
     */
    final public function testHttpResponse(TestCaseConfig $config)
    {
        $config->executeAllCustomizationsDelayedUntilTestExecution();

        if ($config->isIgnored()) {
            $ignoreMessage = sprintf('Test for route "%s" was ignored.', $config->getRouteName());
            $this->markTestSkipped($this->getMessageWithNotes($config, $ignoreMessage));
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

        $router = $this->getRouter();
        $routeConfigsBuilder = new RouteConfigsBuilder($router);

        $this->customizeRouteConfigs($routeConfigsBuilder);
        return array_map(
            function (TestCaseConfig $config) {
                return [$config];
            },
            $routeConfigsBuilder->getTestCaseConfigs()
        );
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    protected function getRouter()
    {
        return static::$kernel->getContainer()->get('router');
    }

    /**
     * This method should be implemented to customize and configure the test cases for individual routes
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigsBuilder $routeConfigsBuilder
     */
    abstract protected function customizeRouteConfigs(RouteConfigsBuilder $routeConfigsBuilder);

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(TestCaseConfig $config)
    {
        $router = $this->getRouter();
        $uri = $router->generate($config->getRouteName(), $config->getParameters());

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
            $this->getMessageWithNotes($config, $failMessage)
        );
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @param string $message
     * @return string
     */
    protected function getMessageWithNotes(TestCaseConfig $config, $message)
    {
        if (count($config->getNotes()) > 0) {
            $indentedNotes = array_map(function ($note) {
                return "\n" . '  - ' . $note;
            }, $config->getNotes());
            $message .= "\n" . 'Notes for this data set:' . implode($indentedNotes);
        }

        return $message;
    }
}
