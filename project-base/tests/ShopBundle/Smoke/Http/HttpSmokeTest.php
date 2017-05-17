<?php

namespace Tests\ShopBundle\Smoke\Http;

use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Shopsys\ShopBundle\Controller\Front\ProductController;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture as BaseUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\UnitDataFixture as DemoUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixture;
use Symfony\Component\HttpFoundation\Request;

class HttpSmokeTest extends HttpSmokeTestCase
{
    const DEFAULT_ID_VALUE = 1;
    const CSRF_TOKEN_LOGOUT_NAME = '_csrf_token';

    const IGNORED_ROUTE_NAMES = [
        // protected by csrf token
        'admin_customer_loginasuser',
        // used only for internal setting of selected domain by tab control in admin
        'admin_domain_selectdomain',
        // do not rewrite XML feed by test products
        'admin_feed_generate',
        // used by firewall to catch login requests
        // http://symfony.com/doc/current/reference/configuration/security.html#check-path
        'admin_login_check',
        // when tests are processed, there are no images in the shop
        'front_image',
        // when tests are processed, there are no images in the shop
        'front_image_without_type',
        // used by firewall to catch login requests
        // http://symfony.com/doc/current/reference/configuration/security.html#check-path
        'front_login_check',
        // in TEST environment is different security configuration
        'admin_logout',
        // temporarily not tested until it will be optimized at US-1517
        'admin_unit_delete',
    ];

    const FRONT_AS_LOGGED_ROUTE_NAMES = [
        'front_customer_edit',
        'front_customer_orders',
    ];

    const EXPECT_REDIRECT_ROUTE_NAMES = [
        'admin_login',
        'admin_login_sso',
        'front_customer_login_as_remembered_user',
        'front_logout',
        'front_order_index',
        'front_order_sent',
        'front_promo_code_remove',
    ];

    protected function setUp()
    {
        parent::setUp();

        self::$kernel->getContainer()->get('shopsys.shop.component.domain')
            ->switchDomainById(1);
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigsBuilder $routeConfigsBuilder
     */
    protected function customizeRouteConfigs(RouteConfigsBuilder $routeConfigsBuilder)
    {
        $routeConfigsBuilder
            ->customize(function (RouteConfig $config) {
                if (!$config->isMethodAllowed('GET')
                    || strpos($config->getRoutePath(), '/_') === 0
                    || strpos($config->getRoutePath(), '/admin/_') === 0
                    || $config->getRouteCondition() === 'request.isXmlHttpRequest()'
                    || in_array($config->getRouteName(), self::IGNORED_ROUTE_NAMES, true)
                    || !preg_match('~^(admin|front)_~', $config->getRouteName())
                ) {
                    $config->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_domain_list' && $this->isSingleDomain()) {
                    $config->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('~^admin_~', $config->getRouteName())) {
                    $config->setCredentials('superadmin', 'admin123');
                }
            })
            ->customize(function (RouteConfig $config) {
                if (in_array($config->getRouteName(), self::FRONT_AS_LOGGED_ROUTE_NAMES, true)) {
                    $config->setCredentials('no-reply@netdevelo.cz', 'user123');
                }
            })
            ->customize(function (RouteConfig $config) {
                foreach ($config->getRoutePathParameters() as $name) {
                    if ($config->isParameterRequired($name) && preg_match('~^(id|.+Id)$~', $name)) {
                        $config->setParameter($name, self::DEFAULT_ID_VALUE);
                    }
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('~/delete/~', $config->getRoutePath())
                    || in_array($config->getRouteName(), self::EXPECT_REDIRECT_ROUTE_NAMES, true)
                ) {
                    $config->expectStatusCode(302);
                }
            })
            ->customize(function (RouteConfig $config) {
                switch ($config->getRouteName()) {
                    case 'admin_administrator_edit':
                        // admin ID 1 is reserved for superadmin, which is not editable
                        $config->expectStatusCode(404);
                        $config->addTestCase()
                            ->setParameter('id', 2)
                            ->expectStatusCode(200);
                        break;
                    case 'admin_category_edit':
                        // category ID 1 is special root category, cannot be edited
                        $config->expectStatusCode(404);
                        $config->addTestCase()
                            ->setParameter('id', 2)
                            ->expectStatusCode(200);
                        break;
                    case 'admin_bestsellingproduct_detail':
                        // category ID 1 is special root category, therefore we use ID 2
                        $config->setParameter('categoryId', 2);
                        $config->setParameter('domainId', 1);
                        break;
                    case 'front_logout':
                        $config->setParameter(self::CSRF_TOKEN_LOGOUT_NAME, 'frontend_logout');
                        break;
                    case 'admin_superadmin_icondetail':
                        $config->setParameter('icon', 'delete');
                        break;
                    case 'admin_pricinggroup_delete':
                        $pricingGroup = $this->getPersistentReference(PricingGroupDataFixture::PRICING_GROUP_PARTNER_DOMAIN_1);
                        /** @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */
                        $config->setParameter('id', $pricingGroup->getId());
                        break;
                    case 'admin_unit_delete':
                        $unit = $this->getPersistentReference(BaseUnitDataFixture::UNIT_PIECES);
                        /** @var $unit \Shopsys\ShopBundle\Model\Product\Unit\Unit */
                        $newUnit = $this->getPersistentReference(DemoUnitDataFixture::UNIT_CUBIC_METERS);
                        /** @var $newUnit \Shopsys\ShopBundle\Model\Product\Unit\Unit */
                        $config->setParameter('id', $unit->getId());
                        $config->setParameter('newId', $newUnit->getId());
                        break;
                    case 'admin_vat_delete':
                        $vat = $this->getPersistentReference(VatDataFixture::VAT_SECOND_LOW);
                        /** @var $vat \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
                        $newVat = $this->getPersistentReference(VatDataFixture::VAT_LOW);
                        /** @var $newVat \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
                        $config->setParameter('id', $vat->getId());
                        $config->setParameter('newId', $newVat->getId());
                        break;
                    case 'front_article_detail':
                        $config->setParameter('id', 1);
                        break;
                    case 'front_brand_detail':
                        $config->setParameter('id', 1);
                        break;
                    case 'front_customer_order_detail_unregistered':
                        $order = $this->getPersistentReference(OrderDataFixture::ORDER_PREFIX . '1');
                        /** @var $order \Shopsys\ShopBundle\Model\Order\Order */
                        $config->setParameter('urlHash', $order->getUrlHash());
                        break;
                    case 'front_customer_order_detail_registered':
                        $order = $this->getPersistentReference(OrderDataFixture::ORDER_PREFIX . '1');
                        /** @var $order \Shopsys\ShopBundle\Model\Order\Order */
                        $config->setCredentials('no-reply@netdevelo.cz', 'user123');
                        $config->setParameter('orderNumber', $order->getNumber());
                        break;
                    case 'front_error_page':
                    case 'front_error_page_format':
                        $config->setParameter('code', 404);
                        $config->setParameter('_format', 'html');
                        break;
                    case 'front_product_detail':
                        $config->setParameter('id', 1);
                        // getMainVariantDetailRouteData
                        $config->addTestCase()
                            ->setParameter('id', 150);
                        break;
                    case 'front_product_list':
                        $config->setParameter('id', 2);
                        // getProductListInCategoryWith500ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 8);
                        // getProductListWithFilteringInCategoryWith500ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 8)
                            ->setParameter('product_filter_form', [
                                'inStock' => '1',
                                'parameters' => [
                                    41 => [58],
                                ],
                            ]);
                        // getProductListInCategoryWith7600ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 3);
                        // getProductListWithFilteringInCategoryWith7600ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 3)
                            ->setParameter('product_filter_form', [
                                'minimalPrice' => '100',
                                'inStock' => '1',
                                'parameters' => [
                                    1 => ['1'],
                                ],
                            ]);
                        // getProductListInCategoryWith13600ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 11);
                        // getProductWithListFilteringInCategoryWith13600ProductsRouteData
                        $config->addTestCase()
                            ->setParameter('id', 11)
                            ->setParameter('product_filter_form', [
                                'minimalPrice' => '100',
                                'inStock' => '1',
                            ]);
                        break;
                    case 'front_product_search':
                        // getSearchFilteringRouteData
                        $config->addTestCase()
                            ->setParameter(ProductController::SEARCH_TEXT_PARAMETER, 'a')
                            ->setParameter('product_filter_form', [
                                'inStock' => '1',
                                'flags' => ['2'],
                                'brands' => ['2', '19'],
                            ]);
                        break;
                    case 'front_registration_set_new_password':
                        $customer = $this->getPersistentReference(UserDataFixture::USER_WITH_RESET_PASSWORD_HASH);
                        /** @var $customer \Shopsys\ShopBundle\Model\Customer\User */
                        $config->setParameter('email', $customer->getEmail());
                        $config->setParameter('hash', $customer->getResetPasswordHash());
                        $config->addTestCase()
                            ->setParameter('hash', 'invalidHash')
                            ->expectStatusCode(302);
                        break;
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('@_delete$@', $config->getRouteName())) {
                    $routeCsrfProtector = self::$kernel->getContainer()
                        ->get('shopsys.shop.router.security.route_csrf_protector');
                    /* @var $routeCsrfProtector \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector */

                    $tokenId = $routeCsrfProtector->getCsrfTokenId($config->getRouteName());

                    $config->setParameter(RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER, $tokenId);
                }
            });
    }

    /**
     * @param string $name
     * @return object
     */
    private function getPersistentReference($name)
    {
        $persistentReferenceFacade = self::$kernel->getContainer()
            ->get('shopsys.shop.component.data_fixture.persistent_reference_facade');
        /* @var $persistentReferenceFacade \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade*/

        return $persistentReferenceFacade->getReference($name);
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(TestCaseConfig $config)
    {
        $parameters = $config->getParameters();
        foreach ($parameters as $name => $value) {
            if (in_array($name, [RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER, self::CSRF_TOKEN_LOGOUT_NAME], true)) {
                $csrfTokenManager = self::$kernel->getContainer()->get('security.csrf.token_manager');
                /* @var $csrfTokenManager \Symfony\Component\Security\Csrf\CsrfTokenManager */

                $config->setParameter($name, $csrfTokenManager->getToken($value)->getValue());
            }
        }

        return parent::createRequest($config);
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

    /**
     * @return bool
     */
    private function isSingleDomain()
    {
        $domain = self::$kernel->getContainer()->get('shopsys.shop.component.domain');
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        return count($domain->getAll()) === 1;
    }
}
