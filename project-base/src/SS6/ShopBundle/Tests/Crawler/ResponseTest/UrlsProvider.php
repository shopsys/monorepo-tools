<?php

namespace SS6\ShopBundle\Tests\Crawler\ResponseTest;

use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture as BaseUnitDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\UnitDataFixture as DemoUnitDataFixture;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UrlsProvider {

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService
	 */
	private $persistentReferenceService;

	/**
	 * @var \SS6\ShopBundle\Component\Router\CurrentDomainRouter
	 */
	private $router;

	/**
	 * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
	 */
	private $tokenManager;

	/**
	 * @var \SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector
	 */
	private $routeCsrfProtector;

	/**
	 * @var string[]
	 */
	private $ignoredRouteNames = [
		// protected by csrf token
		'admin_customer_loginasuser',
		// used only for internal setting of selected domain by tab control in admin
		'admin_domain_selectdomain',
		// do not rewrite XML feed by test products
		'admin_feed_generate',
		// used by firewall to catch login requests
		// http://symfony.com/doc/current/reference/configuration/security.html#check-path
		'admin_login_check',
		// currently, we cannot test AJAX requests
		'admin_order_addproduct',
		// when tests are processed, there are no images in the shop
		'front_image',
		// when tests are processed, there are no images in the shop
		'front_image_without_type',
		// used by firewall to catch login requests
		// http://symfony.com/doc/current/reference/configuration/security.html#check-path
		'front_login_check',
		// currently, we cannot test AJAX requests
		'admin_domain_edit',
		// currently, we cannot test AJAX requests
		'admin_article_saveordering',
		// in TEST environment is different security configuration
		'admin_logout',
	];

	/**
	 * @var string[]
	 */
	private $frontAsLoggedRouteNames = [
		'front_customer_edit',
		'front_customer_orders',
		'front_customer_order_detail_registered',
	];

	/**
	 * @var int[]
	 */
	private $expectedStatusCodesByRouteName = [
		'admin_login' => 302,
		'admin_login_sso' => 302,
		'front_customer_login_as_remembered_user' => 302,
		'front_logout' => 302,
		'front_order_index' => 302,
		'front_order_sent' => 302,
		'front_promo_code_remove' => 302,
		'front_registration_set_new_password' => 302,
	];

	/**
	 * @param \Symfony\Component\Routing\Route $route
	 * @param string $routeName
	 * @return array
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	private function getRouteParameters(Route $route, $routeName) {
		switch ($routeName) {
			case 'admin_category_edit':
				// category ID 1 is special root category, therefore we use ID 2
				return ['id' => 2];

			case 'admin_bestsellingproduct_detail':
				// category ID 1 is special root category, therefore we use ID 2
				return ['categoryId' => 2, 'domainId' => 1];

			case 'front_logout':
				return ['_csrf_token' => '{frontend_logout}'];

			case 'admin_superadmin_icondetail':
				return ['icon' => 'delete'];

			case 'admin_pricinggroup_delete':
				return ['id' => $this->persistentReferenceService->getReference(PricingGroupDataFixture::PARTNER_DOMAIN_1)->getId()];

			case 'admin_unit_delete':
				return [
					'id' => $this->persistentReferenceService->getReference(BaseUnitDataFixture::PCS)->getId(),
					'newId' => $this->persistentReferenceService->getReference(DemoUnitDataFixture::M3)->getId(),
				];

			case 'admin_vat_delete':
				return ['id' => $this->persistentReferenceService->getReference(VatDataFixture::VAT_SECOND_LOW)->getId()];

			case 'front_article_detail':
				return ['id' => 1];

			case 'front_customer_order_detail_unregistered':
				return ['urlHash' => $this->persistentReferenceService->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getUrlHash()];

			case 'front_customer_order_detail_registered':
				return ['orderNumber' => $this->persistentReferenceService->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getNumber()];

			case 'front_error_page':
			case 'front_error_page_format':
				return ['code' => 404, '_format' => 'html'];

			case 'front_product_detail':
				return ['id' => 1];

			case 'front_product_list':
				return ['id' => 2];

			case 'front_registration_set_new_password':
				return ['email' => 'no-reply@netdevelo.cz', 'hash' => 'invalidHash'];

			default:
				$parameters = [];
				foreach ($this->getRouteParametersNames($route) as $parameterName) {
					if (!$route->hasDefault($parameterName) && preg_match('/(^id|.+Id)$/', $parameterName)) {
						$parameters[$parameterName] = 1;
					}
				}
				return $parameters;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @param \Symfony\Component\Routing\RouterInterface $router
	 * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager
	 * @param \SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector $routeCsrfProtector
	 */
	public function __construct(
		PersistentReferenceService $persistentReferenceService,
		CurrentDomainRouter $router,
		CsrfTokenManagerInterface $tokenManager,
		RouteCsrfProtector $routeCsrfProtector
	) {
		$this->persistentReferenceService = $persistentReferenceService;
		$this->router = $router;
		$this->tokenManager = $tokenManager;
		$this->routeCsrfProtector = $routeCsrfProtector;
	}

	/**
	 * @return array
	 */
	public function getAdminTestableUrlsProviderData() {
		$urls = [];
		foreach ($this->router->getRouteCollection() as $routeName => $route) {
			if ($this->isTestableRoute($route, $routeName) && $this->isAdminRouteName($routeName)) {
				$routeParameters = $this->getRouteParameters($route, $routeName);
				$routeParameters = $this->addRouteCsrfParameter($routeName, $routeParameters);
				$urls[] = [
					$routeName,
					$this->router->generate($routeName, $routeParameters, RouterInterface::RELATIVE_PATH),
					$this->getExpectedStatusCode($route, $routeName),
					true,
				];
			}
		}

		return $urls;
	}

	/**
	 * @return array
	 */
	public function getFrontTestableUrlsProviderData() {
		$urls = [];
		foreach ($this->router->getRouteCollection() as $routeName => $route) {
			if ($this->isTestableRoute($route, $routeName) && $this->isFrontRouteName($routeName)) {
				$routeParameters = $this->getRouteParameters($route, $routeName);
				$routeParameters = $this->addRouteCsrfParameter($routeName, $routeParameters);
				$urls[] = [
					$routeName,
					$this->router->generate($routeName, $routeParameters, RouterInterface::RELATIVE_PATH),
					$this->getExpectedStatusCode($route, $routeName),
					in_array($routeName, $this->frontAsLoggedRouteNames),
				];
			}
		}

		return $urls;
	}

	/**
	 * @param string $routeName
	 * @param array $routeParameters
	 * @return array
	 */
	private function addRouteCsrfParameter($routeName, $routeParameters) {
		if (preg_match('@_delete$@', $routeName)) {
			$routeParameters[RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER] =
				'{'
				. $this->routeCsrfProtector->getCsrfTokenId($routeName)
				. '}';
		}

		return $routeParameters;
	}

	/**
	 * Each url creates new client with clean TokenManager.
	 * Csrf token must be generated for the new client before request is created.
	 *
	 * @param string $url
	 * @return string
	 */
	public function replaceCsrfTokensInUrl($url) {
		return preg_replace_callback(
			'@\%7B([^%]+)\%7D@',
			function ($matches) {
				return $this->tokenManager->getToken($matches[1])->getValue();
			},
			$url
		);
	}

	/**
	 * @param \Symfony\Component\Routing\Route $route
	 * @return string[]
	 */
	private function getRouteParametersNames(Route $route) {
		$matches = [];
		preg_match_all('/\{([^\}]+)\}/', $route->getPath(), $matches);

		return $matches[1];
	}

	/**
	 * @param string $routeName
	 * @return bool
	 */
	private function isAdminRouteName($routeName) {
		return strpos($routeName, 'admin_') === 0;
	}

	/**
	 * @param string $routeName
	 * @return string
	 */
	private function isFrontRouteName($routeName) {
		return strpos($routeName, 'front_') === 0;
	}

	/**
	 * @param \Symfony\Component\Routing\Route $route
	 * @param string $routeName
	 * @return bool
	 */
	private function isTestableRoute(Route $route, $routeName) {
		if (in_array($routeName, $this->ignoredRouteNames)
			|| count($route->getMethods()) > 0 && !in_array('GET', $route->getMethods())
			|| strpos($route->getPath(), '/_') === 0
			|| strpos($route->getPath(), '/admin/_') === 0
		) {
			return false;
		}

		return true;
	}

	/**
	 * @param \Symfony\Component\Routing\Route $route
	 * @param string $routeName
	 * @return int
	 */
	private function getExpectedStatusCode(Route $route, $routeName) {
		if (array_key_exists($routeName, $this->expectedStatusCodesByRouteName)) {
			return $this->expectedStatusCodesByRouteName[$routeName];
		} elseif (strpos($route->getPath(), '/delete/') !== false) {
			return 302;
		}

		return 200;
	}

}
