<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Component\HttpFoundation\Request;

class HttpSmokeTest extends HttpSmokeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        self::$kernel->getContainer()->get('shopsys.shop.component.domain')
            ->switchDomainById(1);
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    protected function customizeRouteConfigs(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomization = new RouteConfigCustomization(self::$kernel->getContainer());
        $routeConfigCustomization->customizeRouteConfigs($routeConfigCustomizer);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleRequest(Request $request)
    {
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $entityManager->beginTransaction();
        $response = parent::handleRequest($request);
        $entityManager->rollback();

        return $response;
    }
}
