<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Feed\Exception\FeedException;

class FeedConfigNotFoundException extends Exception implements FeedException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
