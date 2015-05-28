<?php

/**
 * Copy-pasted from Symfony\Component\Validator\Validation
 * and added custom ValidatorBuilder.
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SS6\ShopBundle\Component\Validator;

/**
 * Entry point for the Validator component.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ValidatorBuilderFactory {

	/**
	 * @return \Symfony\Component\Validator\ValidatorInterface The new validator.
	 */
	public static function createValidator() {
		return self::createValidatorBuilder()->getValidator();
	}

	/**
	 * @return \Symfony\Component\Validator\ValidatorBuilderInterface The new builder.
	 */
	public static function createValidatorBuilder() {
		return new ValidatorBuilder();
	}

	/**
	 * This class cannot be instantiated.
	 */
	private function __construct() {

	}

}
