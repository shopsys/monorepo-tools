<?php

namespace Tests\ShopBundle\Crawler\ResponseTest;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Tests\ShopBundle\Crawler\ResponseTest\UrlsProvider;
use Tests\ShopBundle\Test\CrawlerTestCase;

class AllPagesResponseTest extends CrawlerTestCase
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

        $this->getClient(false, 'superadmin', 'admin123')->request('GET', $url);

        $statusCode = $this->getClient()->getResponse()->getStatusCode();

        $this->assertSame(
            $expectedStatusCode,
            $statusCode,
            sprintf(
                'Failed asserting that status code %d for route "%s" is identical to expected %d',
                $testedRouteName,
                $statusCode,
                $expectedStatusCode
            )
        );
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
            $this->getClient(false, 'no-reply@netdevelo.cz', 'user123')->request('GET', $url);
        } else {
            $this->getClient()->request('GET', $url);
        }

        $statusCode = $this->getClient()->getResponse()->getStatusCode();

        $this->assertSame(
            $expectedStatusCode,
            $statusCode,
            sprintf(
                'Failed asserting that status code %d for route "%s" is identical to expected %d',
                $testedRouteName,
                $statusCode,
                $expectedStatusCode
            )
        );
    }
}
