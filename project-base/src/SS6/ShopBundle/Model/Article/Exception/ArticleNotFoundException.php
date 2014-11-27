<?php

namespace SS6\ShopBundle\Model\Article\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleNotFoundException extends NotFoundHttpException implements ArticleException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Article not found by criteria ' . Debug::export($criteria), $previous, 0);
	}

}
