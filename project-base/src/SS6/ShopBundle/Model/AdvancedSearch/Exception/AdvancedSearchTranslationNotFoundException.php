<?php

namespace SS6\ShopBundle\Model\AdvancedSearch\Exception;

use Exception;
use SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchException;

class AdvancedSearchTranslationNotFoundException extends Exception implements AdvancedSearchException {

	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
