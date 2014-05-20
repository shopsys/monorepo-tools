<?php

namespace SS6\ShopBundle\Model\Article\Exception;

use Exception;

class ArticleNotFoundException extends Exception implements ArticleException {
	
	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Article not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
