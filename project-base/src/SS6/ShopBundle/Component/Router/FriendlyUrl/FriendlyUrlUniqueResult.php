<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;

class FriendlyUrlUniqueResult {

	/**
	 * @var bool
	 */
	private $unique;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl
	 */
	private $friendlyUrlForPersist;

	/**
	 * @param bool $unique
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null $friendlyUrl
	 */
	public function __construct($unique, FriendlyUrl $friendlyUrl = null) {
		$this->unique = $unique;
		$this->friendlyUrlForPersist = $friendlyUrl;
	}

	/**
	 * @return bool
	 */
	public function isUnique() {
		return $this->unique;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
	 */
	public function getFriendlyUrlForPersist() {
		return $this->friendlyUrlForPersist;
	}

}
