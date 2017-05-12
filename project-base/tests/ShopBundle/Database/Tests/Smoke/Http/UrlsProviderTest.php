<?php

namespace Tests\ShopBundle\Database\Tests\Smoke\Http;

use ReflectionClass;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\CurrentDomainRouter;
use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\Routing\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\ShopBundle\Smoke\Http\UrlsProvider;
use Tests\ShopBundle\Test\FunctionalTestCase;

class UrlsProviderTest extends FunctionalTestCase
{
    public function testGetFrontTestableUrlsProviderData()
    {
        $routeCollection = [
            'baz' => new Route('baz'),
            'front_foo' => new Route('foo'),
            'front_logged' => new Route('foo'),
            'admin_bar' => new Route('bar'),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName) {
            switch ($routeName) {
                case 'front_foo':
                    return 'foo';
                case 'front_logged':
                    return 'logged';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);

        $reflectionClass = new ReflectionClass(UrlsProvider::class);
        $reflectionProperty = $reflectionClass->getProperty('frontAsLoggedRouteNames');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($urlsProvider, [
            'front_logged',
        ]);

        $providerData = $urlsProvider->getFrontTestableUrlsProviderData();

        $this->assertSame('front_foo', $providerData[0][0]);
        $this->assertSame('foo', $providerData[0][1]);
        $this->assertSame(200, $providerData[0][2]);
        $this->assertFalse($providerData[0][3]);

        $this->assertSame('front_logged', $providerData[1][0]);
        $this->assertSame('logged', $providerData[1][1]);
        $this->assertSame(200, $providerData[1][2]);
        $this->assertTrue($providerData[1][3]);
    }

    public function testGetAdminTestableUrlsProviderData()
    {
        $routeCollection = [
            'baz' => new Route('baz'),
            'front_foo' => new Route('foo'),
            'admin_bar' => new Route('bar'),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName) {
            if ($routeName === 'admin_bar') {
                return 'bar';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);

        $providerData = $urlsProvider->getAdminTestableUrlsProviderData();

        $this->assertSame('admin_bar', $providerData[0][0]);
        $this->assertSame('bar', $providerData[0][1]);
        $this->assertSame(200, $providerData[0][2]);
        $this->assertTrue($providerData[0][3]);
    }

    public function testGetFrontTestableUrlsProviderDataStatusCode()
    {
        $routeCollection = [
            'front_foo' => new Route('foo'),
            'front_delete' => new Route('foo/delete/'),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName) {
            if ($routeName === 'front_foo') {
                return 'foo';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);
        $reflectionClass = new ReflectionClass(UrlsProvider::class);
        $reflectionProperty = $reflectionClass->getProperty('expectedStatusCodesByRouteName');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($urlsProvider, [
            'front_foo' => 302,
        ]);

        $providerData = $urlsProvider->getFrontTestableUrlsProviderData();

        $this->assertSame(302, $providerData[0][2]);
        $this->assertSame(302, $providerData[1][2]);
    }

    public function testGetAdminTestableUrlsProviderDataStatusCode()
    {
        $routeCollection = [
            'admin_bar' => new Route('bar'),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName) {
            if ($routeName === 'admin_bar') {
                return 'bar';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);
        $reflectionClass = new ReflectionClass(UrlsProvider::class);
        $reflectionProperty = $reflectionClass->getProperty('expectedStatusCodesByRouteName');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($urlsProvider, [
            'admin_bar' => 302,
        ]);

        $providerData = $urlsProvider->getAdminTestableUrlsProviderData();

        $this->assertSame(302, $providerData[0][2]);
    }

    public function testGetTestableRoutes()
    {
        $routeCollection = [
            'front_testable' => new Route('testable'),
            'front_ignored' => new Route('ignored'),
            'front_underscore' => new Route('_underscore'),
            'front_post' => new Route('post', [], [], [], '', [], ['POST']),
            'admin_testable' => new Route('admin/testable'),
            'admin_ignored' => new Route('admin/ignored'),
            'admin_underscore' => new Route('admin/_underscore'),
            'admin_post' => new Route('admin/post', [], [], [], '', [], ['POST']),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName) {
            switch ($routeName) {
                case 'front_testable':
                    return 'testable';
                case 'admin_testable':
                    return 'admin/testable';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);

        $reflectionClass = new ReflectionClass(UrlsProvider::class);
        $reflectionProperty = $reflectionClass->getProperty('ignoredRouteNames');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($urlsProvider, [
            'front_ignored',
            'admin_ignored',
        ]);

        $frontProviderData = $urlsProvider->getFrontTestableUrlsProviderData();

        $this->assertSame('front_testable', $frontProviderData[0][0]);
        $this->assertSame('testable', $frontProviderData[0][1]);

        $adminProviderData = $urlsProvider->getAdminTestableUrlsProviderData();
        $this->assertSame('admin_testable', $adminProviderData[0][0]);
        $this->assertSame('admin/testable', $adminProviderData[0][1]);
    }

    public function testGetRouteParameters()
    {
        $routeCollection = [
            'front_foo' => new Route('foo/{barId}/{id}/{bazId}', ['barId' => 3]),
        ];

        $routerMock = $this->getMockBuilder(CurrentDomainRouter::class)
            ->setMethods(['getRouteCollection', 'generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $routerMock->expects($this->atLeastOnce())->method('getRouteCollection')->willReturn($routeCollection);
        $routerMock->expects($this->atLeastOnce())->method('generate')->willReturnCallback(function ($routeName, $parameters) {
            if ($routeName === 'foo') {
                $this->assertSame(1, $parameters['id']);
                $this->assertSame(1, $parameters['bazId']);
                return 'foo';
            }
        });

        $urlsProvider = $this->createUrlsProvider($routerMock);
        $urlsProvider->getFrontTestableUrlsProviderData();
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Router\CurrentDomainRouter $router
     * @return \Tests\ShopBundle\Performance\UrlsProvider
     */
    private function createUrlsProvider(CurrentDomainRouter $router)
    {
        $persistentReferenceFacade = $this->getContainer()->get('shopsys.shop.component.data_fixture.persistent_reference_facade');
        $tokenManagerMock = $this->getMockForAbstractClass(CsrfTokenManagerInterface::class);
        $routeCsrfProtector = $this->getMock(RouteCsrfProtector::class, [], [], '', false);
        $domainMock = $this->getMock(Domain::class, [], [], '', false);

        return new UrlsProvider($persistentReferenceFacade, $router, $tokenManagerMock, $routeCsrfProtector, $domainMock);
    }
}
