<?php

namespace SS6\ShopBundle\Form;

class UrlListData {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public $toDelete;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[domainId]
	 */
	public $mainOnDomains;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public $newUrls;

	public function __construct() {
		$this->toDelete = [];
		$this->mainOnDomains = [];
		$this->newUrls = [];
	}
}
