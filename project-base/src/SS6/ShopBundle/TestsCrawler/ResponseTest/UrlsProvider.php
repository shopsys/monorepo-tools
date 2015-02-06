<?php

namespace SS6\ShopBundle\TestsCrawler\ResponseTest;

use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class UrlsProvider {

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService
	 */
	private $persistentReferenceService;

	/**
	 * @var \Symfony\Component\Routing\RouterInterface
	 */
	private $router;

	/**
	 * @var string[]
	 */
	private $ignoredRouteNames = [
		'admin_domain_selectdomain',
		'admin_login_check',
		'front_image',
		'front_image_without_type',
		'front_login_check',
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
		'front_order_index' => 302,
		'front_order_sent' => 302,
		'admin_login' => 302,
		'admin_logout' => 302,
		'front_logout' => 302,
	];

	/**
	 * @param \Symfony\Component\Routing\Route $route
	 * @param string $routeName
	 * @return array
	 */
	private function getRouteParameters(Route $route, $routeName) {
		switch ($routeName) {
			case 'admin_superadmin_icondetail':
				return ['icon' => 'delete'];

			case 'admin_pricinggroup_delete':
				return ['id' => $this->persistentReferenceService->getReference(PricingGroupDataFixture::PARTNER_DOMAIN_1)->getId()];

			case 'admin_vat_delete':
				return ['id' => $this->persistentReferenceService->getReference(VatDataFixture::VAT_SECOND_LOW)->getId()];

			case 'front_customer_order_detail_unregistered':
				return ['urlHash' => $this->persistentReferenceService->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getUrlHash()];

			case 'front_customer_order_detail_registered':
				return ['orderNumber' => $this->persistentReferenceService->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getNumber()];

			case 'front_error_page':
			case 'front_error_page_format':
				return ['code' => 404, '_format' => 'html'];

			case 'front_registration_set_new_password':
				return ['email' => 'no-reply@netdevelo.cz', 'hash' => 'test'];

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
	 */
	public function __construct(PersistentReferenceService $persistentReferenceService, RouterInterface $router) {
		$this->persistentReferenceService = $persistentReferenceService;
		$this->router = $router;
	}

	/**
	 * @return array
	 */
	public function getAdminTestableUrlsProviderData() {
		$urls = [];
		foreach ($this->router->getRouteCollection() as $routeName => $route) {
			if ($this->isTestableRoute($route, $routeName) && $this->isAdminRouteName($routeName)) {
				$urls[] = [
					$routeName,
					$this->router->generate($routeName, $this->getRouteParameters($route, $routeName), RouterInterface::RELATIVE_PATH),
					$this->getExpectedStatusCode($route, $routeName),
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
				$urls[] = [
					$routeName,
					$this->router->generate($routeName, $this->getRouteParameters($route, $routeName), RouterInterface::RELATIVE_PATH),
					$this->getExpectedStatusCode($route, $routeName),
					in_array($routeName, $this->frontAsLoggedRouteNames),
				];
			}
		}

		return $urls;
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
