<?php

namespace Shopsys\AutoServicesBundle\Compiler;

class ServiceHelper {

	/**
	 * @param string $className
	 * @return bool
	 */
	public function canBeService($className) {
		return class_exists($className);
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public function convertClassNameToServiceId($className) {
		$id = preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], $className);
		$id = strtr($id, '\\', '.');
		return strtolower($id);
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	public function isServiceId($id) {
		return preg_match('/^[a-z\d\._]+$/', $id) > 0;
	}
}
