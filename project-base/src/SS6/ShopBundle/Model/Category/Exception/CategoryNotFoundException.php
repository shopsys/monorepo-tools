<?php

namespace SS6\ShopBundle\Model\Category\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use SS6\ShopBundle\Model\Category\Exception\CategoryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryNotFoundException extends NotFoundHttpException implements CategoryException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Category not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
