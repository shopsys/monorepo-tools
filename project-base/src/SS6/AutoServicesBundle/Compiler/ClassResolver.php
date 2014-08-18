<?php

namespace SS6\AutoServicesBundle\Compiler;

class ClassResolver {

	/**
	 * @param string $className
	 * @return bool
	 */
	public function canBeResolved($className) {
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
}
