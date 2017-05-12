<?php

namespace Tests\ShopBundle\Smoke\Http;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Tests\ShopBundle\Smoke\Http\HttpSmokeTestCase;

class AllRoutesResponseTest extends HttpSmokeTestCase
{
    public function adminTestableUrlsProvider()
    {
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */
        // DataProvider is called before setUp() - domain is not set
        $domain->switchDomainById(1);

        return $this->createUrlsProvider()->getAdminTestableUrlsProviderData();
    }

    /**
     * @param string $testedRouteName Used for easier debugging
     * @param string $url
     * @param int $expectedStatusCode
     * @dataProvider adminTestableUrlsProvider
     */
    public function testAdminPages($testedRouteName, $url, $expectedStatusCode)
    {
        $url = $this->createUrlsProvider()->replaceCsrfTokensInUrl($url);

        $client = $this->getClient(false, 'superadmin', 'admin123');

        $this->makeRequestInTransaction($client, $url);

        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertRouteStatusCode($expectedStatusCode, $statusCode, $testedRouteName);
    }

    public function frontTestableUrlsProvider()
    {
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */
        // DataProvider is called before setUp() - domain is not set
        $domain->switchDomainById(1);

        return $this->createUrlsProvider()->getFrontTestableUrlsProviderData();
    }

    /**
     * @param string $testedRouteName Used for easier debugging
     * @param string $url
     * @param int $expectedStatusCode
     * @param bool $asLogged
     * @dataProvider frontTestableUrlsProvider
     */
    public function testFrontPages($testedRouteName, $url, $expectedStatusCode, $asLogged)
    {
        $url = $this->createUrlsProvider()->replaceCsrfTokensInUrl($url);

        if ($asLogged) {
            $client = $this->getClient(false, 'no-reply@netdevelo.cz', 'user123');
        } else {
            $client = $this->getClient();
        }

        $this->makeRequestInTransaction($client, $url);

        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertRouteStatusCode($expectedStatusCode, $statusCode, $testedRouteName);
    }

    /**
     * @param int $expectedStatusCode
     * @param int $statusCode
     * @param string $testedRouteName
     */
    private function assertRouteStatusCode($expectedStatusCode, $statusCode, $testedRouteName)
    {
        $this->assertSame(
            $expectedStatusCode,
            $statusCode,
            sprintf(
                'Failed asserting that status code %d for route "%s" is identical to expected %d',
                $statusCode,
                $testedRouteName,
                $expectedStatusCode
            )
        );
    }
}
