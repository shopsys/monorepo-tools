<?php

namespace Shopsys\ShopBundle\Component;

use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader as SensioAnnotatedRouteControllerLoader;

/**
 * AnnotatedRouteControllerLoader
 */
class AnnotatedRouteControllerLoader extends SensioAnnotatedRouteControllerLoader {
	/**
	 * Makes the default route name shorter by removing some obvious parts.
	 *
	 * @param \ReflectionClass $class
	 * @param \ReflectionMethod $method
	 *
	 * @return string The default route name
	 */
	protected function getDefaultRouteName(\ReflectionClass $class, \ReflectionMethod $method) {
		$routeName = parent::getDefaultRouteName($class, $method);

		return preg_replace('/^ss6_shop_/', '', $routeName);
	}
}
