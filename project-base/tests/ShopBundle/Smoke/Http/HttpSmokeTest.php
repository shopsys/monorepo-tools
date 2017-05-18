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
        $this->filterRoutesForTesting($routeConfigCustomizer);
        $this->configureGeneralRules($routeConfigCustomizer);
        $this->configureAdminRoutes($routeConfigCustomizer);
        $this->configureFrontendRoutes($routeConfigCustomizer);
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    public function filterRoutesForTesting(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomizer
            ->customize(function (RouteConfig $config) {
                if (!$config->isHttpMethodAllowed('GET')) {
                    $config->addNote('Only routes supporting GET method are tested.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('~^(/admin)?/_~', $config->getRoutePath())) {
                    $config->addNote('Internal routes (prefixed with "/_") are not tested.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteCondition() === 'request.isXmlHttpRequest()') {
                    $config->addNote('AJAX-only routes are not tested.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if (!preg_match('~^(admin|front)_~', $config->getRouteName())) {
                    $config->addNote('Only routes for front-end and administration are tested.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if (in_array($config->getRouteName(), ['admin_login_check', 'front_login_check'], true)) {
                    $config->addNote('Used by firewall to catch login requests.')
                        ->addNote('http://symfony.com/doc/current/reference/configuration/security.html#check-path')
                        ->ignore();
                }
            })->customize(function (RouteConfig $config) {
                if (in_array($config->getRouteName(), ['front_image', 'front_image_without_type'], true)) {
                    $config->addNote('There are no images in the shop when the tests are processed.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_domain_selectdomain') {
                    $config->addNote('Used only for internal setting of selected domain by tab control in admin.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_feed_generate') {
                    $config->addNote('Do not rewrite XML feed by test products.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_logout') {
                    $config->addNote('There is different security configuration in TEST environment.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_unit_delete') {
                    $config->addNote('temporarily not tested until it will be optimized in US-1517.')
                        ->ignore();
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_domain_list' && $this->isSingleDomain()) {
                    $config->addNote('Domain list in administration is not available when only 1 domain exists.')
                        ->ignore();
                }
            });
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    public function configureGeneralRules(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomizer
            ->customize(function (RouteConfig $config) {
                foreach ($config->getRouteParameterNames() as $name) {
                    if ($config->isRouteParameterRequired($name) && preg_match('~^(id|.+Id)$~', $name)) {
                        $note = 'Route requires ID parameter "%s" in its path, try using %d by default.';
                        $config->changeDefaultTestCase(sprintf($note, $name, self::DEFAULT_ID_VALUE))
                            ->setParameter($name, self::DEFAULT_ID_VALUE);
                    }
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('~_delete$~', $config->getRouteName())) {
                    $config->changeDefaultTestCase('Expect redirect by 302 for any delete action.')
                        ->expectStatusCode(302)
                        ->delayCustomizationUntilTestExecution(function (TestCaseConfig $config) {
                            $routeCsrfProtector = self::$kernel->getContainer()
                                ->get('shopsys.shop.router.security.route_csrf_protector');
                            /* @var $routeCsrfProtector \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector */
                            $csrfTokenManager = self::$kernel->getContainer()->get('security.csrf.token_manager');
                            /* @var $csrfTokenManager \Symfony\Component\Security\Csrf\CsrfTokenManager */

                            $tokenId = $routeCsrfProtector->getCsrfTokenId($config->getRouteName());
                            $token = $csrfTokenManager->getToken($tokenId);

                            $config->addNote('Add CSRF token for any delete action (protected by RouteCsrfProtector).')
                                ->setParameter(RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER, $token->getValue());
                        });
                }
            });
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    public function configureAdminRoutes(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomizer
            ->customize(function (RouteConfig $config) {
                if (preg_match('~^admin_~', $config->getRouteName())) {
                    $config->changeDefaultTestCase('Log as "admin" to administration.')
                        ->setCredentials('admin', 'admin123');
                }
            })
            ->customize(function (RouteConfig $config) {
                if (preg_match('~^admin_(superadmin_|translation_list$)~', $config->getRouteName())) {
                    $config->changeDefaultTestCase('Only superadmin should be able to see this route.')
                        ->expectStatusCode(404);
                    $config->addTestCase('Should be OK when logged in as "superadmin".')
                        ->setCredentials('superadmin', 'admin123')
                        ->expectStatusCode(200);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_login') {
                    $config->changeDefaultTestCase('Admin login should redirect by 302.')
                        ->expectStatusCode(302);
                    $config->addTestCase('Admin login should not redirect for users that are not logged in yet.')
                        ->setCredentials(null, null)
                        ->expectStatusCode(200);
                }
            })
            ->customize(function (RouteConfig $config) {
                $routeNames = ['admin_login_sso', 'admin_customer_loginasuser'];
                if (in_array($config->getRouteName(), $routeNames, true)) {
                    $config->changeDefaultTestCase(sprintf('Route "%s" should always just redirect.', $config->getRouteName()))
                        ->expectStatusCode(302);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_administrator_edit') {
                    $config->changeDefaultTestCase('It is forbidden to edit administrator with ID 1 as it is the superadmin.')
                        ->expectStatusCode(404);
                    $config->addTestCase('Editing normal administrator should be OK.')
                        ->setParameter('id', 2)
                        ->expectStatusCode(200);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_category_edit') {
                    $config->changeDefaultTestCase('It is forbidden to edit category with ID 1 as it is the root.')
                        ->expectStatusCode(404);
                    $config->addTestCase('Editing normal category should be OK.')
                        ->setParameter('id', 2)
                        ->expectStatusCode(200);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_bestsellingproduct_detail') {
                    $config->changeDefaultTestCase('Category with ID 1 is the root, use ID 2 instead.')
                        ->setParameter('categoryId', 2);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_pricinggroup_delete') {
                    $pricingGroup = $this->getPersistentReference(PricingGroupDataFixture::PRICING_GROUP_PARTNER_DOMAIN_1);
                    /** @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */
                    $config->changeDefaultTestCase(sprintf('Delete pricing group "%s".', $pricingGroup->getName()))
                        ->setParameter('id', $pricingGroup->getId());
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_unit_delete') {
                    $unit = $this->getPersistentReference(BaseUnitDataFixture::UNIT_PIECES);
                    /** @var $unit \Shopsys\ShopBundle\Model\Product\Unit\Unit */
                    $newUnit = $this->getPersistentReference(DemoUnitDataFixture::UNIT_CUBIC_METERS);
                    /** @var $newUnit \Shopsys\ShopBundle\Model\Product\Unit\Unit */
                    $config->changeDefaultTestCase(sprintf('Delete unit "%s" and replace it by "%s".', $unit->getName('en'), $newUnit->getName('en')))
                        ->setParameter('id', $unit->getId())
                        ->setParameter('newId', $newUnit->getId());
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'admin_vat_delete') {
                    $vat = $this->getPersistentReference(VatDataFixture::VAT_SECOND_LOW);
                    /** @var $vat \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
                    $newVat = $this->getPersistentReference(VatDataFixture::VAT_LOW);
                    /** @var $newVat \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
                    $config->changeDefaultTestCase(sprintf('Delete VAT "%s" and replace it by "%s".', $vat->getName(), $newVat->getName()))
                        ->setParameter('id', $vat->getId())
                        ->setParameter('newId', $newVat->getId());
                }
            });
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer $routeConfigCustomizer
     */
    public function configureFrontendRoutes(RouteConfigCustomizer $routeConfigCustomizer)
    {
        $routeConfigCustomizer
            ->customize(function (RouteConfig $config) {
                if (in_array($config->getRouteName(), ['front_customer_edit', 'front_customer_orders'], true)) {
                    $config->changeDefaultTestCase('Log as demo user "Jaromír Jágr" on pages in client section.')
                        ->setCredentials('no-reply@netdevelo.cz', 'user123');
                }
            })
            ->customize(function (RouteConfig $config) {
                $routeNames = ['front_customer_login_as_remembered_user', 'front_promo_code_remove'];
                if (in_array($config->getRouteName(), $routeNames, true)) {
                    $config->changeDefaultTestCase(sprintf('Route "%s" should always just redirect.', $config->getRouteName()))
                        ->expectStatusCode(302);
                }
            })
            ->customize(function (RouteConfig $config) {
                if (in_array($config->getRouteName(), ['front_order_index', 'front_order_sent'], true)) {
                    $config->changeDefaultTestCase('Order page should redirect by 302 as the cart is empty by default.')
                        ->expectStatusCode(302);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_logout') {
                    $config->delayCustomizationUntilTestExecution(function (TestCaseConfig $config) {
                        $csrfTokenManager = self::$kernel->getContainer()->get('security.csrf.token_manager');
                        /* @var $csrfTokenManager \Symfony\Component\Security\Csrf\CsrfTokenManager */

                        $token = $csrfTokenManager->getToken('frontend_logout');

                        $config->addNote('Add CSRF token for logout action (configured in app/security.yml).')
                            ->setParameter('_csrf_token', $token->getValue());
                    });
                    $config->changeDefaultTestCase('Logout action should redirect by 302')
                        ->expectStatusCode(302);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_article_detail') {
                    $config->changeDefaultTestCase('Use ID 1 as default article.')
                        ->setParameter('id', 1);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_brand_detail') {
                    $config->changeDefaultTestCase('Use ID 1 as default brand.')
                        ->setParameter('id', 1);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_customer_order_detail_unregistered') {
                    $order = $this->getPersistentReference(OrderDataFixture::ORDER_PREFIX . '1');
                    /** @var $order \Shopsys\ShopBundle\Model\Order\Order */
                    $config->changeDefaultTestCase(sprintf('Use hash of order n. %s for unregistered access.', $order->getNumber()))
                        ->setParameter('urlHash', $order->getUrlHash());
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_customer_order_detail_registered') {
                    $order = $this->getPersistentReference(OrderDataFixture::ORDER_PREFIX . '1');
                    /** @var $order \Shopsys\ShopBundle\Model\Order\Order */
                    $config->changeDefaultTestCase(sprintf('Log as demo user "Jaromír Jágr" on front-end to access order n. %s.', $order->getNumber()))
                        ->setCredentials('no-reply@netdevelo.cz', 'user123')
                        ->setParameter('orderNumber', $order->getNumber());
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_product_detail') {
                    $config->changeDefaultTestCase('Use ID 1 as default product.')
                        ->setParameter('id', 1);
                    $config->addTestCase('See detail of a product that is main variant')
                        ->setParameter('id', 150);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_product_list') {
                    $config->changeDefaultTestCase('Use ID 2 as default category (ID 1 is the root).')
                        ->setParameter('id', 2);
                    $config->addTestCase('See category that has 500 products in performance data')
                        ->setParameter('id', 8);
                    $config->addTestCase('See and filter category that has 500 products in performance data')
                        ->setParameter('id', 8)
                        ->setParameter('product_filter_form', [
                            'inStock' => '1',
                            'parameters' => [
                                41 => [58],
                            ],
                        ]);
                    $config->addTestCase('See category that has 7600 products in performance data')
                        ->setParameter('id', 3);
                    $config->addTestCase('See and filter category that has 7600 products in performance data')
                        ->setParameter('id', 3)
                        ->setParameter('product_filter_form', [
                            'minimalPrice' => '100',
                            'inStock' => '1',
                            'parameters' => [
                                1 => ['1'],
                            ],
                        ]);
                    $config->addTestCase('See category that has 3600 products in performance data')
                        ->setParameter('id', 11);
                    $config->addTestCase('See and filter category that has 3600 products in performance data')
                        ->setParameter('id', 11)
                        ->setParameter('product_filter_form', [
                            'minimalPrice' => '100',
                            'inStock' => '1',
                        ]);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_product_search') {
                    $config->addTestCase('Search for "a" and filter the results')
                        ->setParameter(ProductController::SEARCH_TEXT_PARAMETER, 'a')
                        ->setParameter('product_filter_form', [
                            'inStock' => '1',
                            'flags' => ['2'],
                            'brands' => ['2', '19'],
                        ]);
                }
            })
            ->customize(function (RouteConfig $config) {
                if ($config->getRouteName() === 'front_registration_set_new_password') {
                    $customer = $this->getPersistentReference(UserDataFixture::USER_WITH_RESET_PASSWORD_HASH);
                    /** @var $customer \Shopsys\ShopBundle\Model\Customer\User */
                    $config->changeDefaultTestCase('See new password page for customer with reset password hash.')
                        ->setParameter('email', $customer->getEmail())
                        ->setParameter('hash', $customer->getResetPasswordHash());
                    $config->addTestCase('Expect redirect when the hash is invalid.')
                        ->setParameter('hash', 'invalidHash')
                        ->expectStatusCode(302);
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
